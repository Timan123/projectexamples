<?php

namespace Cogent\Transformer\Api;

use Cogent\Transformer\BaseTransformer;
use Illuminate\Database\Eloquent\Model;

class Vendor extends BaseTransformer
{
	/**
	 * Transforms the Eloquent model response
	 *
	 * @return array
	 */
	public function transform(Model $model)
	{
//		$accounts = $model->accounts
//			->filter(function($entry)
//			{
//				// When set, it means it found a match result based on the criteria
//				return $entry->getRelation('orderDetails');
//			})
//			->map(function($entry)
//			{
//				//echo ddd($entry->latestInvoice);
//				return [ 'telcoAccNum' => $entry->TelcoAccNum, 'latestInvoiceDt' => $entry->latestInvoice['InvoiceDt'] ];
//			});
		
		$array = array_only($model->toArray(), [ 'vendorDesc', 'region' ]);

		//array_set($array, 'accounts', $accounts->all());

		return $array;
	}
	
	
	
	
}