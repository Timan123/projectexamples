<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class ProvWorkEvent extends BaseModel {
	
	protected $table = 'Construction.dbo.ProvWorkEvent';

	protected $primaryKey = 'id';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	protected $appends = [ 'ByUser', 'StatusCode' ];
	
	//allow mass assignment
	protected $guarded = [];
		
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	
	public function getByUserAttribute()
	{
		return trim($this->getAttributeFromArray('ByUser'));
	}
	
	public function getStatusCodeAttribute()
	{
		return array_get($this->order, 'StatusCode');
	}
	
	public function FEs() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasMany('ProvWorkEventFEs', 'ProvId', 'id');
	}
	
	public function order() {
		return $this->hasOne('Order','OrderId','OrderId');
	}
}
