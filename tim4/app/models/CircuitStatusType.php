<?php

use Cogent\Model\TIMModel;
use Cogent\Utility\String;

class CircuitStatusType extends TIMModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'TLG.mjain.CircuitStatusType_tbl';

	protected $visible = [ 'CircuitStatusDesc' ];
	
	protected $primaryKey = 'CircuitStatusCode';



}