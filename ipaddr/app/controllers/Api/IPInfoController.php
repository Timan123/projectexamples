<?php

namespace Api;


use DB;
use Carbon;
use Input;
use Log;
use Config;

/**
 * Description of InvoiceController
 *
 * @author tcassidy
 */
class IPInfoController extends BaseController {
	
	public function getIPInfo($gid) {
		
		
		
		
		$subIP = Input::get('subIP');
		$addIP = Input::get('addIP');
		$subCDR = Input::get('subCDR');
		$addCDR = Input::get('addCDR');
		
		
		$server = Config::get('server.server');
		
		
		$blocks = DB::connection('prodsfip')->table('TLG.dbo.v_sdscorecard as sd')
				->join('Starfish.adminsf.ip_block as i','sd.OrderId','=','i.orderid')
				->where(function($query) {
					//uncancelled orders
					$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>',DB::raw('getdate()')); 
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
							'i.descr',
							'sd.ProvOrderStatus',
							DB::raw("dbo.V6Convert(netaddr,netmask) as block")
						])->get();
		$numAllocs = count($blocks);
		$status = 'success';
		if ($numAllocs == 0) {
			$status = 'no blocks';
		}
		
		$totalCDR = DB::table('TLG.dbo.v_sdscorecard as sd')
			->whereIn('InterfaceTypeInt',[6, 12, 10, 8, 13, 14, 33, 40, 42, 45])
			->whereIn('Layer',[3,4])
			->where('ProvOrderStatus','Completed')
			->where(function($query) {
					//uncancelled orders
				$query->whereNull('OrderCancelDt')->orWhere('OrderCancelDt','>',DB::raw('getdate()')); 
			}) 
			->where('GlobalLogoID',$gid)
			->sum('CDR');
			
		$totalCDR = intval($totalCDR);	
		
		$project = false;
		$check = DB::table('BillingSystem.dbo.IPv4Billing')->where('GlobalLogoID',$gid)->first();
		if ($check) {
			$project = true;
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
		
		if ($subIP && is_numeric($subIP)) {
			$rawSum -= $subIP;
		}
		if ($addIP && is_numeric($addIP)) {
			$rawSum += $addIP;
		}
		if ($subCDR && is_numeric($subCDR)) {
			$totalCDR -= $subCDR;
		}
		if ($addCDR && is_numeric($addCDR)) {
			$totalCDR += $addCDR;
		}
		if ($rawSum < 0) {
			$rawSum = 0;
		}
		if ($totalCDR < 0) {
			$totalCDR = 0;
		}
		$notation = '/' . strval(32 - (log($rawSum) / log(2)));
		
		$ports = [];
		$lags = [];
		$portTypes = [6, 12, 10, 8, 13, 14, 33, 42, 45];
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
			$childPortsTot = array_reduce($lags, function($carry, $lag) use ($portTypes) {
				$childPorts = DB::table('TLG.dbo.v_sdscorecard as sd')
							->leftJoin('mjain.OPM_Order_Details as ood','sd.OrderId','=','ood.OrderId')
							->where('ParentOrderId',$lag)->whereIn('InterfaceTypeInt',$portTypes)->count();
				$carry += $childPorts;
				return $carry;
			});
			$numPorts += $childPortsTot;
		}
		

		
		$ret = ['data' => $blocks];
		$ret['ok'] = true;
		$ret['msg'] = ['status' => $status];
		$ret['ipv4cnt'] = $numAllocs;
		$ret['ports'] = $ports;
		$ret['totalCDR'] = $totalCDR;
		$ret['lags'] = $lags;
		$footer = ['block' => 'Total IPv4'];
		$footer['ccount'] = $rawSum;
		$footer['notation'] = $notation;
		$ret['footer'] = $footer;
		$ret['numports'] = $numPorts;
		$ret['discountsize'] = 8 * $numPorts;
		$ret['project'] = $project;
		
		
		$chargeSum = $rawSum - (8 * $numPorts);
		if ($chargeSum < 0) {
			$chargeSum = 0;
		}
		$lookup = \ChargeRules::getMRC($chargeSum, $chargeSum, $totalCDR);
		$mrc = $lookup['mrc'];
		$level = $lookup['level'];
		$ret['ipsum'] = $chargeSum;
		$ret['mrc'] = $mrc;
		$ret['level'] = $lookup['level'];
		$ret['icb'] = $lookup['icb'];
		
		$outstandingMRC = DB::table('TLG.dbo.v_sdscorecard')
					->where('interfacetypeint',47)
					->where('ProvOrderStatus','Completed')
					->whereNull('OrderCancelDt')
					->where('GlobalLogoID',$gid)
					->sum('MRR');
		
		$outstandingMRC = round($outstandingMRC,2);
		
		$ret['currentipv4mrccharged'] = $outstandingMRC;
		
		$serverArr = $_SERVER;
		if (isset($serverArr['HTTP_ORIGIN'])) {
			$server = $serverArr['HTTP_ORIGIN'];
		}
		
		Log::info($serverArr);
		return $this->response->array( $ret )->header('Access-Control-Allow-Origin',$server);
				
		
		
	}
	
	

	
	
	public static function getMRC($ipCount, $custIpCount, $CDR) {
		if ($ipCount == 0) {
			return 0;
		}
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






