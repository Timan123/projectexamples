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
class Building  extends BaseModel {
	protected $table = 'TLG.dbo.Buildings';

	protected $primaryKey = 'BoBID';
	
	protected $appends = [ 'CogentMarket', 'ConstructionStatusDesc', 'ConstructionManager' ];
	
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
	
	public function getConstructionStatusDescAttribute()
	{
		return array_get($this->constructionStatus, 'ConstructionStatus');
	}
	
	public function getConstructionManagerAttribute()
	{
		return strtolower($this->getAttributeFromArray('ConstructionManager'));
	}
	
	public function constructionStatus()
	{
		return $this->hasOne('ConstructionStatus', 'ConstructionStatusId', 'ConstructionStatus');
	}
	

	
	
	
	public function scopeNA($query) {
		return $query->where('Continent','North America');
	}
	
	public function scopeEU($query) {
		return $query->where('Continent','Europe');
	}
	
	public function scopeMarket($query, $market) {
		return $query->where('CogentMarket',$market);
	}
	
	public function scopeFE($query, $FE) {
		return $query->where('ConstructionManager',$FE);
	}
}
