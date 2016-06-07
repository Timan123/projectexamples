<?php

namespace Cogent\Transformer\Api;

use Cogent\Transformer\BaseTransformer;
use Illuminate\Database\Eloquent\Model;

class DisputeTransformer extends BaseTransformer
{
	/**
	 * Transforms the Eloquent model response
	 *
	 * @return array
	 */
	public function transform(Model $model)
	{
		

		$arr = $model->toArray();
		$arr['invoice'] = array_only($arr['invoice'], ['InvoiceDt','IndexNum']);

		return $arr;
	}
	
	
	
	
}