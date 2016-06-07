<?php

namespace Api;

use DB;
use RemedyTickets;
use DispatchEmails;
use Input;


/**
 * Description of BuildingController
 *
 * @author tcassidy
 */
class TicketController extends BaseController {
	
	
	
	
	public function getTicketCounts() {
		$P1 = RemedyTickets::whereNotIn('Status',[5,7])->where('Severity','P1 Failure')->count();
		$P2 = RemedyTickets::whereNotIn('Status',[5,7])->where('Severity','P2 Failure')->count();
		$P3 = RemedyTickets::whereNotIn('Status',[5,7])->where('Severity','P3 Failure')->count();
		$closedLast5Days = RemedyTickets::whereIn('Status',[5,7])->whereRaw('Modified_Date >= dateadd(d,-5,getdate())')->count();
		$older15Days = RemedyTickets::whereNotIn('Status',[5,7])
				->whereHas('mostRecentDispatchEmail', function ($query) {
				$query->whereRaw('Date_Sent < dateadd(d,-15,getdate())');
			})->count();
		$nocAwaiting = RemedyTickets::whereNotIn('Status',[5,7])->whereRaw("PendingNext like 'NOC Awaiting FE%'")->count();
		$ret = ['region' => 'NA', 'P1' => $P1, 'P2' => $P2, 'P3' => $P3, 'closedLast5Days' => $closedLast5Days, 'older15Days' => $older15Days, 'nocAwaiting' => $nocAwaiting];
		return json_encode($ret);
		
	}
	
	public function getTicketMarketCounts() {
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
		
			$P1 = RemedyTickets::byMarket($market)->whereNotIn('Status',[5,7])->where('Severity','P1 Failure')->count();
			$P2 = RemedyTickets::byMarket($market)->whereNotIn('Status',[5,7])->where('Severity','P2 Failure')->count();
			$P3 = RemedyTickets::byMarket($market)->whereNotIn('Status',[5,7])->where('Severity','P3 Failure')->count();
			$closedLast5Days = RemedyTickets::byMarket($market)->whereIn('Status',[5,7])->whereRaw('Modified_Date >= dateadd(d,-5,getdate())')->count();
			$older15Days = RemedyTickets::byMarket($market)->whereNotIn('Status',[5,7])
					->whereHas('mostRecentDispatchEmail', function ($query) {
					$query->whereRaw('Date_Sent < dateadd(d,-15,getdate())');
				})->count();
			$nocAwaiting = RemedyTickets::byMarket($market)->whereNotIn('Status',[5,7])->whereRaw("PendingNext like 'NOC Awaiting FE%'")->count();
			$sum = $P1 + $P2 + $P3 + $closedLast5Days + $older15Days + $nocAwaiting;
			$part = ['market' => $market, 'P1' => $P1, 'P2' => $P2, 'P3' => $P3, 'closedLast5Days' => $closedLast5Days, 'older15Days' => $older15Days, 'nocAwaiting' => $nocAwaiting, 'sum' => $sum];
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
	
	public function getTicketFECounts() {
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
			$P1 = RemedyTickets::byFE($FE)->whereNotIn('Status',[5,7])->where('Severity','P1 Failure')->count();
			$P2 = RemedyTickets::byFE($FE)->whereNotIn('Status',[5,7])->where('Severity','P2 Failure')->count();
			$P3 = RemedyTickets::byFE($FE)->whereNotIn('Status',[5,7])->where('Severity','P3 Failure')->count();
			$closedLast5Days = RemedyTickets::byFE($FE)->whereIn('Status',[5,7])->whereRaw('Modified_Date >= dateadd(d,-5,getdate())')->count();
			$older15Days = RemedyTickets::byFE($FE)->whereNotIn('Status',[5,7])
					->whereHas('mostRecentDispatchEmail', function ($query) {
					$query->whereRaw('Date_Sent < dateadd(d,-15,getdate())');
				})->count();
			$nocAwaiting = RemedyTickets::byFE($FE)->whereNotIn('Status',[5,7])->whereRaw("PendingNext like 'NOC Awaiting FE%'")->count();
			$sum = $P1 + $P2 + $P3 + $closedLast5Days + $older15Days + $nocAwaiting;
			$part = ['FE' => $FE, 'P1' => $P1, 'P2' => $P2, 'P3' => $P3, 'closedLast5Days' => $closedLast5Days, 'older15Days' => $older15Days, 'nocAwaiting' => $nocAwaiting, 'sum' => $sum];
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
	
	public function getTicketsDisplay($by, $type, $q) {
		
		$stmt = new RemedyTickets;
		//do nothing for region since they are all NA anyway
		if ($by == 'market') {
			$stmt = $stmt->byMarket($q);
		}
		if ($by == 'FE') {
			$stmt = $stmt->byFE($q);
		}
		switch ($type) {
			case 'P1':
				$stmt = $stmt->whereNotIn('Status',[5,7])->where('Severity','P1 Failure');
				break;
			case 'P2':
				$stmt = $stmt->whereNotIn('Status',[5,7])->where('Severity','P2 Failure');
				break;
			case 'P3':
				$stmt = $stmt->whereNotIn('Status',[5,7])->where('Severity','P3 Failure');
				break;
			case 'older15Days':
				$stmt = $stmt->whereNotIn('Status',[5,7])
					->whereHas('mostRecentDispatchEmail', function ($query) {
					$query->whereRaw('Date_Sent < dateadd(d,-15,getdate())');
					});
				break;
			case 'closedLast5Days':
				$stmt = $stmt->whereIn('Status',[5,7])->whereRaw('Modified_Date >= dateadd(d,-5,getdate())');
				break;
			case 'nocAwaiting':
				$stmt = $stmt->whereNotIn('Status',[5,7])->whereRaw("PendingNext like 'NOC Awaiting FE%'");
				break;
		}
		
		$results = $stmt->with('mostRecentDispatchEmail')->get();
		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
	}
	
	
	
	
	
	
	

}
