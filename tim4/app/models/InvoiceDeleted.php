<?php

use Cogent\Model\TIMModel;

class InvoiceDeleted extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.mjain.InvoicesDeleted';

	protected $primaryKey = 'DeletedIndexNum';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
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
	
}