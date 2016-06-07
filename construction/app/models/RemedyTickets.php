<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class RemedyTickets extends BaseModel {
	
	protected $table = 'Construction.dbo.DispatchRemTickets';

	protected $primaryKey = 'id';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
	
	protected $appends = ['statusDesc'];
	
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	
	public function dispatchEmails() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasMany('DispatchEmails', 'Case_ID', 'Case_ID');
	}
	
	public function mostRecentDispatchEmail() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasOne('DispatchEmails', 'Case_ID', 'Case_ID')->orderBy('Date_Sent','desc');
	}
	
	public function getStatusDescAttribute()
	{
		$code = $this->getAttributeFromArray('Status');
		switch ($code) {
			case 0:
				return 'New';
				break;
			case 1:
				return 'Assigned';
				break;
			case 2:
				return 'Work In Progress';
				break;
			case 3:
				return 'Pending';
				break;
			case 4:
				return 'Resolved';
				break;
			case 5:
				return 'Closed';
				break;
			case 6:
				return 'Rejected';
				break;
			default:
				return 'Locked';
				
		}
	}
	
	public function remedyMarkets() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasMany('RemedyMarkets', 'Case_ID', 'Case_ID');
	}
	
	public function audit() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasMany('RemedyAudit', 'HT_Case_ID', 'Case_ID');
	}
	
	public function scopeByMarket($query,$market) {
		return $query->whereHas('RemedyMarkets', function ($query2) use ($market) {
			$query2->where('CogentMarket',$market);
		});
	}
	
	public function scopeByFE($query,$FE) {
		return $query->where('FE_Assigned', $FE);
	}
	
}
