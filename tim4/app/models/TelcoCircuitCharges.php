<?php

use Cogent\Model\TIMModel;

class TelcoCircuitCharges extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.mjain.TelcoCircuitCharges';
	
	protected $primaryKey = 'IndexNum';
	
	const UPDATED_AT = 'LastUpDt';
	
	const CREATED_AT = null;
	
	const UPDATED_BY = 'LastUpUser';
	
	const CREATED_BY = NULL;

	public static $snakeAttributes = false;
	
	public function telcoOrderDetails() {
		// (Other Model, Local Key, Foreign Key )
		return $this->hasOne('TelcoOrderDetails','IndexNum','PON');
		
	}
	
	public function telcoBillingLines() {
		return $this->hasMany('TelcoBillingLines','ChargeIndexNum','IndexNum');
	}
	
	public function openLines() {
		return $this->hasMany('TelcoBillingLines','ChargeIndexNum','IndexNum')->where('Remaining','>',0);
	}
}