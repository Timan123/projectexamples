<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrdersFirstLast
 *
 * @author tcassidy
 */
class QuiltDateCheck extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'quiltDateCheck';

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
		$customers = DB::table('BillingSystem.dbo.IPv4Billing')->select(['GlobalLogoID','CustomerName','EffLastBillDt'])->where('Quilt',1)->get();
		$goLive = new Carbon('2016-07-01');
		$cutOff = new Carbon('2015-09-01');
		
		foreach($customers as $customer) {
			$gid = $customer['GlobalLogoID'];
			//get all orders that are port or lag, completed, not cancelled, 
			$orders = DB::table('dbo.v_sdscorecard as sd')
				->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
				->leftJoin('mjain.OPM_Order_Term as t','ood.Term','=','t.TermCode')	
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->whereIn('Layer',[3,4])
				->where('GlobalLogoID', $gid)
				->where('ProvOrderStatus','Completed')
				->whereNull('OrderCancelDt')
				->where('ood.Term','>',0)
				->select(['sd.OrderId',DB::raw('cast(sd.BillStartDt as date) as BillStartDt'),'t.NoOfMonths','t.TermDesc','InterfaceTypeInt','ProductCode','ood.AutoRenew'])
				->get();

			$renewDates = [];
			foreach($orders as $order) {
				$orderId = $order['OrderId'];
				
				//date section
				$carbon = new Carbon($order['BillStartDt']);
				if ($order['NoOfMonths'] == 1) {
					$carbon = $goLive;
				} else if ($order['AutoRenew'] == 0) {
					//if it's autorenew = no, treat it as monthly after the first term
					$carbon->addMonths($order['NoOfMonths']);
					if ($carbon->lt($goLive)) {
						$carbon = $goLive;
					}
				} else {
					while ($carbon->lt($goLive)) {
						$carbon->addMonths($order['NoOfMonths']);
					}
				}
				//echo $customer . "\t" . $carbon->format('Y-m-d') . "\n";
				
				
				array_push($renewDates, $carbon);
				
			
			}
			$max = max($renewDates);
			DB::table('BillingSystem.dbo.Ipv4Billing')
				->where('GlobalLogoId',$gid)
				->update([	'EffLastBillDt' => $max->format('Y-m-d')]);
			//echo $gid . ',' . str_replace(',','',$customer['CustomerName']) . ',' . $customer['EffLastBillDt'] . ',' .  $max->format('Y-m-d') . "\n";
			

		}
		


		
	}
	
}
