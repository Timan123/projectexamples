<?php

namespace Api;

use Cogent\Utility\Database;
use TelcoVendor;
use TelcoInvoiceSetup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * Description of VendorController
 *
 * @author tcassidy
 */
class VendorController extends BaseController
{
	/**
	 * @TODO: Add Comments!
	 */
	public function vendor()
	{
		// Possibly add in validation before to ensure at least 1 of the arguments is present
		//this DB call uses vendor view, needs the ANSI stuff
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		$vendor  = Input::get('vendor');
		$circuit = Input::get('circuit');
		$pon     = Input::get('pon');
		$accountNum = Input::get('accountNum');

		// Construct the query
		$query = TelcoVendor::where(function($query) use ($vendor)
			{
				if ( $vendor )
				{
					$query->where('VendorId', $vendor);
				}
			});
		//only pass the where condition onto the "has" if we queried by a circuit or a pon	
		if ( $circuit || $pon ) {
			$query->whereHas('accounts.orderDetails', function($query) use ($circuit, $pon)
			{
				if ( $circuit )
				{
					$query->where('CircuitId', $circuit);
				}

				if ( $pon )
				{
					$query->where('IndexNum', $pon);
				}
			});
		}
		if ( $accountNum ) {
			$query->whereHas('accounts', function($query) use ($accountNum)
			{
				$query->where('TelcoAccNum', $accountNum);	
			});
		}
					
		// Get the first result, which contains the accounts
		$record = $query->first();
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		return $this->response
			->item($record, new \Cogent\Transformer\Api\Vendor)
			//->item($record, new \Cogent\Transformer\BaseTransformer)	
			->meta('vendor', $vendor)
			->meta('circuit', $circuit)
			->meta('pon', $pon);
			//removed accountNum meta from here, it was screwing stuff up for pon and circuit queries
	}
	
	public function getAllVendors() {
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		$records = TelcoVendor::whereIn('VNDCLSID',['COLOCATION','CIRCUITS'])->get();
		return $this->response
			->collection($records, new \Cogent\Transformer\BaseTransformer);
		
		
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
	}
	
	public function vendorLookup() {
		//this DB call uses vendor view, needs the ANSI stuff
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		$vendorPiece = Input::get('vendor');
		$records = TelcoVendor::whereRaw("(VendorId like '%$vendorPiece%' OR Vendname like '%$vendorPiece%')");
		//if this is the main search search only for existing account attached vendors
		if (Input::has('existing')) {
			$records = $records->whereIn('VendorId',function($query) {
				$query->from('TLG.mjain.TelcoInvoiceSetup')->select('TelcoId');
			});
		} 
		//if this is the account link page search for only vendors with the 2 classes we want
		else {
			$records = $records->whereIn('VNDCLSID',['COLOCATION','CIRCUITS']);
		}
		
		$records = $records->get();
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		return $this->response
			->collection($records, new \Cogent\Transformer\Api\VendorLookup);
	}
}