<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class RemedyAudit extends BaseModel {
	
	protected $table = 'hhcsrv-sbremdb.ARSystem.dbo.CGNT_HD_Help_Desk_Audit_Hist';

	protected $primaryKey = 'HT_Case_ID';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
	
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	

}
