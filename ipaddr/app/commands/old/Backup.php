<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrdersFirstLast
 *
 * @author tcassidy
 */
class GetOrdersFirstLast extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'getOrdersFirstLast';

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
		$excludeSet = Config::get('constants.excludeSet');
		$customers = DB::table('v_sdscorecard as sd')->whereIn('OrderId',function($query) {
				$query->from('Starfish.AdminSF.ip_block')->select('OrderId')->distinct();

			})
			->whereNotIn('GlobalLogoID',$excludeSet)
			->select('GlobalLogoID')->distinct()->get();

		foreach($customers as $customer) {
			$customer = $customer['GlobalLogoID'];

			//get all orders that are port or lag, completed, not cancelled, 
			$orders = DB::table('dbo.v_sdscorecard as sd')
				->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
				->leftJoin('mjain.OPM_Order_Term as t','ood.Term','=','t.TermCode')	
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->where('GlobalLogoID', $customer)
				->where('ProvOrderStatus','Completed')
				->whereNull('OrderCancelDt')
				->where('ood.Term','>',0)
				->select(['sd.OrderId',DB::raw('cast(sd.BillStartDt as date) as BillStartDt'),'t.NoOfMonths','t.TermDesc','InterfaceTypeInt','ProductCode','ood.AutoRenew'])
				->get();
			if (count($orders) == 0) {
				continue;
			}		

			$customerTotal = 0;
			//$orderIdsDisp = []; //for display
			$addArr = [];
			$orderArrMulti = [];
			$goLive = new Carbon('2016-07-01');
			$cutOff = new Carbon('2015-09-01');
			$renewDates = [];
			foreach($orders as $order) {
				$orderId = $order['OrderId'];
				$orderArr = ['OrderId' => $orderId, 
							'GlobalLogoID' => $customer, 'ProductCode' => $order['ProductCode'], 
							'BillStartDt' => $order['BillStartDt'], 'Term' => $order['TermDesc'], 'AutoRenew' => $order['AutoRenew']];
				
				//date section
				$carbon = new Carbon($order['BillStartDt']);
				if ($carbon->gt($cutOff)) {
					//if the order started after 9/1/15 it's good to go, set it to the go live date
					$carbon = $goLive;
				} else if ($order['NoOfMonths'] == 1) {
					//for monthly order just set it = goLive
					$carbon = $goLive;
				} else if ($order['AutoRenew'] == 0) {
					//if it's autorenew = no, treat it as monthly after the first term
					$carbon->addMonths($order['NoOfMonths']);
					if($carbon->lt($goLive)) {
						$carbon = $goLive;
					}
				} else {
					while ($carbon->lt($goLive)) {
						$carbon->addMonths($order['NoOfMonths']);
					}
				}
				array_push($renewDates, $carbon);
				$orderArr['RenewalDt'] = $carbon->format('Y-m-d');
				
				//ip section
				$mult = 1;
				if ($order['InterfaceTypeInt'] == 40) {
					$mult = DB::table('dbo.v_sdscorecard as sd')
							->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
							->where('ParentOrderId',$orderId)->whereIn('InterfaceTypeInt',[13, 14, 33, 42, 45])->count();
				}
				$totalIps = 0;
				$numIps = 0;
				$ips = DB::table('Starfish.AdminSF.ip_block')
					->where('Version',4)
					->where('OrderId', $orderId)
					->select(['count','OrderId',DB::raw("dbo.V6Convert(netaddr,netmask) as Address")])->get();
				foreach($ips as $ip) {
					$numIps++;
					$count = $ip['count'];
					$totalIps += $count;
					array_push($addArr,['GlobalLogoID' => $customer, 'OrderId' => $orderId, 'Address' => $ip['Address']]);
				}
				if ($totalIps > 0) {
					$orderArr['RawCount'] = $totalIps;
					$orderArr['NumIPBlocks'] = $numIps;
					$discountIPs = $totalIps - (8 * $mult);
					if ($discountIPs > 0) {
						$customerTotal += $discountIPs;
						$orderArr['DiscountCount'] = $discountIPs;
					}
					else {
						$orderArr['DiscountCount'] = 0;
					}
					array_push($orderArrMulti, $orderArr);
				}
				
			}
			$min = min($renewDates);
			$max = max($renewDates);
			$emailReq = clone $min;
			$emailReq->subDays(60);
			
			if ($customerTotal > 0) {
				
				//get the absolute total for the customer
				$allocCount = DB::table('Starfish.AdminSF.ip_block')->whereIn('orderid',function($query) use ($customer) {
					$query->from('TLG.dbo.v_sdscorecard')
						->select('OrderId')
						->where('GlobalLogoID',$customer)
						->where('ProvOrderStatus','Completed')
						->where('InterfaceTypeInt',47)	
						->whereNull('OrderCancelDt');
				})->where('version',4)->sum('count');
				$custIpCount = $customerTotal + intval($allocCount);
				
				
				//redo this query without the restricting time/term condition
				$totalCDR = DB::table('dbo.v_sdscorecard as sd')
					->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
					->where('Layer',3)
					->where('ProvOrderStatus','Completed')
					->whereNull('OrderCancelDt')
					->where('GlobalLogoID',$customer)
					->sum('CDR');
				$mrc = GetOrdersFirstLast::getMRC($customerTotal, $custIpCount, $totalCDR);
				DB::table('BillingSystem.dbo.IPv4Billing')
					->insert(
					[	'GlobalLogoID' => $customer, 
						'IPCount' => $customerTotal, 
						'CustIPCount' => $custIpCount,
						'FirstBillDt' => $min->format('Y-m-d'),
						'LastBillDt' => $max->format('Y-m-d'),
						'LatestPosEmailDt' => $emailReq->format('Y-m-d'),
						'MRC' => $mrc,
						'TotalCDR' => intval($totalCDR)
					]);
				DB::table('BillingSystem.dbo.IPv4BillingToAddresses')
					->insert(
					$addArr);
				DB::table('BillingSystem.dbo.IPv4BillingToOldOrder')
					->insert(
					$orderArrMulti);
				echo "$customer\n"; 
			}
			
		}
		
	}
	
	
	public static function getMRC($ipCount, $custIpCount, $CDR) {
		
		if ($CDR == 0) {
			if ($custIpCount <= 256) {
				return 75; //higher flat for 0 CDR customer
			} else {
				return .30 * $ipCount; //premium
			}
		}
		else if ($custIpCount <= 256) {
			return 50; //flat
		}
		else if ($custIpCount <= 512) { //23
			if ($CDR > 100) {
				return .20 * $ipCount; //standard
			}
			else {
				return .30 * $ipCount; //premium
			}
		}
		else if ($custIpCount <= 1024) { //22
			if ($CDR >= 1000) {
				return .20 * $ipCount; //standard
			}
			else {
				return .30 * $ipCount; //premium
			}
		}
		else if ($custIpCount <= 2048) { //21
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
