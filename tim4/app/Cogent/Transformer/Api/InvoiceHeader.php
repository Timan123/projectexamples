<?php

namespace Cogent\Transformer\Api;

use Cogent\Transformer\BaseTransformer;
use Illuminate\Database\Eloquent\Model;

class InvoiceHeader extends BaseTransformer
{
	/**
	 * Transforms the Eloquent model response
	 *
	 * @return array
	 */
	public function transform(Model $model) {
		$array = $model->toArray();
		array_set($array, 'FieldsLocked', false);
		return $array;
	}
	
	
}