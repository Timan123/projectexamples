<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Cogent\Model\BaseModel;
/**
 * Description of Market
 *
 * @author tcassidy
 */
class Market  extends BaseModel {
	protected $table = 'TLG.dbo.CogentMarket';

	protected $primaryKey = 'CogentMarketId';
	
	protected $appends = [ 'CogentMarket' ];
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];

	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	
	public function getCogentMarketAttribute()
	{
		return trim($this->getAttributeFromArray('CogentMarket'));
	}
}
