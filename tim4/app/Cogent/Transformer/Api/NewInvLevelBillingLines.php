<?php

namespace Cogent\Transformer\Api;

use Cogent\Transformer\BaseTransformer;
use Illuminate\Database\Eloquent\Model;

class NewInvLevelBillingLines extends BaseTransformer
{
	/**
	 * Transforms the Eloquent model response
	 *
	 * @return array
	 */
	public function transform(Model $model)
	{

		
		$array = array_only($model->toArray(), [ 'Source', 'Type', 'Amount' ]);
		return $array;
	}
	
	
	
	
}