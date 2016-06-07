<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class ConstructionStatus extends BaseModel {
	
	protected $table = 'TLG.dbo.ConstructionStatus';

	protected $primaryKey = 'ConstructionStatusId';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
	
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	

	
}
