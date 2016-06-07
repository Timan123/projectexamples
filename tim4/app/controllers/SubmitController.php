<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use \Carbon\Carbon;
use Cogent\Exception\DatabaseSavingException;
//use Log;


/**
 * Call it PageController to clarify it just routes the pages, action in Api Controllers/Knockout
 *
 * @author tcassidy
 */
class SubmitController  extends \BaseController
{
	
	
	
	public function updateDisputes() {
		$accountNum = Input::get('accountNum');
		$circuit = Input::get('circuit');
		$inv = Input::get('inv');

		
		//get the multiple inputs array style
		$remedyTicket = Input::get('RemedyTicket');
		$vendorDisputeNum = Input::get('VendorDisputeNum');
		$status = Input::get('Status');
		$remaining = Input::get('Remaining');
		$wonAmount = Input::get('WonAmount');
		$paidBack = Input::get('PaidBack');
		$invIndexNum = Input::get('InvIndexNum');
		$disputeId = Input::get('DisputeId');
		$lineIndexNum = Input::get('LineIndexNum');
		
		
		for($i = 0; $i < count($remaining); ++$i) {
			//find the line row using the key, update the remaining value
			$line = TelcoBillingLines::find($lineIndexNum[$i]);
			$origRemaining = $line->Remaining;
			$line->Remaining = str_replace(',','',$remaining[$i]);
			$deltaRemaining = $origRemaining - $remaining[$i];
			
			$line->saveOrFail();
			
			//find the dispute using the key, update all the info for this 
			$dispute = TelcoDispute::find($disputeId[$i]);
			$dispute->Status = $status[$i];
			$dispute->WonAmount = str_replace(',','',$wonAmount[$i]);
			$dispute->VendorDisputeNum = $vendorDisputeNum[$i];
			$dispute->RemedyTicket = $remedyTicket[$i];
			$dispute->PaidBack = str_replace(',','',$paidBack[$i]);
			$dispute->saveOrFail();
			
			
			$invoice = Invoice::find($invIndexNum[$i]);
			$rod = $invoice->RemainingOpenDispute;
			$totRemaining = $rod - $deltaRemaining;
			$invoice->RemainingOpenDispute = $totRemaining;
			//Log::info("\n\n\nOringRemaining: $origRemaining,  DeltaRemaining: $deltaRemaining,  ROD: $rod, TotRemaining: $totRemaining\n\n\n\n");
			$invoice->saveOrFail();
		}
		
		
		//return Redirect::route('disputes.index',['accountNum' => $accountNum, 'circuit' => $circuit])->withSuccess('Disputes successfully updated');
		return Redirect::route('disputes.index',compact( 'accountNum', 'circuit', 'inv'))->withSuccess('Disputes successfully updated');
		
	}
	
	
	public function assignAndCreateInv() {
		//return Input::all();
		$invoice = new Invoice;
		$invoice->InvoiceDt = Input::get("invoiceDt");
		$invoice->InvoiceNum = Input::get("accountNum");
		$invoice->TelcoAccNum = Input::get("accountNum");
		$invoice->PaymentDueDt = Input::get("dueDt");
		$invoice->InvoiceType = Input::get("invoiceType");
		$invoice->AssignedTo = Input::get("assignTo");
		$kwikTag = Input::get("kwikTag");
		$kwikTag = trim($kwikTag);
		//if the first character is alpha don't right trim down to 9 digits
		if (!ctype_alpha(substr($kwikTag,0,1))) {
			if (strlen($kwikTag) > 9) {
				$cut = 9 - strlen($kwikTag);
				$kwikTag = substr($kwikTag, 0, $cut);
			}
		}
		$invoice->KwikTag = $kwikTag;
		//invoices created in this way are always going to be 1/New
		$invoice->InvoiceStatus = 1;
		//we got the previous balance in a JS piece and set it on a hidden field
		$invoice->PrevBalance = Input::get('prevBal');
		$invoice->saveOrFail();
		
		$message = 'Invoice created and assigned to ' . Input::get("assignTo") . '!';
		
		
		
		return Redirect::route('invAssign.index',['invoiceType' => Input::get("invoiceType")])->withSuccess($message);
	}
	
	
	public function submitDispute() {
		$message = 'Dispute successfully ';
		$dispute = TelcoDispute::findOrNew(Input::get('DisputeId'));
		$indexNumToLink = Input::get('IndexNumToLink');
		//remove the primaryKey from the array to prevent trying to set it
		$arr = Input::all();
		unset($arr['DisputeId']);
		unset($arr['IndexNumToLink']);
		$arr['WonAmount'] = str_replace(',','',$arr['WonAmount']);
		$arr['PaidBack'] = str_replace(',','',$arr['PaidBack']);
		if (!$dispute->getKey()) {
			$message .= 'created';
		} else {
			$message .= 'updated';
		}
		$dispute->fill($arr);
		$dispute->saveOrFail();
		
		
		$key = $dispute->getKey();
		if ($indexNumToLink) {
			//take the disputeId that was just updated or inserted and set it on the billing line
			$line = TelcoBillingLines::find($indexNumToLink);
			$line->DisputeId = $key;
			$line->saveOrFail();
		}
		array_set($arr, 'message', $message);
		array_set($arr, 'DisputeId', $key);
		
		
		return $arr;
	}
	
	//also inserts invoice too! this is the big one
	public function updateInvoice()
	{
		//take out all the commas in this function along the way
		//return Input::all();
		//saveOrFail function we use here automatically does both timestamps like laravel does and puts the current user in the appropriate column, our extension
		$params = Input::all();
		//return $params;
		//stage the params to be filled into the Invoice Model, get all that start with Inv, remove that substring
		//call fill
		$message = 'Invoice successfully ';
		$invoiceParams = array_where($params, function($key, $value) { 
			if (strpos($key, 'Inv') === 0) {
				$key = substr($key,3);
				return true;
			} else {
				return false;
			}
		});
		
		foreach($invoiceParams as $key => $value){
			$invoiceParams[substr($key,3)] = str_replace(',','',$value);
			unset($invoiceParams[$key]);
		}
		
		$kwikTag = trim($invoiceParams['KwikTag']);
		//if the first character is alpha don't right trim down to 9 digits
		if (!ctype_alpha(substr($kwikTag,0,1))) {
			if (strlen($kwikTag) > 9) {
				$cut = 9 - strlen($kwikTag);
				$kwikTag = substr($kwikTag, 0, $cut);
			}
		}
		$invoiceParams['KwikTag'] = $kwikTag;
		//return $invoiceParams;
		$indexNum = Input::get('indexNum');
		$invoice = Invoice::findOrNew($indexNum);
		
		
		
		//grab the current status before we fill it from the form
		
		$invoice->fill($invoiceParams);
		//return ddd(Input::get('InvInvoiceStatus'));
		if (!$invoice->FirstDraftDt  && Input::get('InvInvoiceStatus') == 3) {
			$invoice->FirstDraftDt = $invoice->freshTimestamp();
		}
		if (!$invoice->FirstSubmitDt && Input::get('InvInvoiceStatus') == 4) {
			$invoice->FirstSubmitDt = $invoice->freshTimestamp();
		}
		$submitStatus = false;
		if (Input::get('InvInvoiceStatus') == 4) {
			$submitStatus = true;
		}
		$accountNum = Input::get('accountNum');
		$isNew = false;
		if (!$indexNum) {
			$isNew = true;
			$message .= 'created!';
			//only need to set the accountnum on a new invoice, it's not an updatable field
			$invoice->TelcoAccNum = $accountNum;
		} else {
			$message .= 'updated!';
		}

		$invoice->saveOrFail();
		//for existing invoice, indexNum is already from the form, for new invoice we get the primary key we just made
		$invoiceKey = $invoice->getKey(); //variable is redundant, but makes it clearer below what is happening
		$indexNum = $invoiceKey;
		//save it if it's not defined
		if (!$invoice->GPInvoiceNum) {
			//the GP number aka voucher number is jut TLINV_ with primarykey appended, so now that we got the key, do a round 2 update to set it as well
			$invoiceRound2 = Invoice::find($indexNum);
			$invoiceRound2->GPInvoiceNum = 'TLINV_' . $indexNum;
			$invoiceRound2->save();
		}
		
		
		//charge gets
		$pons = Input::get('pon');
		$indexNumsForCharge = Input::get('indexNumForCharge');
		
		//billing line gets
		$chargeLineIndexes = Input::get('chargeLineIndex');
		$billingLines = Input::get('billingLine');
		$billingLineIndexNums = Input::get('billingLineIndexNum');
		//hidden field set by jquery to help with backend linking of the dispute
		$disputeIds = Input::get('disputeId');
		$amounts = Input::get('amount');
		$disputed = Input::get('disputed');
		$remaining = Input::get('remaining');
		$notes = Input::get('notes');
		
	
		//do an old school loop here
		for($i = 0; $i < count($indexNumsForCharge); ++$i) {
			$charge = TelcoCircuitCharges::findOrNew($indexNumsForCharge[$i]);
			$charge->PON = $pons[$i];
			$charge->InvIndexNum = $invoiceKey;
			//InvoiceDt and AccountNum are unnecessary on the charge row with the new architecture but put them on anyway for clarity
			$charge->InvoiceDt = Input::get('InvInvoiceDt');
			$charge->TelcoAccNum = Input::get('accountNum');
			
			
			//iterate through the billing lines, check for index of them == this index, then 
			//use the charge type form the line to key off the right column in the charge table
			for($j = 0; $j < count($billingLines); ++$j) {
				if ($chargeLineIndexes[$j] == $i) {
					$charge->$billingLines[$j] = str_replace(',','',$amounts[$j]);
				}
			}
			
			$charge->saveOrFail();
			//set the indexNum to the key of the charge we just made, if inserting
			$indexNumsForCharge[$i] = $charge->getKey();
		}
		
		
		//work on the invoice one more time, only save if we did anything with it
		//putting the invoice level values on it for backwards compatibility, may drop
		$invoiceRound3 = Invoice::find($indexNum);
		$saveInvoice = false;
		
		$insertedNotes = [];
		
		//do an old school loop here
		for($i = 0; $i < count($billingLines); ++$i) {
			
			//do findOrNew on the lines
			$telcoBillingLine = TelcoBillingLines::findOrNew($billingLineIndexNums[$i]);
			//if it's a zero charge line delete it and continue to the next line in the loop
			if (!$amounts[$i] || $amounts[$i] == 0.00) {
				$telcoBillingLine->delete();
				continue;
			}
			
			$telcoBillingLine->Amount = str_replace(',','',$amounts[$i]);
			
			
			
			//if-out disputed, remaining, and disputeIds so don't try to set emptry string to numeric field
			//shouldn't be any empty strings out there but leave this code in anyway
			if ($disputed[$i]) { $telcoBillingLine->Disputed = str_replace(',','',$disputed[$i]); } else { $telcoBillingLine->Disputed = 0; }
			if ($remaining[$i]) { $telcoBillingLine->Remaining = str_replace(',','',$remaining[$i]); } else { $telcoBillingLine->Remaining = 0; }
			if ($disputeIds[$i]) { $telcoBillingLine->DisputeId = $disputeIds[$i]; }
			
			
			
			$telcoBillingLine->Type = $billingLines[$i];
			$telcoBillingLine->InvIndexNum = $invoiceKey;
			$telcoBillingLine->Notes = $notes[$i];
			//handle the charge level lines and the invoice level lines in same block, test on the int
			//we code invoice level lines as -1 in the view
			if ($chargeLineIndexes[$i] >= 0) {
				
				$telcoBillingLine->ChargeIndexNum = $indexNumsForCharge[$chargeLineIndexes[$i]];
				$telcoBillingLine->Source = 'Charge';
			} else {
				//key off the invoice level columns in this way
				$invoiceRound3->$billingLines[$i] = str_replace(',','',$amounts[$i]);
				$saveInvoice = true;
				$telcoBillingLine->Source = 'Invoice';
			}
			//if notes isn't empty string, use the same method to get the pon and insert it into lcdb notes
			//only save circuit notes when invoice is submitted to cut down on dupes
			//eat an exception here, circuit notes are not important
			try {
				if ($submitStatus) {
					if ($notes[$i]) {
						//don't insert invoice level notes here, these have -1 on the charge
						if ($chargeLineIndexes[$i] >= 0) {

							if (!in_array($pons[$chargeLineIndexes[$i]] . ',' . $notes[$i], $insertedNotes)) {

								//get the orderid and circuitid
								$circuit = TelcoOrderDetails::find($pons[$chargeLineIndexes[$i]]);
								DB::table('dbo.TelcoCircuitNotes')->insert(
									array(	'CircuitIndexNum' => $pons[$chargeLineIndexes[$i]], 
											'Note' => $notes[$i], 
											'AccountNum' => $accountNum,
											'OrderId' => $circuit->OrderId,
											'CircuitId' => $circuit->CircuitID,
											'ByUser' => Auth::user()->username, 
											'EnteredOn' => DB::raw('getdate()')));
								array_push($insertedNotes, $pons[$chargeLineIndexes[$i]] . ',' . $notes[$i]);
							}
						}
					}
				}
			} catch (Exception $e) {
				Log::error($e);
			}
			$telcoBillingLine->saveOrFail();
		}
		
		if ($saveInvoice) {
			$invoiceRound3->save();
		}

		return Redirect::route('main.index', [ 'invIndexNum' => $indexNum, 'vendorId' => Input::get('vendorId'), 'accountNum' => Input::get('accountNum') ])->withSuccess($message);
	}
	
	public function updateLink() {
		//return Input::all();
		//get the TIS row by account number or make a new account
		$tis = TelcoInvoiceSetup::firstOrNew(array('TelcoAccNum' => Input::get('accountNum')));
		
		$message = 'Account successfully ';
		if ($tis->isDirty()) {
			$message .= 'created';
		} else {
			$message .= 'updated';
		}
		if (Input::get('vendor')) {
			$tis->TelcoId = Input::get('vendor');
		}
		//it's ok to blank out the account owner, it's an optional field
		$tis->AccountOwner = Input::get('accountOwner');
		//return $tis;
		$tis->saveOrFail();
		
		//return $tis;
		$circuitIndexNum = null;
		//set the accountnumber on the circuit to link it, get the key to put on the circuit note if we have one
		if (Input::get('circuit')) {
			$circuit = TelcoOrderDetails::where('CircuitID','=',Input::get('circuit'))->first();
			if ($circuit) {
				$circuit->VendorAccountNum = Input::get('vendorAccountNum');
				$circuit->AccountNum = Input::get('accountNum');
				$circuit->saveOrFail();
				$circuitIndexNum = $circuit->getKey();
				$message .= ' and circuit successfully linked to account';
			}
		}
		//test on empty string to insert a note
		if (Input::get('lcdbNote') && $circuitIndexNum) {
			//no need to make a model for the circuit notes
			DB::table('dbo.TelcoCircuitNotes')->insert(
					array('CircuitIndexNum' => $circuitIndexNum, 'CircuitId' => Input::get('circuit'), 'Note' => Input::get('lcdbNote'), 
						'ByUser' => Auth::user()->username, 'EnteredOn' => DB::raw('getdate()')));
		}
		
		return Redirect::route('link.index',[ 'circuit' => Input::get('circuit'), 'accountNum' => Input::get('accountNum'), 'vendor' => Input::get('vendor'), 'accountOwner' => Input::get('accountOwner'), 'vendorAccountNum' => Input::get('vendorAccountNum')])->withSuccess($message);;
	}
}
