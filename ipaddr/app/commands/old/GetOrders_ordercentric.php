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
		echo "GlobalLogoID,IPTotal,CDR,MRC,OrderIds\n"; 
		//we don't need to exclude the existing L3_ON_IPV4ALLOC_FLAT orders here because they
		//have a different InterfaceTypeInt anyway
		$orders = DB::table('dbo.v_sdscorecard as sd')
				->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->where('ProvOrderStatus','Completed')
				->where(function($query) {
					$query->where('OrderCreatedDt','>=','9/1/15')->orWhere('ood.Term',1);
				})
				->where(function($query) {
					$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>=',DB::raw('getdate()'));
				}) 
				->select(['sd.OrderId','CustomerName','GlobalLogoID'])
				->orderBy('GlobalLogoID')
				->get();
		$holder = '';
		$runningTotal = 0;
		
		$orderIdsDisp = []; //for display
		foreach($orders as $order) {
			$orderId = $order->OrderId;
			
			if ($holder != $order->GlobalLogoID) {
				if ($runningTotal > 8) {
					//redo this query without the restricting time/term condition
					$totalCDR = DB::table('dbo.v_sdscorecard as sd')
						->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
						->where('ProvOrderStatus','Completed')
						->where(function($query) {
							$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>=',DB::raw('getdate()'));
						})
						->where('GlobalLogoID',$holder)
						->sum('CDR');
					$mrc = GetOrders::getMRC($runningTotal, $totalCDR);
					echo $holder . ',' . $runningTotal . ',' . $totalCDR . ',' . $mrc . ',' . implode(',', $orderIdsDisp) . "\n"; 
				}
				$runningTotal = 0;
				$totalCDR = 0;
				$orderIdsDisp = [];
			}

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
			
			$runningTotal += $totalIps;
			
			//add the CDR to the total CDR regardless of the IPv4 usage
				
			$holder = $order->GlobalLogoID;
			array_push($orderIdsDisp, $orderId);
		}

		//do the last piece just in case
		if ($runningTotal > 8) {
			echo $holder . ',' . $runningTotal . ',' . $totalCDR . "\n"; 
		}
	}
	
	public static function getMRC($ipCount, $CDR) {
		
		if ($CDR == 0) {
			return .30 * $ipCount; //premium
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
