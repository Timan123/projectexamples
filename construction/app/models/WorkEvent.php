<?php

use Cogent\Model\BaseModel;

/**
 * Description of Event
 *
 * @author tcassidy
 */
class WorkEvent extends BaseModel {
	
	protected $table = 'Construction.dbo.WorkEvent';

	protected $primaryKey = 'id';
	
	const UPDATED_AT = null;
	
	const CREATED_AT = null;
	
	//allow mass assignment
	protected $guarded = [];
	
	protected $hidden = ['details'];
		
	//snake_attributes_are_annoying
	public static $snakeAttributes = false;
	
	public function FEs() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasMany('WorkEventFEs', 'WorkId', 'WorkId');
	}
	
	public function markets() {
		// (Other Model, Local Key, Foreign Key)
		return $this->hasMany('WorkEventMarkets', 'WorkId', 'WorkId');
	}
	
	//cancelled does not count as completed but it's also not included in "completed", function below
	public function scopeUncompleted($query) {
		return $query->whereNotIn('Status',['Completed pending FE','Completed with Defect','Completed','cancelled']);
	}
	
	public function scopeCompleted($query) {
		return $query->whereIn('Status',['Completed pending FE','Completed with Defect','Completed']);
	}
	
	public function scopeByMarket($query,$market) {
		return $query->whereHas('markets', function ($query2) use ($market) {
			$query2->where('CogentMarket',$market);
		});
	}
	
	
}
