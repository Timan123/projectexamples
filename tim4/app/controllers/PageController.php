<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Call it PageController to clarify it just routes the pages, action in Api Controllers/Knockout
 *
 * @author tcassidy
 */
class PageController  extends \BaseController
{
	public function mainPage()
	{
		$invIndexNum = Input::get('invIndexNum');
		$vendorId    = Input::get('vendorId');
		$accountNum  = Input::get('accountNum');

		return $this->view('main', compact( 'invIndexNum', 'vendorId', 'accountNum'));
	}
	
	public function kwikTagPage()
	{
		$barcode = Input::get('barcode');
		return $this->view('kwikTag', compact( 'barcode'));
	}
	
	public function disputesPage()
	{
		$accountNum  = Input::get('accountNum');
		$inv  = Input::get('inv');
		$circuit  = Input::get('circuit');

		return $this->view('disputes', compact( 'accountNum', 'circuit', 'inv'));
	}
	
	public function reportPage()
	{
		return $this->view('report', []);
	}
	
	public function linkPage()
	{
		$accountNum = Input::get('accountNum');
		$circuit = Input::get('circuit');
		$vendor = Input::get('vendor');
		$accountOwner = Input::get('accountOwner');
		$vendorAccountNum = Input::get('vendorAccountNum');
		return $this->view('link', compact( 'accountNum', 'circuit', 'vendor', 'vendorAccountNum', 'accountOwner'));
	}
	
	public function testPage() {
		$records = Invoice::where('TelcoAccNum', 'C11RQ11000105')->orderBy('InvoiceDt', 'desc')->paginate(10);
		//return $records;
		return $this->view('test',['records' => $records]);
	}
	
	public function invAssign() {
		$invoiceType = Input::get('invoiceType');
		return $this->view('invoiceAssign',compact('invoiceType'));
	}
	
	public function invListing() {
		return $this->view('invoiceListing',[]);
	}
	
	public function searchPage() {
		return $this->view('search',[]);
	}
	
	public function deleteInv() {
		return $this->view('deleteInv',[]);
	}
	
	
}
