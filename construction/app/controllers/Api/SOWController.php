<?php


namespace Api;

use WorkEvent;
use ProvWorkEvent;
use DB;
use Response;
use Input;
use Api\HelperController;

/**
 * Description of EventController
 *
 * @author tcassidy
 */
class SOWController extends BaseController {
	
	public function getSOWFECounts() {
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
		$completedSet = ['Completed pending FE','Completed with Defect','Completed','cancelled'];
		
		$ret = [];
		foreach($FEs as $FE) {

			$stmt = WorkEvent::whereHas('FEs', function ($query) use ($FE) {
				$query->where('FE',$FE);
			});
			$stmt1 = clone $stmt;
			$stmt2 = clone $stmt;
			$stmt3 = clone $stmt;
			
			$compLast5Days = $stmt1->whereRaw('rstop >= dateadd(d,-5,getdate())')->whereIn('Status',$completedSet)->count();
			
			$reqNext10Days = $stmt2->whereRaw('dueby <= dateadd(d,10,getdate())')->whereRaw('dueby >= getdate()')->count();
			
			$unCompOver30DaysOld = $stmt->whereRaw('date <= dateadd(d,-30,getdate())')
					->whereRaw('date >= dateadd(m,-6,getdate())')
					->whereNotIn('Status',$completedSet)->count();
			
			$totalUnComp = $stmt3->whereRaw('date >= dateadd(m,-6,getdate())')
					->whereNotIn('Status',$completedSet)->count();
			
			$sum = $compLast5Days + $reqNext10Days + $unCompOver30DaysOld + $totalUnComp;
			$part = ['FE' => $FE, 'compLast5Days' => $compLast5Days, 'reqNext10Days' => $reqNext10Days, 'unCompOver30DaysOld' => $unCompOver30DaysOld, 'totalUnComp' => $totalUnComp, 'sum' => $sum];
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
	
	
	
	
	public function getSOWRegionCounts() {
		$regions = ['NA', 'EU'];
		$completedSet = ['Completed pending FE','Completed with Defect','Completed','cancelled'];
		
		$ret = [];
		foreach($regions as $region) {
			
			$stmt = WorkEvent::where('Region','=',$region);
			$stmt1 = clone $stmt;
			$stmt2 = clone $stmt;
			$stmt3 = clone $stmt;
			
			$compLast5Days = $stmt1->whereRaw('rstop >= dateadd(d,-5,getdate())')->whereIn('Status',$completedSet)->count();
			
			$reqNext10Days = $stmt2->whereRaw('dueby <= dateadd(d,10,getdate())')->whereRaw('dueby >= getdate()')->count();
			
			$unCompOver30DaysOld = $stmt->whereRaw('date <= dateadd(d,-30,getdate())')
					->whereRaw('date >= dateadd(year,-1,getdate())')
					->whereNotIn('Status',$completedSet)->count();
			
			$totalUnComp = $stmt3->whereRaw('date >= dateadd(m,-6,getdate())')
					->whereNotIn('Status',$completedSet)->count();
			
			$sum = $compLast5Days + $reqNext10Days + $unCompOver30DaysOld + $totalUnComp;
			$part = ['region' => $region, 'compLast5Days' => $compLast5Days, 'reqNext10Days' => $reqNext10Days, 'unCompOver30DaysOld' => $unCompOver30DaysOld, 'totalUnComp' => $totalUnComp, 'sum' => $sum];
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
	
	public function getSOWMarketCounts() {
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
			
			$compLast5Days = WorkEvent::byMarket($market)->whereRaw('rstop >= dateadd(d,-5,getdate())')->completed()->count();
			
			$reqNext10Days = WorkEvent::byMarket($market)->whereRaw('dueby <= dateadd(d,10,getdate())')->whereRaw('dueby >= getdate()')->count();
			
			$stmt = WorkEvent::byMarket($market)->uncompleted()->whereRaw('date >= dateadd(m,-6,getdate())');
			
			$totalUnComp = $stmt->count();
			
			$unCompOver30DaysOld = $stmt->whereRaw('date <= dateadd(d,-30,getdate())')->count();
			
			$sum = $compLast5Days + $reqNext10Days + $unCompOver30DaysOld + $totalUnComp;
			$part = ['market' => $market, 'compLast5Days' => $compLast5Days, 'reqNext10Days' => $reqNext10Days, 'unCompOver30DaysOld' => $unCompOver30DaysOld, 'totalUnComp' => $totalUnComp, 'sum' => $sum];
			if ($sum > 0) {
				array_push($ret,$part);
			}
		}
		//do a descending sort
		usort($ret, function($a, $b)
		{
			return $b['sum'] > $a['sum'];
		});
		
		return json_encode($ret);
	}
	
	public function getSOWsDisplay($by, $type, $q) {
		$completedSet = ['Completed pending FE','Completed with Defect','Completed','cancelled'];
		
		//return $q;
		
		$stmt = new WorkEvent;
		if ($by == 'region') {
			$stmt = $stmt->where('Region','=',$q);
		}
		if ($by == 'market') {
			$stmt = $stmt->byMarket($q);
		}
		if ($by == 'FE') {
			$stmt = $stmt->whereHas('FEs', function ($query) use ($q) {
				$query->where('FE',$q);
			});
		}
		switch ($type) {
			case 'compLast5Days':
				$stmt = $stmt->whereRaw('rstop >= dateadd(d,-5,getdate())')->completed();
				break;
			case 'reqNext10Days':
				$stmt = $stmt->whereRaw('dueby <= dateadd(d,10,getdate())')->whereRaw('dueby >= getdate()');
				break;
			case 'totalUnComp':
				$stmt = $stmt->uncompleted()->whereRaw('date >= dateadd(m,-6,getdate())');
				break;
			case 'unCompOver30DaysOld':
				$stmt = $stmt->uncompleted()->whereRaw('date >= dateadd(m,-6,getdate())')->whereRaw('date <= dateadd(d,-30,getdate())');
				break;
		}
		
		$results = $stmt->with('FEs')->get();
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	

	

}
