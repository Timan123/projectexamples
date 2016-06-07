<?php

use Cogent\Model\TIMModel;
use Cogent\Utility\String;

class TimInvoiceStatuses extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.dbo.TimInvoiceStatuses';

	protected $visible = [ 'InvoiceStatusID', 'Description' ];
	
	protected $primaryKey = 'InvoiceStatusID';



}