<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class AirportCodes extends BaseModel {
	
	protected $table = 'Construction.dbo.AirportCodes';

	//protected $primaryKey = 'Case_ID';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
	
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	

	
}
