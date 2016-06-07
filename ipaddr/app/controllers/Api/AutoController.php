<?php

namespace Api;


use DB;
use Carbon;
use Input;
use Log;
use Config;

/**
 * Description of PageDataController
 *
 * @author tcassidy
 */
class AutoController extends BaseController {
	
	public function customerLookup()
	{
		$customerPiece = Input::get('customer');

		$recordsBeginWith = DB::table('BillingSystem.dbo.IPv4Billing')->select('CustomerName')
			//->where('CustomerName', 'LIKE', '%' . $customerPiece . '%')->get();
			->where('CustomerName', 'LIKE', $customerPiece . '%')->orderBy('CustomerName')->get();
		
		$flatBegin = array();
		array_walk_recursive($recordsBeginWith, function($a) use (&$flatBegin) { $flatBegin[] = $a; });

		$recordsContain = DB::table('BillingSystem.dbo.IPv4Billing')->select('CustomerName')
			->whereNotIn('CustomerName', $flatBegin)->orderBy('CustomerName')
			->where('CustomerName', 'LIKE', '%' . $customerPiece . '%')->get();
		$flatContain = array();
		array_walk_recursive($recordsContain, function($a) use (&$flatContain) { $flatContain[] = $a; });
		
		$total = array_merge($flatBegin, $flatContain);
		$multi = [];
		foreach($total as $val) {
			array_push($multi, ['value' => $val]);
		}
//			
//		
//		if ($vendor) {
//			$recordsContain->where('TelcoId','=',$vendor);
//		}
//		$recordsContain = $recordsContain->get();
//		
//		// Merge together and transform
//		$allRecords = $recordsBeginWith->merge( $recordsContain )
//			->transform(function($entry)
//			{
//				$accountNum = $entry->TelcoAccNum;
//
//				return [ 'value' => $accountNum, 'label' => $accountNum ];
//			});
		
		return $this->response->array( $multi );
	}
	
	public function gidLookup()
	{
		$gidPiece = Input::get('gid');

		$records = DB::table('BillingSystem.dbo.IPv4Billing')->select('GlobalLogoID as value')
			//->where('CustomerName', 'LIKE', '%' . $customerPiece . '%')->get();
			->where('GlobalLogoID', 'LIKE', $gidPiece . '%')->orderBy('GlobalLogoID')->get();
		
		
		
		return $this->response->array( $records );
	}
	
	
}






