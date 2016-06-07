<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class GetFirstSet extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'getFirstSet';

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

			$firstDt = '';
			$firstOrder = '';
			$lastOrder = '';
			$lastDt = '';
			
			//do stuff
			$renewDates = [];
			$goLive = new Carbon('2016-07-01');
			foreach($ordersAssoc as $order) {
				$carbon = new Carbon($order->BillStartDt);
				$cutOff = new Carbon('2015-09-01');
				
				//$diff = $carbon->gt($cutOff);
				while ($carbon->lt($cutOff)) {
					$carbon->addMonths($order->NoOfMonths);
				}
				array_push($renewDates, $carbon);
			}
			$max = max($renewDates);
			if ($max->lt($goLive)) {
				echo $customer . ',' . $max->format('Y-m-d') . ',' . $mrc . "\n";
			}
			
//			if ($orderCount == 1) {
//				$lastDt = $firstDt;
//				$lastOrder = $firstOrder;
//			}
			//echo "$customer,$orderCount,$firstOrder,$firstDt,$lastOrder,$lastDt\n";
			
		}
		
		
	}
	
	
}
