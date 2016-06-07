<?php

namespace Cogent\Transformer\Api;

use Cogent\Transformer\BaseTransformer;
use Illuminate\Database\Eloquent\Model;

class NewLinesAndPonsTransformer extends BaseTransformer
{
	/**
	 * Transforms the Eloquent model response
	 *
	 * @return array
	 */
	public function transform(Model $model)
	{
		$arr = [ 'telcoOrderDetails' => $model->toArray() ];
		
		
		array_set($arr, 'IndexNum', '' );
		//check on zero
		
		$chargeMRC = array_get($arr, 'telcoOrderDetails.chargeMRC');
		$chargeFBCredit = array_get($arr, 'telcoOrderDetails.chargeFBCredit');
		$theLines = [];
		if ($chargeMRC) {
			array_push($theLines, ['Amount' => $chargeMRC, 'Type' => 'MRC', 'Source' => 'Charge', 'Disputed' => 0, 'IndexNum' => '', 'InvIndexNum' => '', 'ChargeIndexNum' => '', 'Notes' => '', 'DisputeId' => null, 'ChargeIndexNum' => '', 'Remaining' => 0]);
		}
		if ($chargeFBCredit) {
			array_push($theLines, ['Amount' => $chargeFBCredit, 'Type' => 'FBCredit', 'Source' => 'Charge', 'Disputed' => 0, 'IndexNum' => '', 'InvIndexNum' => '', 'ChargeIndexNum' => '', 'Notes' => '', 'DisputeId' => null, 'ChargeIndexNum' => '', 'Remaining' => 0]);
		}
		
		array_set($arr, 'telcoBillingLines', $theLines);
		return $arr;
	}
	
	
}