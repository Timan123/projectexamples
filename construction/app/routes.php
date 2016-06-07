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

Route::group([ 'before' => 'auth' ], function()
	{
Route::get('/', function()
	{
		return Redirect::route('dashboard.index');
	});

Roleperms::routeNeedsPermission( 'dashboard', [ 'construction_public_view' ], Redirect::route('noperms'), false );
Roleperms::routeNeedsPermission( 'SOW', [ 'construction_public_view' ], Redirect::route('noperms'), false );
Roleperms::routeNeedsPermission( 'prov', [ 'construction_public_view' ], Redirect::route('noperms'), false );
Roleperms::routeNeedsPermission( 'building', [ 'construction_public_view' ], Redirect::route('noperms'), false );
Roleperms::routeNeedsPermission( 'ticket', [ 'construction_public_view' ], Redirect::route('noperms'), false );
	
	
Route::get('dashboard', ['before' => 'auth', 'as' => 'dashboard.index', 'uses' => 'PageController@dashboardPage' ]);

Route::get('SOW', [ 'as' => 'inv.SOW', 'uses' => 'PageController@sowPage' ]);

Route::get('prov', [ 'as' => 'inv.prov', 'uses' => 'PageController@provPage' ]);

Route::get('building', [ 'as' => 'inv.building', 'uses' => 'PageController@buildingPage' ]);

Route::get('ticket', [ 'as' => 'inv.ticket', 'uses' => 'PageController@ticketPage' ]);

});

Route::api('v1', function()
{
	Route::group([ 'namespace' => 'Api' ], function()
	{
		
		Route::get('getSOWs', [ 'as' => 'api.getSOWs', 'uses' => 'EventController@getSOWs' ]);
		
		Route::get('getSOWsByMarket/{market}', [ 'as' => 'api.getSOWsByMarket', 'uses' => 'EventController@getSOWsByMarket' ]);
		
		Route::get('getProvsByMarket/{market}', [ 'as' => 'api.getProvsByMarket', 'uses' => 'EventController@getProvsByMarket' ]);
		
		Route::get('getBuildingsByMarket/{market}', [ 'as' => 'api.getBuildingsByMarket', 'uses' => 'EventController@getBuildingsByMarket' ]);
		
		Route::get('getMarkets', [ 'as' => 'api.getMarkets', 'uses' => 'HelperController@getMarkets' ]);
		
		Route::get('getFEs', [ 'as' => 'api.getFEs', 'uses' => 'HelperController@getFEs' ]);
		
		Route::get('getSOWRegionCounts', [ 'as' => 'api.getSOWRegionCounts', 'uses' => 'SOWController@getSOWRegionCounts' ]);
		
		Route::get('getProvRegionCounts', [ 'as' => 'api.getProvRegionCounts', 'uses' => 'ProvController@getProvRegionCounts' ]);
		
		Route::get('getProvMarketCounts', [ 'as' => 'api.getProvMarketCounts', 'uses' => 'EventController@getProvMarketCounts' ]);
		
		Route::get('getSOWMarketCounts', [ 'as' => 'api.getSOWMarketCounts', 'uses' => 'EventController@getSOWMarketCounts' ]);
		
		Route::get('getProvFECounts', [ 'as' => 'api.getProvFECounts', 'uses' => 'EventController@getProvFECounts' ]);
		
		Route::get('getSOWFECounts', [ 'as' => 'api.getSOWFECounts', 'uses' => 'EventController@getSOWFECounts' ]);
		
		Route::get('getBuildingRegionCounts', [ 'as' => 'api.getBuildingRegionCounts', 'uses' => 'BuildingController@getBuildingRegionCounts' ]);
		
		Route::get('getBuildingMarketCounts', [ 'as' => 'api.getBuildingMarketCounts', 'uses' => 'BuildingController@getBuildingMarketCounts' ]);
		
		Route::get('getBuildingFECounts', [ 'as' => 'api.getBuildingFECounts', 'uses' => 'BuildingController@getBuildingFECounts' ]);
		
		Route::get('getTicketCounts', [ 'as' => 'api.getTicketCounts', 'uses' => 'TicketController@getTicketCounts' ]);
		
		Route::get('getTicketMarketCounts', [ 'as' => 'api.getTicketMarketCounts', 'uses' => 'TicketController@getTicketMarketCounts' ]);
		
		Route::get('getTicketFECounts', [ 'as' => 'api.getTicketFECounts', 'uses' => 'TicketController@getTicketFECounts' ]);
		
		Route::get('getTicketCloses', [ 'as' => 'api.getTicketCloses', 'uses' => 'TicketController@getTicketCloses' ]);
		
		Route::get('getSOWMarketCounts', [ 'as' => 'api.getSOWMarketCounts', 'uses' => 'SOWController@getSOWMarketCounts' ]);
		
		Route::get('getSOWFECounts', [ 'as' => 'api.getSOWFECounts', 'uses' => 'SOWController@getSOWFECounts' ]);
		
		Route::get('getProvMarketCounts', [ 'as' => 'api.getProvMarketCounts', 'uses' => 'ProvController@getProvMarketCounts' ]);
		
		Route::get('getProvFECounts', [ 'as' => 'api.getProvFECounts', 'uses' => 'ProvController@getProvFECounts' ]);
		
		//individual views here
		Route::get('getSOWsDisplay/{by}/{type}/{q}', [ 'as' => 'api.getSOWsDisplay', 'uses' => 'SOWController@getSOWsDisplay' ]);
		
		Route::get('getProvsDisplay/{by}/{type}/{q}', [ 'as' => 'api.getProvsDisplay', 'uses' => 'ProvController@getProvsDisplay' ]);
		
		Route::get('getBuildingsDisplay/{by}/{type}/{q}', [ 'as' => 'api.getBuildingsDisplay', 'uses' => 'BuildingController@getBuildingsDisplay' ]);
		
		Route::get('getTicketsDisplay/{by}/{type}/{q}', [ 'as' => 'api.getTicketsDisplay', 'uses' => 'TicketController@getTicketsDisplay' ]);
		
		Route::get('test', [ 'as' => 'api.test', 'uses' => 'HelperController@test' ]);
		
		
	});
});