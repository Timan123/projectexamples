<?php

namespace Api;

use Cogent\Utility\Database;
use DB;
use TelcoInvoiceSetup;
use Invoice;
use InvoiceDeleted;
use TelcoBillingLines;
use TelcoOrderDetails;
use TelcoCircuitCharges;
use TimInvoiceStatuses;
use Illuminate\Support\Facades\Input;
/**
 * Description of InvoiceController
 *
 * @author tcassidy
 */
class InvoiceController extends BaseController {
	
	public function invoices($accountNum) {
		$records = Invoice::where('TelcoAccNum', $accountNum)->orderBy('InvoiceDt', 'desc')->paginate(10);
		return $this->response
			->paginator($records, new \Cogent\Transformer\BaseTransformer);
	}
	
	//this gets the header of the current invoice, making a header of a new invoice is handled in the JS and the submission
	//of the invoice assignment screen
	public function getCurrentInvoice($indexNum) {
		$record = Invoice::where('IndexNum', $indexNum)->with('telcoBillingLines.dispute')->first();
		return $this->response
			->item($record, new \Cogent\Transformer\Api\InvoiceHeader);
	}
	
	public function getLinesAndPons($indexNum) {
		$records = TelcoCircuitCharges::where('InvIndexNum', $indexNum)->with('telcoOrderDetails')->with('telcoBillingLines.dispute')->get();
		return $this->response
			->collection($records, new \Cogent\Transformer\BaseTransformer);
	}
	
	public function invoiceSearch() {
		//return Input::all();
		//this DB call uses vendor view, needs the ANSI stuff
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		$query = Invoice::with('account')->with('account.vendor');
		if (Input::get('assignedTo')) {
			$query = $query->where('AssignedTo','=',Input::get('assignedTo'));
		}
		if (Input::get('invoiceStatus')) {
			$query = $query->where('InvoiceStatus','=',Input::get('invoiceStatus'));
		}
		if (Input::get('invoiceDtFrom') && Input::get('invoiceDtTo')) {
			$query = $query->whereRaw("InvoiceDt between '" . Input::get('invoiceDtFrom') . "' AND '" . Input::get('invoiceDtTo') . "'");
		}
		if (Input::get('createdDtFrom') && Input::get('createdDtTo')) {
			$query = $query->whereRaw("CreatedDt between '" . Input::get('createdDtFrom') . "' AND '" . Input::get('createdDtTo') . "'");
		}
		
		
		
		$records = $query->orderBy('InvoiceDt','desc')->take(2500)->get();
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		return $this->response
			->collection($records, new \Cogent\Transformer\BaseTransformer);
	}
	
	//this makes the export for the batch
	public function timReport() {
		//return Input::all();
		
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		$start = Input::get('invoiceDtFrom');
		$end = Input::get('invoiceDtTo');
		$query = DB::table('mjain.Invoices as inv')
				->leftJoin('mjain.TelcoInvoiceSetup as tis','inv.TelcoAccNum','=','tis.TelcoAccNum')
				->leftJoin('dbo.TimInvoiceStatuses as st','inv.InvoiceStatus','=','st.InvoiceStatusID')
				->leftJoin('dbo.v_TelcoVendors as v','v.VendorId','=','tis.TelcoId')
				->select(['inv.*',
					'v.VendorId',
					'v.VendName',
					'st.Description as Status',
					DB::raw("( select sum(MonthlyCost) from mjain.TelcoOrderDetails where AccountNum =  inv.TelcoAccNum and isactive='Y'	and (TelcoStopBillDt is null or TelcoStopBillDt >  inv.InvoiceDt )) as TotalMRC"),
					DB::raw("(select sum(isnull(Installation, 0)) from mjain.TelcoCircuitCharges where InvIndexNum = inv.IndexNum and cast(LastUpDt as date) between '$start' AND '$end') as TotalNRC"),
					DB::raw("(select	sum(isnull(OM,0)) from mjain.TelcoCircuitCharges where InvIndexNum = inv.IndexNum) as TotalOM")
					]);
		
		
		$query = $query->whereRaw("cast(inv.LastUpDt as date) between '$start' AND '$end'");
		$query = $query->where("v.Region",Input::get('region'));
		
		if (Input::get("invoiceStatus")) {
			$query = $query->where("inv.InvoiceStatus",Input::get("invoiceStatus"));
		}
		if (Input::get("lastUpdatedBy")) {
			$query = $query->where("inv.LastUpUser",Input::get("lastUpdatedBy"));
		}

		$results = $query->get();
		
		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		$results = \Cogent\Model\BaseModel::hydrate($results);

		return $this->response
			->collection($results, new \Cogent\Transformer\BaseTransformer);
		
	}
	
	
	public function newLinesAndPons($accountNum) {
		$theAccount = TelcoInvoiceSetup::where('TelcoAccNum', $accountNum)->with('latestInvoice')->first();
		$putThemOn = false;
		$lastInvoice = null;
		//latestInvoice here is actually the latest ihvoice where status != new, see the account model
		//so we can get real financials from the last invoice
		//absolute latest invoice is latestTotalInvoice
		if ($theAccount->latestInvoice) {
			$lastInvoice = $theAccount->latestInvoice->IndexNum;
			$putThemOn = true;
		}
		
		//stick the mrc from the lastinvoice on the circuit, then we can move it structurally in the transformer
		//if there is no last invoice, stick 0's on all of them with the boolean we made
		$circuits = TelcoOrderDetails::where('CogentAccount#',$accountNum)->get();
		$circuits = $circuits->map(function($entry) use ($lastInvoice, $putThemOn) {
			if ($putThemOn) {
				$pon = $entry->PON;
				$chargeOnLast = TelcoCircuitCharges::where('PON','=', $pon)->where('InvIndexNum','=',$lastInvoice)->select(['MRC','FBCredit'])->first();
				$fbCredit = TelcoCircuitCharges::where('PON','=', $pon)->where('InvIndexNum','=',$lastInvoice)->select('FBCredit')->first();
				//there might not be a charge to go with this circuit because it's a new circuit, so "if out" the null here to 0
				if ($chargeOnLast && $chargeOnLast->MRC != 0.00) {
					$entry->chargeMRC = $chargeOnLast->MRC;
				} else {
					$entry->chargeMRC = 0;
				}
				if ($chargeOnLast && $chargeOnLast->FBCredit != 0.00) {
					$entry->chargeFBCredit = $chargeOnLast->FBCredit;
				} else {
					$entry->chargeFBCredit = 0;
				}
				
			} else {
				$entry->chargeMRC = 0;
			}
			return $entry;
		});
		return $this->response
			->collection($circuits, new \Cogent\Transformer\Api\NewLinesAndPonsTransformer);
	}
	
	public function getInvoiceStatuses() {
		
		$records = TimInvoiceStatuses::all();
		return $this->response
			->collection($records, new \Cogent\Transformer\BaseTransformer);
	
	}
	
	public function newInvLevelBillingLines($invIndexNum) {
		$records = TelcoBillingLines::where('InvIndexNum',$invIndexNum)->where('Source','Invoice')->get();
		return $this->response
			->collection($records, new \Cogent\Transformer\Api\NewInvLevelBillingLines);
	}
	
	public function test($accountNum) {
		$theAccount = TelcoInvoiceSetup::where('TelcoAccNum', $accountNum)->with('latestInvoice')->first();
		return $theAccount->latestInvoice;
	}
	
	public function deleteInv($gpInvoiceNum) {
		$keyToDel = str_replace('TLINV_','',$gpInvoiceNum);
		$invModel = Invoice::find($keyToDel);
		$reason = sanitize_string(Input::get('reason'));
		if ($invModel) {
			
			$invDeleted = new InvoiceDeleted;
			$arr = $invModel->toArray();
			//invoiceStatusDesc is a derived field from a join/relation, will not go in the deleted table
			unset($arr['invoiceStatusDesc']);
			$invDeleted->fill($arr);
			$invDeleted->Reason = $reason;
			$invDeleted->save();
			$invModel->delete();
			return json_encode($gpInvoiceNum);
		} else {
			return json_encode('No Invoice');
		}
		
	}
	
	//the new invoice header is made entirely in JS
//	public function getNewInvoiceHeader($indexNum) {
//		
//	}
	
}
