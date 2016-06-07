<?php


namespace Api;

use WorkEvent;
use ProvWorkEvent;
use DB;
use Input;


/**
 * Description of EventController
 *
 * @author tcassidy
 */
class ProvController extends BaseController {
	
	protected static $activeSet = ['Construction Alert - No ETA',
									'Construction Alert - with ETA',
									'Hold/customer',
									'Hold/Sales',
									'Hold/SysEng - Network Capacity',
									'Order in Progress',
									'Pending customer x-connect',
									'Pending Z-End',
									'Pre-Provisioning Completed',
									'Submit to IP Engineering',
									'Work Order Sent',
									'Work Order Sent - Pending ETA',
									'Work Order Sent - Pending CFA',
									'Work Order Sent - Pending Site Survey'];		
	
	public function getProvRegionCounts() {
		$ret = [];
		
		$activeStmt = ProvWorkEvent::whereHas('order', function ($query) {
				$query->whereIn('StatusCode',ProvController::$activeSet);
			});
		
		$openStmt = ProvWorkEvent::whereHas('order', function ($query) {
				$query->whereRaw("StatusCode like 'Work Order Sent%'");
			});
		$holdStmt = ProvWorkEvent::whereHas('order', function ($query) {
				$query->where('StatusCode','Hold/customer');
			});
		$hold2 = clone $holdStmt;
		$open2 = clone $openStmt;
		$active2 = clone $activeStmt;
		
		$totalActiveNA = $activeStmt->where('Region','North America')->count();
		$totalOpenNA = $openStmt->where('Region','North America')->count();
		$totalOpenNAOlder14Days = $openStmt->whereRaw("OnDate < dateadd(d,-14,getdate())")->count();
		$totalHoldNA = $holdStmt->where('Region','North America')->count();
		
		$totalActiveEU = $active2->where('Region','Europe')->count();
		$totalOpenEU = $open2->where('Region','Europe')->count();
		$totalOpenEUOlder14Days = $open2->whereRaw("OnDate < dateadd(d,-14,getdate())")->count();
		$totalHoldEU = $hold2->where('Region','Europe')->count();
		
		$NA = ['region' => 'NA', 'totalActive' => $totalActiveNA, 'totalOpen' => $totalOpenNA, 'totalOpenOlder14Days' => $totalOpenNAOlder14Days, 'totalHold' => $totalHoldNA];
		$EU = ['region' => 'EU', 'totalActive' => $totalActiveEU, 'totalOpen' => $totalOpenEU, 'totalOpenOlder14Days' => $totalOpenEUOlder14Days, 'totalHold' => $totalHoldEU];
		array_push($ret, $NA);
		array_push($ret, $EU);
		
		return json_encode($ret);
	}
	
	public function getProvFECounts() {
		$FEs = null;
		if (Input::get('FEs')) {
			$FEs = Input::get('FEs');
			if ($FEs == 'NA') {
				$FEs = HelperController::getFEsFlatArray('NA');
			} else if ($FEs == 'EU') {
				$FEs = HelperController::getFEsFlatArray('EU');
			}
		}
		else {
			$FEs = HelperController::getFEsFlatArray('All');
		}
		
		$ret = [];
		foreach($FEs as $FE) {
			
			$totalActive = ProvWorkEvent::whereHas('FEs', function ($query) use ($FE) {
				$query->where('FE',$FE);
			})->whereHas('order', function ($query) {
				$query->whereIn('StatusCode',ProvController::$activeSet);
			})->count();
			
			$totalHold = ProvWorkEvent::whereHas('FEs', function ($query) use ($FE) {
				$query->where('FE',$FE);
			})->whereHas('order', function ($query) {
				$query->where('StatusCode','Hold/customer');
			})->count();
			
			$openStmt = ProvWorkEvent::whereHas('FEs', function ($query) use ($FE) {
				$query->where('FE',$FE);
			})->whereHas('order', function ($query) {
				$query->whereRaw("StatusCode like 'Work Order Sent%'");
			});
			$totalOpen = $openStmt->count();
			$totalOpenOlder14Days = $openStmt->whereRaw("OnDate < dateadd(d,-14,getdate())")->count();
						
			$sum = $totalActive + $totalOpen + $totalHold;
			$part = ['FE' => $FE, 'totalActive' => $totalActive, 'totalOpen' => $totalOpen, 'totalOpenOlder14Days' => $totalOpenOlder14Days, 'totalHold' => $totalHold, 'sum' => $sum];
			if ($sum > 0) {
				array_push($ret,$part);
			}
		}
		
		usort($ret, function($a, $b)
		{
			return $b['sum'] > $a['sum'];
		});
		
		return json_encode($ret);
	}
	
	public function getProvMarketCounts() {
		$markets = null;
		if (Input::get('markets')) {
			$markets = Input::get('markets');
			if ($markets == 'NA') {
				$markets = HelperController::getMarketsFlatArray('NA');
			} else if ($markets == 'EU') {
				$markets = HelperController::getMarketsFlatArray('EU');
			}
		}
		else {
			$markets = HelperController::getMarketsFlatArray('All');
		}
		
		$ret = [];
		foreach($markets as $market) {
			
			$totalActive = ProvWorkEvent::where('Market',$market)->whereHas('order', function ($query) {
				$query->whereIn('StatusCode',ProvController::$activeSet);
			})->count();

			$totalHold = ProvWorkEvent::where('Market',$market)->whereHas('order', function ($query) {
				$query->where('StatusCode','Hold/customer');
			})->count();
			
			$openStmt = ProvWorkEvent::where('Market',$market)->whereHas('order', function ($query) {
				$query->whereRaw("StatusCode like 'Work Order Sent%'");
			});
			$totalOpen = $openStmt->count();
			$totalOpenOlder14Days = $openStmt->whereRaw("OnDate < dateadd(d,-14,getdate())")->count();
						
			$sum = $totalActive + $totalOpen + $totalHold;
			$part = ['market' => $market, 'totalActive' => $totalActive, 'totalOpen' => $totalOpen, 'totalOpenOlder14Days' => $totalOpenOlder14Days, 'totalHold' => $totalHold, 'sum' => $sum];
			if ($sum > 0) {
				array_push($ret,$part);
			}
		}
		//do a descending sort with this
		usort($ret, function($a, $b)
		{
			return $b['sum'] > $a['sum'];
		});
		
		return json_encode($ret);
	}
	
	public function getProvsDisplay($by, $type, $q) {
		
	
		$stmt = new ProvWorkEvent;
		if ($by == 'region') {
			if ($q == 'NA') {
				$stmt = $stmt->where('Region','North America');
			}
			else {
				$stmt = $stmt->where('Region','Europe');
			}
		}
		if ($by == 'market') {
			$stmt = $stmt->where('Market',$q);
		}
		if ($by == 'FE') {
			$stmt = $stmt->whereHas('FEs', function ($query) use ($q) {
				$query->where('FE',$q);
			});
		}
		switch ($type) {
			case 'totalActive':
				$stmt = $stmt->whereHas('order', function ($query) {
					$query->whereIn('StatusCode',ProvController::$activeSet);
				});
				break;
			case 'totalHold':
				$stmt = $stmt->whereHas('order', function ($query) {
					$query->where('StatusCode','Hold/customer');
				});
				break;
			case 'totalOpen':
				$stmt = $stmt->whereHas('order', function ($query) {
					$query->whereRaw("StatusCode like 'Work Order Sent%'");
				});
				break;
			case 'totalOpenOlder14Days':
				$stmt = $stmt->whereRaw("OnDate < dateadd(d,-14,getdate())")->whereHas('order', function ($query) {
					$query->whereRaw("StatusCode like 'Work Order Sent%'");
				});
				break;
		}
		
		$results = $stmt->with('FEs')->orderBy('OnDate','desc')->get();
		$statuses = [];
		foreach($results as $result) {
			$status = $result->StatusCode;
			if (!array_key_exists($status, $statuses)) {
				$statuses[$status] = 1;
			} else {
				$statuses[$status] += 1;
			}
		}
		
		
		//unflatten it
		$unFlat = [];
		$keys = array_keys($statuses);
		foreach($keys as $key) {
			array_push($unFlat, ['status' => $key, 'count' => $statuses[$key]]);
		}
		//do a descending sort with this
		usort($unFlat, function($a, $b)
		{
			return $b['count'] > $a['count'];
		});
		//return $unFlat;
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer)
			->meta('statuses',$unFlat);
	}

}
