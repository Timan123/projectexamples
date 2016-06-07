<?php

use Cogent\Model\TIMModel;
use Cogent\Utility\String;

class TelcoDispute extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.mjain.TelcoDisputes';

	protected $primaryKey = 'DisputeId';
	
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	
	protected $with = [ 'Category'];
	
	const UPDATED_AT = 'LastUpDt';
	
	const CREATED_AT = 'CreatedDt';
	
	const CREATED_BY = 'CreatedUser';
	
	const UPDATED_BY = 'LastUpUser';
	
	//allow mass assignment
	protected $guarded = [];
	
	//automate this int<->int get varchar join
	public function Category()
	{
		return $this->hasOne('TelcoDisputeCategories', 'CategoryId', 'CategoryId');
	}
	
	

	
}