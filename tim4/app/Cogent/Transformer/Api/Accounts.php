<?php

namespace Cogent\Transformer\Api;

use Cogent\Transformer\BaseTransformer;
use Illuminate\Database\Eloquent\Model;

class Accounts extends BaseTransformer
{
	/**
	 * Transforms the Eloquent model response
	 *
	 * @return array
	 */
	public function transform(Model $model)
	{
		
		//return ddd($model);
//		$model = $model->each(function($entry)
//			{
//				//echo ddd($entry->latestInvoice);
//				return $entry;
//			});
		
//		$array = $model->toArray();
//		$array = array_map(function($entry) {
//			$entry->latestInvoice = $entry->latestInvoice['InvoiceDt'];
//			return $entry;
//		}, $array);
		return $model->toArray();

		//return $accounts;
	}
	
	
	
	
}