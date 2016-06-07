<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group([ 'before' => 'auth' ], function()	{

	Route::get('/', [ 'as' => 'index', 'uses' => 'IndexController@index' ]);

	Route::get('/email/{id}', [ 'as' => 'index', 'uses' => 'IndexController@email' ]);

	Route::get('/regus', function()
	{
		return View::make('emails.regus', []);
	});

	Route::get('/customers', [ 'as' => 'index', 'uses' => 'IndexController@customers' ]);

	Route::get('/orders', [ 'as' => 'index', 'uses' => 'IndexController@orders' ]);

	Route::get('/eDocs/{id}', [ 'as' => 'index', 'uses' => 'FileController@eDocs' ]);

	Route::get('/blocks', [ 'as' => 'index', 'uses' => 'IndexController@blocks' ]);

});

Route::api('v1', function()
{
	Route::group([ 'namespace' => 'Api' ], function()
	{
		Route::get('getIPInfo/{gid}', [ 'as' => 'api.getIPInfo', 'uses' => 'IPInfoController@getIPInfo' ]);
		Route::get('getProjectInfo/{customer}/{level}', [ 'as' => 'api.getIPInfo', 'uses' => 'IPInfoController@getProjectInfo' ]);
		
		Route::get('customer', [ 'as' => 'api.getCustomerInfo', 'uses' => 'PageDataController@getCustomerData' ]);
		
		Route::get('order', [ 'as' => 'api.getOrderInfo', 'uses' => 'PageDataController@getOrderData' ]);
		
		Route::get('block', [ 'as' => 'api.getBlockInfo', 'uses' => 'PageDataController@getBlockData' ]);
		
		Route::get('customerLookup', [ 'as' => 'api.customerLookup', 'uses' => 'AutoController@customerLookup' ]);
		
		Route::get('gidLookup', [ 'as' => 'api.gidLookup', 'uses' => 'AutoController@gidLookup' ]);
	});
});
