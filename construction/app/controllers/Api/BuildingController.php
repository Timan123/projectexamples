<?php

namespace Api;

use DB;
use Building;
use Input;


/**
 * Description of BuildingController
 *
 * @author tcassidy
 */
class BuildingController extends BaseController {
	
	public function getBuildingRegionCounts() {
		
		$ret = [];
		
		$NALast15Days = Building::na()->whereRaw('OnNetDt >= dateadd(d,-15,getdate())')
				->whereRaw('OnNetDt <= getdate()')->count();
		
		$NANext30Days = Building::na()->whereRaw('OnNetDt > getdate()')
				->whereRaw('OnNetDt <= dateadd(d,30,getdate())')->count();
		
		
		$EULast15Days = Building::eu()->whereRaw('OnNetDt >= dateadd(d,-15,getdate())')
				->whereRaw('OnNetDt <= getdate()')->count();
		
		$EUNext30Days = Building::eu()->whereRaw('OnNetDt > getdate()')
				->whereRaw('OnNetDt <= dateadd(d,30,getdate())')->count();
		
		$NAInProgress = Building::na()->whereNotIn('ConstructionStatus',[1,4,13,14,15])->whereRaw('ConstructionStatus is not null')->count();
		
		$EUInProgress = Building::eu()->whereNotIn('ConstructionStatus',[1,4,13,14,15])->whereRaw('ConstructionStatus is not null')->count();
		
		$NARE1 = Building::na()->where('RealEstateStatus',1)->whereRaw("LicenseAgreementDt >= dateadd(d,-30,getdate())")->count();
		
		$EURE1 = Building::eu()->where('RealEstateStatus',1)->whereRaw("LicenseAgreementDt >= dateadd(d,-30,getdate())")->count();
		
		$NA = ['region' => 'NA', 'last15Days' => $NALast15Days, 'next30Days' => $NANext30Days, 'inProgress' => $NAInProgress, 'RE1' => $NARE1];
		$EU = ['region' => 'EU', 'last15Days' => $EULast15Days, 'next30Days' => $EUNext30Days, 'inProgress' => $EUInProgress, 'RE1' => $EURE1];
		array_push($ret, $NA);
		array_push($ret, $EU);
		
		return json_encode($ret);
	}
	
	public function getBuildingMarketCounts() {
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
			$last15Days = Building::market($market)->whereRaw('OnNetDt >= dateadd(d,-15,getdate())')
					->whereRaw('OnNetDt <= getdate()')->count();

			$next30Days = Building::market($market)->whereRaw('OnNetDt > getdate()')
					->whereRaw('OnNetDt <= dateadd(d,30,getdate())')->count();

			$inProgress = Building::market($market)->whereNotIn('ConstructionStatus',[1,4,13,14,15])->whereRaw('ConstructionStatus is not null')->count();

			$re1 = Building::market($market)->where('RealEstateStatus',1)->whereRaw("LicenseAgreementDt >= dateadd(d,-30,getdate())")->count();

			$sum = $last15Days + $next30Days + $inProgress + $re1;
			$part = ['market' => $market, 'last15Days' => $last15Days, 'next30Days' => $next30Days, 'inProgress' => $inProgress, 'RE1' => $re1, 'sum' => $sum];
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
	
	public function getBuildingFECounts() {
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

			$last15Days = Building::fe($FE)->whereRaw('OnNetDt >= dateadd(d,-15,getdate())')
					->whereRaw('OnNetDt <= getdate()')->count();

			$next30Days = Building::fe($FE)->whereRaw('OnNetDt > getdate()')
					->whereRaw('OnNetDt <= dateadd(d,30,getdate())')->count();

			$inProgress = Building::fe($FE)->whereNotIn('ConstructionStatus',[1,4,13,14,15])->whereRaw('ConstructionStatus is not null')->count();

			$re1 = Building::fe($FE)->where('RealEstateStatus',1)->whereRaw("LicenseAgreementDt >= dateadd(d,-30,getdate())")->count();

			$sum = $last15Days + $next30Days + $inProgress + $re1;
			$part = ['FE' => $FE, 'last15Days' => $last15Days, 'next30Days' => $next30Days, 'inProgress' => $inProgress, 'RE1' => $re1, 'sum' => $sum];
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
	
	public function getBuildingsDisplay($by, $type, $q) {
		
		$stmt = new Building;
		if ($by == 'region') {
			if ($q == 'NA') {
				$stmt = $stmt->na();
			}
			else {
				$stmt = $stmt->eu();
			}
		}
		if ($by == 'market') {
			$stmt = $stmt->market($q);
		}
		if ($by == 'FE') {
			$stmt = $stmt->fe($q);
		}
		switch ($type) {
			case 'last15Days':
				$stmt = $stmt->whereRaw('OnNetDt >= dateadd(d,-15,getdate())')
					->whereRaw('OnNetDt <= getdate()');
				break;
			case 'next30Days':
				$stmt = $stmt->whereRaw('OnNetDt > getdate()')
					->whereRaw('OnNetDt <= dateadd(d,30,getdate())');
				break;
			case 'inProgress':
				$stmt = $stmt->whereNotIn('ConstructionStatus',[1,4,13,14,15])->whereRaw('ConstructionStatus is not null');
				break;
			case 'RE1':
				$stmt = $stmt->where('RealEstateStatus',1)->whereRaw("LicenseAgreementDt >= dateadd(d,-30,getdate())");
				break;
		}
		
		$results = $stmt->get();
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function old_getBuildingMarketCounts() {
		$markets = ['New York','Washington, DC','Paris'];
		$ret = [];
		foreach($markets as $market) {
			$result = DB::table('TLG.dbo.Buildings')->whereRaw('OnNetDt >= dateadd(m,-4,getdate())')
				->whereRaw('OnNetDt <= getdate()')->where('CogentMarket',$market)->count();
			$part = ['market' => $market, 'count' => $result];
			array_push($ret,$part);
		}
		return json_encode($ret);
	}
	
	public function old_getBuildingFECounts() {
		$FEs = ['deverett','jgarcia','rwilkins'];
		$ret = [];
		foreach($FEs as $FE) {
			$result = DB::table('TLG.dbo.Buildings')->whereRaw('OnNetDt >= dateadd(m,-4,getdate())')
				->whereRaw('OnNetDt <= getdate()')->where('ConstructionManager',$FE)->count();
			$part = ['FE' => $FE, 'count' => $result];
			array_push($ret,$part);
		}
		return json_encode($ret);
	}
	

}
