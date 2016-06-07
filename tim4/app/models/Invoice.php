<?php

use Cogent\Model\TIMModel;

class Invoice extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.mjain.Invoices';

	protected $primaryKey = 'IndexNum';
	
	const UPDATED_AT = 'LastUpDt';
	
	const UPDATED_BY = 'LastUpUser';
	
	const CREATED_AT = 'CreatedDt';
	
	const CREATED_BY = 'CreatedUser';
	
	//protected $fillable = ['InvoiceNum'];
	
	//allow mass assignment
	protected $guarded = [];
		
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	/**
	 * The relations to eager load on every query.
	 *
	 * @var array
	 */
	protected $with = [ 'invoiceStatus' ];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [ 'invoiceStatusDesc', 'LastUpUser', 'InvoiceDt'];

	protected $hidden = [ 'invoiceStatus' ];
	/**
	 * Get the associated invoice status
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function invoiceStatus()
	{
		return $this->hasOne('TimInvoiceStatuses', 'InvoiceStatusID', 'InvoiceStatus');
	}
	
	public function lines() {
		// (Other Model, Foreign Key, Local Key)
		return $this->hasMany('TelcoCircuitCharges','InvIndexNum','IndexNum');
		
	}

	/**
	 * Retrieve the composed description of the invoice status
	 *
	 * @return string
	 */
	public function getInvoiceStatusDescAttribute()
	{
		return array_get($this->invoiceStatus, 'Description');

	}
	
	//lots of whitespace on this column, do it this way
	public function getLastUpUserAttribute()
	{
		return trim($this->getAttributeFromArray('LastUpUser'));
	}
	
	public function getInvoiceDtAttribute()
	{
		return trim($this->getAttributeFromArray('InvoiceDt'));

	}
	
	//link the invoice level billing lines in this way pass the where Source=Invoice to only get Invoice level billing lines
	public function telcoBillingLines() {
		return $this->hasMany('TelcoBillingLines','InvIndexNum','IndexNum')->where('Source','Invoice');
	}
	
	public function openLines() {
		return $this->hasMany('TelcoBillingLines','InvIndexNum','IndexNum')->where('Source','Invoice')->where('Remaining','>',0);
	}
	
		/**
	 * Get the associated vendor
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function account()
	{
		return $this->belongsTo('TelcoInvoiceSetup', 'TelcoAccNum', 'TelcoAccNum');
	}
}