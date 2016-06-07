<?php


namespace Api;

use WorkEvent;
use ProvWorkEvent;
use DB;


/**
 * Description of EventController
 *
 * @author tcassidy
 */
class EventController extends BaseController {
	
	
	public function getSOWs()
	{
		$results = WorkEvent::take(100)->get();
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function getSOWsByMarket($market) {
		$results = WorkEvent::where('Market','=',$market)->orderBy('Subject')->get();
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
		
	}
	
	public function getProvsByMarket($market) {
		$results = ProvWorkEvent::where('Market','=',$market)->get();
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
		
	}
	
	public function getBuildingsByMarket($market) {
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		$results = DB::table('TLG.dbo.Buildings')
				->select(['Address','City','State','ZipCD','BuildingID','NodeNumber'])
				->whereRaw('OnNetDt >= dateadd(m,-6,getdate())')
				->whereRaw('OnNetDt <= getdate()')
				->where('CogentMarket','=',$market)->get();
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		$results = \Cogent\Model\BaseModel::hydrate($results);
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function getSOWRegionCounts() {
		$NA = WorkEvent::where('Region','=','NA')->whereRaw('Date >= dateadd(m,-2,getdate())')->count();
		$EU = WorkEvent::where('Region','=','EU')->whereRaw('Date >= dateadd(m,-2,getdate())')->count();
		//return ['NA' => $NA, 'EU' => $EU];
		return json_encode(['NA' => $NA, 'EU' => $EU]);
	}
	
	
	public function getProvRegionCountsOld() {
		$NA = ProvWorkEvent::whereIn('Region',['AP','US','CA','MX'])->whereRaw('OnDate >= dateadd(m,-2,getdate())')->count();
		$EU = ProvWorkEvent::where('Region','=','EU')->whereRaw('OnDate >= dateadd(m,-2,getdate())')->count();
		//return ['NA' => $NA, 'EU' => $EU];
		return json_encode(['NA' => $NA, 'EU' => $EU]);
	}
	
	
	public function old_getProvMarketCounts() {
		$markets = ['New York','Washington, DC','Paris'];
		$ret = [];
		foreach($markets as $market) {
			$result = ProvWorkEvent::where('Market',$market)->whereRaw('OnDate >= dateadd(m,-2,getdate())')->count();
			$part = ['market' => $market, 'count' => $result];
			array_push($ret,$part);
		}
		return json_encode($ret);
	}
	
	public function old_getSOWMarketCounts() {
		$markets = ['New York','Washington, DC','Paris'];
		$ret = [];
		foreach($markets as $market) {
			$result = WorkEvent::where('Market',$market)->whereRaw('Date >= dateadd(m,-2,getdate())')->count();
			$part = ['market' => $market, 'count' => $result];
			array_push($ret,$part);
		}
		return json_encode($ret);
	}
	
	public function old_getProvFECounts() {
		$FEs = ['kbroadway','mperez','abrunet'];
		$ret = [];
		foreach($FEs as $FE) {
			$result = ProvWorkEvent::whereHas('FEs', function ($query) use ($FE) {
				$query->where('FE',$FE);
			})
			->whereRaw('OnDate >= dateadd(m,-2,getdate())')->count();
			$part = ['FE' => $FE, 'count' => $result];
			array_push($ret,$part);
		}
		return json_encode($ret);
	}
	
	public function old_getSOWFECounts() {
		$FEs = ['kbroadway','mperez','abrunet'];
		$ret = [];
		foreach($FEs as $FE) {
			$result = WorkEvent::whereHas('FEs', function ($query) use ($FE) {
				$query->where('FE',$FE);
			})
			->whereRaw('Date >= dateadd(m,-2,getdate())')->count();
			$part = ['FE' => $FE, 'count' => $result];
			array_push($ret,$part);
		}
		return json_encode($ret);
	}
}
