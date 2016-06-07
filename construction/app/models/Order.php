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
class Order extends BaseModel {
	protected $table = 'TLG.mjain.Order_Prov_Details';

	protected $primaryKey = 'OrderId';
	
	//we only need the status, keep extra stuff from returning
	protected $visible = [ 'StatusCode'];
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];

	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	

}
