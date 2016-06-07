<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrdersFirstLast
 *
 * @author tcassidy
 */
class OldLegacyCheck extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'oldLegacyCheck';

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
		$customers = DB::table('BillingSystem.dbo.IPv4Billing')->select(['GlobalLogoID','CreatedOrder','CustomerName','MRC','TotalCDR'])->where('PosLegacyAllocSmall',1)->get();
		
		//$base = 'https://ipaddr-dev.sys.cogentco.com/api/getIPInfo/';
		foreach($customers as $customer) {
			$gid = $customer['GlobalLogoID'];
			$myOrder = '';
			if ($customer['CreatedOrder']) {
				$myOrder = $customer['CreatedOrder'];
			}
//			$url = $base . $gid;
			$arr = OldLegacyCheck::getIPInfo($gid);
			$ipSum = $arr['ipsum'];
			//echo "$ipSum\n";
			$theoryMRC = GetOrdersFirstLast::getMRC($ipSum, $ipSum, $customer['TotalCDR']);
			
			$outstandingMRC = DB::table('v_sdscorecard')->where('interfacetypeint',47)->whereNull('OrderCancelDt')->where('GlobalLogoID',$gid)->where('OrderId','!=',$myOrder)->sum('MRR');
			$totalMRC = $outstandingMRC + $customer['MRC'];
			
			echo $gid . ',' . str_replace(',','',$customer['CustomerName']) . ',' . $theoryMRC . ',' .  $outstandingMRC . ',' . $totalMRC . "\n";
			

		}
		
	}
	
	public static function getIPInfo($gid) {
		DB::connection('dev')->statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		
		$blocks = DB::connection('dev')->table('dca-05.TLG.dbo.v_sdscorecard as sd')
				->join('Starfish.adminsf.ip_block_sync as i','sd.OrderId','=','i.orderid')
				->where(function($query) {
					//uncancelled orders
					$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>=',DB::raw('getdate()')); 
				}) 
				->where('ProvOrderStatus','Completed')
				->where('sd.GlobalLogoId',$gid)
				->where('i.version',4)
				->select([	'sd.orderid',
							'sd.ProductCode as pcode',
							DB::raw("cast(sd.ordercanceldt as date) as ordercanceldt"),
							'sd.interfacetypeint',
							DB::raw("cast(i.count as int) as count"),
							'i.port_order',
							DB::raw("dbo.V6Convert(netaddr,netmask) as block")
						])->get();
		$numAllocs = count($blocks);
		$status = 'success';
		if ($numAllocs == 0) {
			$status = 'no blocks';
		}
		
		$blocks = array_map(function($block) { 
					$block['autoipv4order'] = false;
					$check = DB::table('BillingSystem.dbo.IPv4Billing')->where('CreatedOrder',$block['orderid'])->first();
					if ($check) {
						$block['autoipv4order'] = true;
					}
					return $block;
				}, $blocks);
		
		$rawSum = array_reduce($blocks, function($carry, $block) {
				return $carry += $block['count'];
		});
		$notation = '/' . strval(32 - (log($rawSum) / log(2)));
		
		
		$ports = [];
		$lags = [];
		$portTypes = [13, 14, 33, 42, 45];
		foreach($blocks as $block) {
			if (in_array($block['interfacetypeint'], $portTypes)) {
				array_push($ports, $block['orderid']);
			} 
			else if ($block['interfacetypeint'] == 40) {
				array_push($lags, $block['orderid']);
			}
			if ($block['port_order']) {
				array_push($ports, $block['port_order']);
			}
		}
		$ports = array_values(array_unique($ports));
		$lags = array_values(array_unique($lags));
		
		$numPorts = count($ports);
		if ($lags) {
			$childPortsTot = array_reduce($lags, function($carry, $lag) {
				$childPorts = DB::table('dbo.v_sdscorecard as sd')
							->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
							->where('ParentOrderId',$lag)->whereIn('InterfaceTypeInt',[13, 14, 33, 42, 45])->count();
				$carry += $childPorts;
				return $carry;
			});
			$numPorts += $childPortsTot;
		}
		

		DB::connection('dev')->statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		$ret = ['data' => $blocks];
		$ret['ok'] = true;
		$ret['msg'] = ['status' => $status];
		$ret['ipv4cnt'] = $numAllocs;
		$ret['ports'] = $ports;
		$ret['lags'] = $lags;
		$footer = ['block' => 'Total IPv4'];
		$footer['ccount'] = $rawSum;
		$footer['notation'] = $notation;
		$ret['footer'] = $footer;
		$ret['numports'] = $numPorts;
		$ret['discountsize'] = 8 * $numPorts;
		$chargeSum = $rawSum - (8 * $numPorts);
		if ($chargeSum < 0) {
			$chargeSum = 0;
		}
		$ret['ipsum'] = $chargeSum;

		return $ret;
				
		
		
	}
	
}
