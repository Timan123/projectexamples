<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class GetDiff extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'getDiff';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'IP Allocation';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{ 
	
		$bigArray = Config::get('constants.alllocCustMRC');
		
		foreach($bigArray as $data) {
			$customer = $data['customer'];
			$mrc = $data['mrc'];
			$ordersAssoc = DB::table('dbo.v_sdscorecard as sd')
					->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
					->leftJoin('mjain.OPM_Order_Term as t','ood.Term','=','t.TermCode')
					->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
					->where('ProvOrderStatus','Completed')
					->where('GlobalLogoID', $customer)
					->where(function($query) {
						//uncancelled orders
						$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>=',DB::raw('getdate()')); 
					})->select(['sd.OrderId',DB::raw('cast(sd.BillStartDt as date) as BillStartDt'),'t.NoOfMonths'])->get();
			if (count($ordersAssoc) == 0) {
				continue;
			}		
			$orders = [];
			$firstDt = '';
			$firstOrder = '';
			$lastOrder = '';
			$lastDt = '';
			foreach($ordersAssoc as $order) {
				array_push($orders,$order->OrderId);
			}
			$orderCount = count($orders);
			//check for monthly
			$monthlyBool = false;
			$monthly = DB::table('dbo.v_sdscorecard as sd')
				->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
				->leftJoin('mjain.OPM_Order_Term as t','ood.Term','=','t.TermCode')	
				->where(function($query) {
					//first 2 conditions, newer order or monthly order
					$query->orWhere('ood.Term',1);
					$query->orWhere(function($query2) {
						//orders that reached their initial term and have billingicb = Non MAC price change are MtM, eligible
						$query2->whereRaw('dateadd(m,t.NoOfMonths,sd.BillStartDt) < getdate()')->where('ood.ICBBilling',3);
					});
				})
				->whereIn('sd.OrderId',$orders)
				->select(['sd.OrderId','t.TermDesc'])->get();
			if (count($monthly) > 0) {
				$monthlyBool = true;
				//$firstDt = '2016-07-01';
				//$firstOrder = $monthly[0]->OrderId;
			}
			
			//do stuff
			$renewDates = [];
			foreach($ordersAssoc as $order) {
				$carbon = new Carbon($order->BillStartDt);
				$cutOff = new Carbon('2015-09-01');
				$goLive = new Carbon('2016-07-01');
				//$diff = $carbon->gt($cutOff);
				while ($carbon->lt($cutOff)) {
					$carbon->addMonths($order->NoOfMonths);
				}
				if ($carbon->lt($goLive)) {
					$carbon = $goLive;
				}
				array_push($renewDates, $carbon);
				//echo $customer . ',' . $order->OrderId . ',' . $order->BillStartDt . ',' . $order->NoOfMonths . ',' . $carbon->format('Y-m-d') . "\n";
			}
			$max = max($renewDates);
			$min = min($renewDates);
			if ($monthlyBool) {
				$min = new Carbon('2016-07-01');
			}
			$diff = $max->diffInMonths($min);
			$revenue = $diff * $mrc;
			echo $customer . ',' . $min->format('Y-m-d') . ',' . $max->format('Y-m-d') . ',' . $diff . ',' . $mrc . ',' . $revenue . "\n";
			
//			if ($orderCount == 1) {
//				$lastDt = $firstDt;
//				$lastOrder = $firstOrder;
//			}
			//echo "$customer,$orderCount,$firstOrder,$firstDt,$lastOrder,$lastDt\n";
			
		}
		
		
	}
	
	
}
