<?php

namespace Api;

use Cogent\Utility\Database;
use TelcoInvoiceSetup;
use Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * Description of VendorController
 *
 * @author tcassidy
 */
class AccountsController extends BaseController
{
	/**
	 * @TODO: Add Comments!
	 */
	public function accounts()
	{
		// Possibly add in validation before to ensure at least 1 of the arguments is present

		$vendor  = Input::get('vendor');
		$circuit = Input::get('circuit');
		$pon     = Input::get('pon');
		$accountNum = Input::get('accountNum');

		// Construct the query
		$query = TelcoInvoiceSetup::where(function($query) use ($vendor, $accountNum)
			{
				if ( $vendor )
				{
					$query->where('TelcoId', $vendor);
				}
				if ( $accountNum) {
					$query->where('TelcoAccNum', $accountNum);
				}
			});
			
		//only pass the where condition onto the "has" if we queried by a circuit or a pon	
		if ( $circuit || $pon ) {
			$query->whereHas('orderDetails', function($query) use ($circuit, $pon)
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
		
		
		// Get the accounts paginated 10 each, like the invoice pane
		$records = $query->with('latestInvoice')->with('latestTotalInvoice')->paginate(10);

		return $this->response
			->paginator($records, new \Cogent\Transformer\Api\Accounts);
	}
	
	public function accountLookup()
	{
		$accountPiece = Input::get('account');
		$vendor = Input::get('vendor');

		$recordsBeginWith = TelcoInvoiceSetup::select('TelcoAccNum')
			->where('TelcoAccNum', 'LIKE', $accountPiece . '%');
		
		if ($vendor) {
			$recordsBeginWith->where('TelcoId','=',$vendor);
		}
		$recordsBeginWith = $recordsBeginWith->get();

		$recordsContain = TelcoInvoiceSetup::select('TelcoAccNum')
			->whereNotIn('TelcoAccNum', $recordsBeginWith->fetch('TelcoAccNum')->all())
			->where('TelcoAccNum', 'LIKE', '%' . $accountPiece . '%');
			
		
		if ($vendor) {
			$recordsContain->where('TelcoId','=',$vendor);
		}
		$recordsContain = $recordsContain->get();
		
		// Merge together and transform
		$allRecords = $recordsBeginWith->merge( $recordsContain )
			->transform(function($entry)
			{
				$accountNum = $entry->TelcoAccNum;

				return [ 'value' => $accountNum, 'label' => $accountNum ];
			});

		return $this->response->array( $allRecords->all() );
	}
	
	//check to see if this account exists
	public function accountCheck() {
		$account = Input::get('account');
		$record = TelcoInvoiceSetup::where("TelcoAccNum","=",$account)->get();
		return $this->response
			->collection($record, new \Cogent\Transformer\BaseTransformer);
		
	}
	
	//keep this here because it's kinda related to accounts, setting account owner
	public function getTimAnalysts() {
		$users = call_user_func([ app('config')->get('auth.model'), 'query' ])->hasRoles([ 'tim_public' ])->orderBy('username')->get(['username']);
		$users = $users->transform(function($entry)
			{
				$entry->username = strtolower($entry->username);
				return $entry;
			});
		return json_encode($users);
	}

	
}