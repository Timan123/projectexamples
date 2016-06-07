<?php

use Cogent\Model\TIMModel;

class TelcoBillingLines extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.mjain.TelcoBillingLines';

	public static $snakeAttributes = false;
	
	protected $primaryKey = 'IndexNum';
	
	const UPDATED_AT = 'LastUpdt';
	
	const CREATED_AT = 'CreatedDt';
	
	const UPDATED_BY = 'LastUpUser';
	
	const CREATED_BY = 'CreatedUser';
	
	public function dispute() {
		// (Other Model, Foreign Key, Local Key )
		return $this->hasOne('TelcoDispute', 'DisputeId', 'DisputeId');
	}
	
	public function charge() {
		return $this->belongsTo('TelcoCircuitCharges','ChargeIndexNum','IndexNum');
	}
	
	public function invoice() {
		return $this->belongsTo('Invoice','InvIndexNum','IndexNum');
	}
	
}