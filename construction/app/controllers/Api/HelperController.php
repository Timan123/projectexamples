<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api;

use Market;
use DB;
use Exception;

/**
 * Description of MarketController
 *
 * @author tcassidy
 */
class HelperController extends BaseController {
	
	
	public function getMarkets() {
		$results = DB::table('TLG.dbo.CogentMarket')->selectRaw('rtrim(CogentMarket) as Market')->distinct()->get();
		$results = \Cogent\Model\BaseModel::hydrate($results);
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function getFEs() {
		$results = DB::table('HRDB.dbo.v_hrdb')->selectRaw('lower(Username) as Username')
				->whereRaw("CurrentPositionID in (select ID from HRDB.dbo.HRPositionList where FE = 1)")
				->where('Status','Active')->orderBy('Username')->get();
		$results = \Cogent\Model\BaseModel::hydrate($results);
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	
	public static function getMarketsFlatArray($set) {
		$stmt = DB::table('TLG.dbo.CogentMarket')->selectRaw('rtrim(CogentMarket) as Market')->distinct();
		if ($set == 'NA') {
			$stmt->whereIn('Continent',['North America','Asia-Pac']);
		} else if ($set == 'EU') {
			$stmt->where('Continent','Europe');
		}
		$results = $stmt->get();
		$arr = [];
		foreach ($results as $market) {
			array_push($arr, $market->Market);
		}
		return $arr;
	}
	
	public static function getFEsFlatArray($set) {
		$stmt = DB::table('HRDB.dbo.v_hrdb')->selectRaw('lower(Username) as Username')
			->whereRaw("CurrentPositionID in (select ID from HRDB.dbo.HRPositionList where FE = 1)")
			->where('Status','Active')->orderBy('Username');
		if ($set == 'NA') {
			$stmt->whereIn('PayrollProvider',['US','Canada']);
		} else if ($set == 'EU') {
			$stmt->whereNotIn('PayrollProvider',['US','Canada']);
		}
		$results = $stmt->get();
		$arr = [];
		foreach ($results as $FE) {
			array_push($arr, $FE->Username);
		}
		return $arr;
	}
	
	public function test() {
		throw new Exception('saldkfasklfkas');
//return json_encode(boolval( app('config')->get('app.debug') ));
	}
}
