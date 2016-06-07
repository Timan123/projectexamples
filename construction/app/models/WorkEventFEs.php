<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class WorkEventFEs extends BaseModel {
	
	protected $table = 'Construction.dbo.WorkEvent_FEs';

	protected $primaryKey = 'id';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
		
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	
	
		
}
