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
	
	Route::get('/', function()
	{
		return Redirect::route('main.index');
	});

	//Route::get('testPage','PageController@testPage');


	Roleperms::routeNeedsPermission( 'main', [ 'tim_public_view' ], Redirect::route('noperms'), false );
	Roleperms::routeNeedsPermission( 'link', [ 'tim_public_view' ], Redirect::route('noperms'), false );
	Roleperms::routeNeedsPermission( 'disputes', [ 'tim_public_view' ], Redirect::route('noperms'), false );
	Roleperms::routeNeedsPermission( 'invAssign', [ 'tim_public_view' ], Redirect::route('noperms'), false );
	Roleperms::routeNeedsPermission( 'invListing', [ 'tim_public_view' ], Redirect::route('noperms'), false );
	Roleperms::routeNeedsPermission( 'report', [ 'tim_public_view' ], Redirect::route('noperms'), false );
	Roleperms::routeNeedsPermission( 'deleteInv', [ 'tim_admin_update_invoices' ], Redirect::route('noperms'), false );

	Route::group([ 'prefix' => 'main' ], function() {
		Route::get('/', [ 'as' => 'main.index', 'uses' => 'PageController@mainPage' ]);
		Route::post('/', [ 'as' => 'main.index', 'uses' => 'SubmitController@updateInvoice' ]);
		Route::post('/submitDispute', [ 'as' => 'dispute.index', 'uses' => 'SubmitController@submitDispute' ]);
	});
	
	Route::group([ 'prefix' => 'link' ], function() {
		Route::get('/', [ 'as' => 'link.index', 'uses' => 'PageController@linkPage' ]);
		Route::post('/', [ 'as' => 'link.index', 'uses' => 'SubmitController@updateLink' ]);
	});

	Route::group([ 'prefix' => 'disputes' ], function() {
		Route::get('/', [ 'as' => 'disputes.index', 'uses' => 'PageController@disputesPage' ]);
		Route::post('/', [ 'as' => 'disputes.index', 'uses' => 'SubmitController@updateDisputes' ]);
	});

	Route::group([ 'prefix' => 'invAssign' ], function() {
		Route::get('/', [ 'as' => 'invAssign.index', 'uses' => 'PageController@invAssign' ]);
		Route::post('/', [ 'as' => 'invAssign.index', 'uses' => 'SubmitController@assignAndCreateInv' ]);
	});

	Route::get('deleteInv', [ 'as' => 'deleteInv.index', 'uses' => 'PageController@deleteInv' ]);

	Route::get('invListing', [ 'as' => 'invListing.index', 'uses' => 'PageController@invListing' ]);

	Route::get('kwikTag', [ 'as' => 'kwikTag.index', 'uses' => 'PageController@kwikTagPage' ]);

	Route::get('report', [ 'as' => 'report.index', 'uses' => 'PageController@reportPage' ]);

	Route::get('search', [ 'as' => 'search.index', 'uses' => 'PageController@searchPage' ]);

});

// ----------------------------------------------------------
// API
// ----------------------------------------------------------

Route::api('v1', function()
{
	Route::group([ 'namespace' => 'Api' ], function()
	{
		Route::group([ 'prefix' => 'invoiceSearch' ], function()
		{
			Route::get('/', [ 'as' => 'api.invoiceSearch', 'uses' => 'InvoiceController@invoiceSearch' ]);
		});

		Route::group([ 'prefix' => 'timReport' ], function()
		{
			Route::get('/', [ 'as' => 'api.timReport', 'uses' => 'InvoiceController@timReport' ]);
		});

		Route::group([ 'prefix' => 'invoices' ], function()
		{
			Route::get('/{accountNum}', [ 'as' => 'api.invoices.account', 'uses' => 'InvoiceController@invoices' ]);
		});

		Route::group([ 'prefix' => 'currentInvoice' ], function()
		{
			Route::get('/{indexNum}', [ 'as' => 'api.currentInvoice', 'uses' => 'InvoiceController@getCurrentInvoice' ]);
		});

		Route::group([ 'prefix' => 'newLinesAndPons' ], function()
		{
			Route::get('/{accountNum}', [ 'as' => 'api.newLinesAndPons', 'uses' => 'InvoiceController@newLinesAndPons' ]);
		});

		Route::get('/test/{accountNum}', [ 'uses' => 'InvoiceController@test' ]);

		Route::group([ 'prefix' => 'linesAndPons' ], function()
		{
			Route::get('/{indexNum}', [ 'as' => 'api.linesAndPons', 'uses' => 'InvoiceController@getLinesAndPons' ]);
		});

		Route::get('getOldDisputes/{pon}', [ 'as' => 'api.getOldDisputes', 'uses' => 'DisputeController@getOldDisputesByPon' ]);

		Route::get('getOldDisputesInv/{accountNum}', [ 'as' => 'api.getOldDisputesInv', 'uses' => 'DisputeController@getOldInvoiceLevelDisputes' ]);

		Route::get('getOldDisputes/{pon}', [ 'as' => 'api.getOldDisputes', 'uses' => 'DisputeController@getOldDisputesByPon' ]);

		Route::get('getDisputesByAccountNum/{accountNum}', [ 'as' => 'api.getDisputesByAccountNum', 'uses' => 'DisputeController@getDisputesByAccountNum' ]);


		Route::group([ 'prefix' => 'accounts' ], function()
		{
			Route::get('/', [ 'as' => 'api.accounts', 'uses' => 'AccountsController@accounts' ]);
		});


		Route::group([ 'prefix' => 'vendor' ], function()
		{
			Route::get('/', [ 'as' => 'api.vendor', 'uses' => 'VendorController@vendor' ]);
		});

		Route::get('newInvLevelBillingLines/{invIndexNum}', [ 'as' => 'api.newInvLevelBillingLines', 'uses' => 'InvoiceController@newInvLevelBillingLines' ]);

		Route::get('deleteInv/{gpInvoiceNum}', [ 'as' => 'api.deleteInv', 'uses' => 'InvoiceController@deleteInv' ]);

		//for autocompletes
		Route::get('vendorLookup', 'VendorController@vendorLookup');
		Route::get('accountLookup', 'AccountsController@accountLookup');
		Route::get('circuitLookup', 'CircuitController@circuitLookup');

		Route::get('disputes', 'DisputeController@getAllDisputes');

		Route::get('getTimAnalysts', [ 'as' => 'api.getTimAnalysts', 'uses' => 'AccountsController@getTimAnalysts' ]);

		Route::get('getAllVendors', [ 'as' => 'api.getAllVendors', 'uses' => 'VendorController@getAllVendors' ]);

		//accountCheck
		Route::group([ 'prefix' => 'accountCheck' ], function()
		{
			Route::get('/', [ 'as' => 'api.accountCheck', 'uses' => 'AccountsController@accountCheck' ]);
		});

		//circuitCheck
		Route::group([ 'prefix' => 'circuitCheck' ], function()
		{
			Route::get('/', [ 'as' => 'api.circuitCheck', 'uses' => 'CircuitController@circuitCheck' ]);
		});

		Route::get('getInvoiceStatuses', [ 'as' => 'api.getInvoiceStatuses', 'uses' => 'InvoiceController@getInvoiceStatuses' ]);
		Route::get('getCategories', [ 'as' => 'api.getCategories', 'uses' => 'DisputeController@getCategories' ]);
		//dd for dropdowns
		Route::group([ 'prefix' => 'dd' ], function() {
			Route::get('/telcoNames', [ 'as' => 'api.telcoNames', 'uses' => 'CircuitController@telcoNames' ]);
			Route::get('/circuitStatuses', [ 'as' => 'api.circuitStatuses', 'uses' => 'CircuitController@circuitStatuses' ]);
			Route::get('/circuitTypes', [ 'as' => 'api.circuitTypes', 'uses' => 'CircuitController@circuitTypes' ]);
			Route::get('/circuitClasses', [ 'as' => 'api.circuitClasses', 'uses' => 'CircuitController@circuitClasses' ]);
			Route::get('/orderCurrencies', [ 'as' => 'api.orderCurrencies', 'uses' => 'CircuitController@orderCurrencies' ]);
			Route::get('/telcoSpeeds', [ 'as' => 'api.telcoSpeeds', 'uses' => 'CircuitController@telcoSpeeds' ]);
			Route::get('/serviceCoordinators', [ 'as' => 'api.serviceCoordinators', 'uses' => 'CircuitController@serviceCoordinators' ]);
		});

		Route::get('bigSearch', [ 'as' => 'api.bigSearch', 'uses' => 'CircuitController@bigSearch' ]);
	});
});
