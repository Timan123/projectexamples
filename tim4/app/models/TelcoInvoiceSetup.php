<?php

use Cogent\Model\TIMModel;

class TelcoInvoiceSetup extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.mjain.TelcoInvoiceSetup';

	public static $snakeAttributes = false;
	
	protected $primaryKey = 'TelcoAccNum';
	
	const UPDATED_AT = 'LastUpdt';
	
	const CREATED_AT = 'CreatedDt';
	
	const UPDATED_BY = 'LastUpUser';
	
	const CREATED_BY = 'CreatedUser';
	
	//allow mass assignment
	protected $guarded = [];
	/**
	 * @TODO: Add comments!
	 */
	public function orderDetails()
	{
		// (Other Model, Foreign Key, Local Key )
		return $this->hasOne('TelcoOrderDetails', 'CogentAccount#', 'TelcoAccNum');
	}
	
	public function invoices() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasMany('Invoices', 'TelcoAccNum', 'TelcoAccNum');
	}
	
	//if we grab the latest invoice, don't grab a "New" status one because it'll have blank charges on it
	public function latestInvoice() {
		return $this->hasOne('Invoice', 'TelcoAccNum', 'TelcoAccNum')->where('InvoiceStatus','!=',1)->orderBy('InvoiceDt','desc');
	}
	
	//need a separate relation here to get the absolute newest invoice, to prevent a crashing of the dates, violating unique key
	public function latestTotalInvoice() {
		return $this->hasOne('Invoice', 'TelcoAccNum', 'TelcoAccNum')->orderBy('InvoiceDt','desc');
	}

	/**
	 * Get the associated vendor
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function vendor()
	{
		return $this->belongsTo('TelcoVendor', 'TelcoId', 'VENDORID');
	}
}