<?php

use Cogent\Model\TIMModel;

class TelcoOrderDetails extends TIMModel
{
	/**
	 * Use this crazy view because legacy TIM uses it and it provides everything TIM needs in a smaller package
	 */
	protected $table = 'TLG.dbo.V_TelcoCircuitDetails';

	protected $hidden = [ 'NewPON' ];
	
	protected $primaryKey = 'IndexNum';
	
	protected $appends = ['PON', 'OrderId', 'POP', 'CircuitType', 'CircuitClass','AccountNum', 'VendorAccountNum'];
	
	const UPDATED_AT = 'LastUpdateDt';
	
	const CREATED_AT = null;
	
	const UPDATED_BY = 'LastUpdatedBy';
	
	const CREATED_BY = null;
	
	public static $snakeAttributes = false;
	/**
	 * @TODO: Add comments!
	 */
	public function  invoice()
	{
		// (Other Model, Foreign Key, Local Key)
		return $this->belongsTo('TelcoInvoiceSetup', 'TelcoAccNum', 'CogentAccount#');		
	}
	
	
	
	public function getPONAttribute() {
		return $this->NewPON;
	}
	
	public function getOrderIdAttribute() {
		return $this->getAttributeFromArray('Siebel#');
	}
	public function getPOPAttribute() {
		return $this->getAttributeFromArray('POP A');
	}
	public function getCircuitTypeAttribute() {
		return $this->getAttributeFromArray('Circuit Type');
	}
	public function getCircuitClassAttribute() {
		return $this->getAttributeFromArray('Circuit Class');
	}
	public function getAccountNumAttribute() {
		return $this->getAttributeFromArray('CogentAccount#');
	}
	public function setAccountNumAttribute($accountNum) {
		$this->setAttribute('CogentAccount#',$accountNum);
	}
	public function getVendorAccountNumAttribute() {
		return $this->getAttributeFromArray('TelcoAccount#');
	}
	public function setVendorAccountNumAttribute($vendorAccountNum) {
		$this->setAttribute('TelcoAccount#',$vendorAccountNum);
	}

}