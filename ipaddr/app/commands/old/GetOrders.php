<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class GetOrders extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'getOrders';

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
	
		
		$results2 = DB::table('dbo.v_sdscorecard as sd')
				->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
				->leftJoin('mjain.OPM_Order_Term as t','ood.Term','=','t.TermCode')
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->where('ProvOrderStatus','Completed')
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
		
			//get all orders that are port or lag, completed, not cancelled, 
			$orderIds = DB::table('dbo.v_sdscorecard')
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->where('GlobalLogoID', $customer)
				->where('ProvOrderStatus','Completed')
				->whereNull('OrderCancelDt')
				->select(['OrderId'])
				->get();
			$customerTotal = 0;
			$orderIdsDisp = []; //for display
			foreach($orderIds as $orderId) {
				$orderId = $orderId->OrderId;
				$ips = DB::table('Starfish.AdminSF.ip_block')
					->where('Version',4)
					->where('OrderId', $orderId)
					->where('Netmask','>=',115) //exclude the super jumbo blocks
					->select('count')->get();
				$free8 = false;
				$free4 = false;
				$totalIps = 0;
				foreach($ips as $ip) {
					$count = $ip->count;
					if (!$free8 && $count == 8) {
						$free8 = true;
					}
					if (!$free4 && $count == 4) {
						$free4 = true;
					}
					$totalIps += $count;
				}
				if ($free8) {
					$totalIps -= 8;
				} else if ($free4) {
					$totalIps -= 4;
				}
				$customerTotal += $totalIps;
				array_push($orderIdsDisp, $orderId);
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
				echo $customer . ',' . $customerTotal . ',' . $totalCDR . ',' . $mrc . ',' . implode(',', $orderIdsDisp) . "\n"; 
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
