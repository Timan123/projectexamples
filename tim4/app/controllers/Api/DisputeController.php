<?php

namespace Api;

use Cogent\Utility\Database;
use TelcoDispute;
use TelcoCircuitCharges;
use TelcoBillingLines;
Use Invoice;
use Log;
use Auth;
use TelcoDisputeCategories;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * Description of VendorController
 *
 * @author tcassidy
 */
class DisputeController extends BaseController
{
	
	//not used practice method
	public function getAllDisputes() {
		
		return ddd(Auth::user()->can('tim_admin_update_invoices'));
//		$records = TelcoDispute::all();
//		return $this->response
//			->collection($records, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function getCategories() {
		$records = TelcoDisputeCategories::all();
		return $this->response
			->collection($records, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function getDisputesByAccountNum($accountNum) {
		//get all the invoices first
		$circuit = Input::get('circuit');
		$inv = Input::get('inv');
		$circuit = trim($circuit);
		$invoices = Invoice::where('TelcoAccNum',$accountNum)->get();
		$invIndexNums = [];
		foreach($invoices as $invoice) {
			array_push($invIndexNums,$invoice->IndexNum);
		}
		Log::info("\n\n\n$inv\n\n\n");
		//then get all the lines that have disputes, capturing both circuit level and invoive level
		$stmt = TelcoBillingLines::whereIn('InvIndexNum', $invIndexNums);
		if (filter_var($inv, FILTER_VALIDATE_BOOLEAN)) {
			Log::info("\n\n\napplied\n\n\n");
			$stmt->where('Source','Invoice');
		}
		$stmt->whereHas('dispute', function($query) {
					$query->whereNotNull('DisputeId')->whereNotIn('Status',['Closed-Won','Closed-PaidBack']);
				})->with([
		'dispute',			
		'charge' => function($query)
			{
				$query->select([ 'PON', 'IndexNum' ]);
			},
		'charge.telcoOrderDetails' => function($query)
			{
				$query->select([ 'CircuitID', 'IndexNum' ]);
			},
		'invoice'	
		]);
			
		
		if ($circuit) {
			$stmt->whereHas('charge.telcoOrderDetails', function($query) use ($circuit) {
					$query->where('CircuitID','=', $circuit);
				});
		}

		$records = $stmt->get();
		$records->sortByDesc('invoice.InvoiceDt');
		return $this->response
			->collection($records, new \Cogent\Transformer\Api\DisputeTransformer);
	}
	
	public function getOldDisputesByPon($pon) {
		
		$invoiceDt = Input::get('invoiceDt');
		$query = TelcoCircuitCharges::where('PON', $pon)
				->whereHas('openLines', function ($rquery) {
			$rquery->where('Remaining','>',0.00);
		})
		->with('openLines')->with('openLines.dispute')->orderBy('InvoiceDt','desc');
		if ($invoiceDt) {
			$query->where('InvoiceDt','!=',$invoiceDt);
		}
		$records = $query->get();
		return $this->response
			->collection($records, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function getOldInvoiceLevelDisputes($accountNum) {
		$invoiceDt = Input::get('invoiceDt');
		$query = Invoice::where('TelcoAccNum',$accountNum)->whereHas('openLines', function ($rquery) {
			$rquery->where('Remaining','>',0.00);
		})
		->with('openLines')->with('openLines.dispute')->orderBy('InvoiceDt','desc');
		if ($invoiceDt) {
			$query->where('InvoiceDt','!=',$invoiceDt);
		}
		$records = $query->get();
		return $this->response
			->collection($records, new \Cogent\Transformer\BaseTransformer);
	}
	
	

	
}