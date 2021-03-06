<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class GetOrdersLast extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'getOrdersLast';

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
		//look in app/config/constants.php
		$hugeSet = Config::get('constants.hugeSet');
		
		$results2 = DB::table('dbo.v_sdscorecard as sd')
				->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
				->leftJoin('mjain.OPM_Order_Term as t','ood.Term','=','t.TermCode')
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->where('ProvOrderStatus','Completed')
				->whereNotIn('GlobalLogoID',$hugeSet)
				->where(function($query) {
					//first 2 conditions, newer order or monthly order
					$query->where('OrderCreatedDt','>=','9/1/15')->orWhere('ood.Term',1);
					//check up to 9 terms out
					for ($i = 1; $i <= 9; $i++) {
						$query->orWhereRaw("dateadd(m,t.NoOfMonths*" . $i . ",sd.BillStartDt) between getdate() and '7/1/19'");
					}
					$query->orWhere(function($query2) {
						//orders that reached their initial term and have billingicb = Non MAC price change are MtM, eligible
						$query2->whereRaw('dateadd(m,t.NoOfMonths,sd.BillStartDt) < getdate()')->where('ood.ICBBilling',3);
					});
				})
				->where(function($query) {
					//uncancelled orders
					$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>=',DB::raw('getdate()')); 
				}) 
				->select('GlobalLogoID')->distinct()->get();

		//do for each customer in the loop
		foreach($results2 as $customer) {
			$customer = $customer->GlobalLogoID;
			echo $customer . "\n";
			//get all orders that are port or lag, completed, not cancelled, 
			$orderIds = DB::table('dbo.v_sdscorecard as sd')
				->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
				->leftJoin('mjain.OPM_Order_Term as t','ood.Term','=','t.TermCode')	
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->where('GlobalLogoID', $customer)
				->where('ProvOrderStatus','Completed')
				->whereNull('OrderCancelDt')
				->where('ood.Term','>',0)
				->select(['sd.OrderId',DB::raw('cast(sd.BillStartDt as date) as BillStartDt'),'t.NoOfMonths'])
				->get();
			if (count($orderIds) == 0) {
				continue;
			}		
			$goLive = new Carbon('2016-07-01');
			$renewDates = [];
			foreach($orderIds as $order) {
				$carbon = new Carbon($order->BillStartDt);
				$cutOff = new Carbon('2015-09-01');
				
				//$diff = $carbon->gt($cutOff);
				while ($carbon->lt($cutOff)) {
					$carbon->addMonths($order->NoOfMonths);
				}
				array_push($renewDates, $carbon);
			}
			$max = max($renewDates);
			if ($max->gt($goLive)) {
				continue;
			}

			$customerTotal = 0;
			//$orderIdsDisp = []; //for display
			foreach($orderIds as $orderId) {
				$orderId = $orderId->OrderId;
				$ips = DB::table('Starfish.AdminSF.ip_block')
					->where('Version',4)
					->where('frozen','no')	
					->where('OrderId', $orderId)
					//->where('Netmask','>=',115) handling the exclusion of super jumbo blocks on the customer level
					->select('count')->get();
				$free4 = false;
				$totalIps = 0;
				foreach($ips as $ip) {
					$count = $ip->count;
					if (!$free4 && $count == 4) {
						$free4 = true;
					}
					$totalIps += $count;
				}
				if ($free4) {
					$totalIps -= 4;
				} else {
					$totalIps -= 8;
				}
				$customerTotal += $totalIps;
				//array_push($orderIdsDisp, $orderId);
			}
			if ($customerTotal > 0) {
				//redo this query without the restricting time/term condition
				$totalCDR = DB::table('dbo.v_sdscorecard as sd')
					->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
					->where('ProvOrderStatus','Completed')
					->where(function($query) {
						$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>=',DB::raw('getdate()'));
					})
					->where('GlobalLogoID',$customer)
					->sum('CDR');
				$mrc = GetOrders::getMRC($customerTotal, $totalCDR);
				DB::connection('dev')->table('IPv4Billing')
					->insert(
					[	'GlobalLogoID' => $customer, 
						'IPCount' => $customerTotal, 
						'LastBillDt' => $max->format('Y-m-d'),
						'MRC' => $mrc,
						'TotalCDR' => intval($totalCDR)
					]);
				//echo $customer . ',' . $customerTotal . ',' . intval($totalCDR) . ',' . $mrc . ',' . "\n"; 
			}
			
		}
		
		
	}
	
	
	public static function getMRC($ipCount, $CDR) {
		
		if ($CDR == 0) {
			if ($ipCount <= 256) {
				return 75; //higher flat for 0 CDR customer
			} else {
				return .30 * $ipCount; //premium
			}
		}
		else if ($ipCount <= 256) {
			return 50; //flat
		}
		else if ($ipCount <= 512) { //23
			if ($CDR > 100) {
				return .20 * $ipCount; //standard
			}
			else {
				return .30 * $ipCount; //premium
			}
		}
		else if ($ipCount <= 1024) { //22
			if ($CDR >= 1000) {
				return .20 * $ipCount; //standard
			}
			else {
				return .30 * $ipCount; //premium
			}
		}
		else if ($ipCount <= 2048) { //21
			if ($CDR >= 10000) {
				return .20 * $ipCount; //standard
			}
			else {
				return .30 * $ipCount; //premium
			}
		}
		else { //if over /21, use premium pricing and ignore CDR
			return .30 * $ipCount;
		}
	}
	
}
