<?php

use Cogent\Model\TIMModel;
use Cogent\Utility\String;

class TelcoVendor extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.dbo.v_telcovendors';

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [ 'vendorDesc', 'vendorShortDesc', 'vendorId', 'vendorName' ];
	
	//this whitelist makes it return less Great Plains junk columns we don't need
	protected $visible = [ 'VENDORID', 'vendorDesc', 'vendorShortDesc', 'vendorId', 'vendorName', 'region'];
	

	/**
	 * Get the associated accounts
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function accounts()
	{
		return $this->hasMany('TelcoInvoiceSetup', 'TelcoId', 'VENDORID');
	}

	/**
	 * Seriously people... -_-
	 *
	 * @return string
	 */
	public function getVENDORIDAttribute()
	{
		return trim($this->getAttributeFromArray('VENDORID'));
	}

	/**
	 * Retrieve the composed description of the vendor
	 *
	 * @return string
	 */
	public function getVendorDescAttribute()
	{
		return sprintf
		(
			'%s - %s',
			$this->VENDORID,
			implode
			(
				' ',
				array_filter
				(
					[
						trim($this->VENDNAME),
						trim($this->ADDRESS1),
						trim($this->ADDRESS2),
						trim($this->CITY),
						trim($this->STATE),
						trim($this->ZIPCODE)
					],
					'strlen'
				)
			)
		);
	}
	
	public function getVendorShortDescAttribute()
		{
		return sprintf
		(
			'%s - %s',
			$this->VENDORID,
			trim($this->VENDNAME)
		);
	}		
	
	public function getVendorNameAttribute() {
		return trim($this->getAttributeFromArray('VENDNAME'));
	}
}