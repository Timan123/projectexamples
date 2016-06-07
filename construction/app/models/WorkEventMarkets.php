<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class WorkEventMarkets extends BaseModel {
	
	protected $table = 'Construction.dbo.WorkEventMarkets';

	protected $primaryKey = 'id';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
	
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	


	
	
}
