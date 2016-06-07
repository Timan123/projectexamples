<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class RemedyMarkets extends BaseModel {
	
	protected $table = 'Construction.dbo.RemedyMarkets';

	//protected $primaryKey = 'Case_ID';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
	
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	
	public function airportCode() {
		return $this->hasOne('AirportCodes','AirportCode','AirportCode');
	}
	

	
	
}
