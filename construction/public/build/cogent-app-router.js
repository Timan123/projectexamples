/**
 * Cogent Communication - Router
 *
 * @author  Shawn Dean <sdean@cogentco.com>
 * @date      2015-05-07
 */
;
window.app = window.app || {};

;(function(app, _)
{
	app.router = (function()
	{
		var router =
		{
			/**
			 * Collection of routes provided by Laravel
			 *
			 * @var  Array
			 */
			ROUTES: [{"host":null,"methods":["GET","HEAD"],"uri":"login","name":"login.form","action":"Cogent\\Controller\\LoginController@form"},{"host":null,"methods":["POST"],"uri":"login","name":"login","action":"Cogent\\Controller\\LoginController@login"},{"host":null,"methods":["GET","HEAD","POST","PUT","PATCH","DELETE"],"uri":"login\/forgot","name":"login.forgot","action":"Cogent\\Controller\\LoginController@forgot"},{"host":null,"methods":["GET","HEAD","POST","PUT","PATCH","DELETE"],"uri":"logout","name":"logout","action":"Cogent\\Controller\\LoginController@logout"},{"host":null,"methods":["GET","HEAD","POST","PUT","PATCH","DELETE"],"uri":"noperms","name":"noperms","action":"Cogent\\Controller\\SystemController@noperms"},{"host":null,"methods":["GET","HEAD"],"uri":"\/","name":null,"action":"Closure"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin","name":"cogmin","action":"Cogent\\Controller\\Cogmin\\IndexController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/users","name":"cogmin.users.listing","action":"Cogent\\Controller\\Cogmin\\UsersController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/users\/{user}\/edit","name":"cogmin.users.edit","action":"Cogent\\Controller\\Cogmin\\UsersController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/users\/{user}\/edit","name":null,"action":"Cogent\\Controller\\Cogmin\\UsersController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/users\/{user}\/login","name":"cogmin.users.login","action":"Cogent\\Controller\\Cogmin\\UsersController@login"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles","name":"cogmin.roles.listing","action":"Cogent\\Controller\\Cogmin\\RolesController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles\/create","name":"cogmin.roles.create","action":"Cogent\\Controller\\Cogmin\\RolesController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/roles\/create","name":null,"action":"Cogent\\Controller\\Cogmin\\RolesController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles\/{role}\/edit","name":"cogmin.roles.edit","action":"Cogent\\Controller\\Cogmin\\RolesController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/roles\/{role}\/edit","name":null,"action":"Cogent\\Controller\\Cogmin\\RolesController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles\/{role}\/delete","name":"cogmin.roles.delete","action":"Cogent\\Controller\\Cogmin\\RolesController@deleteForm"},{"host":null,"methods":["POST"],"uri":"cogmin\/roles\/{role}\/delete","name":null,"action":"Cogent\\Controller\\Cogmin\\RolesController@delete"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions","name":"cogmin.permissions.listing","action":"Cogent\\Controller\\Cogmin\\PermissionsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions\/create","name":"cogmin.permissions.create","action":"Cogent\\Controller\\Cogmin\\PermissionsController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/permissions\/create","name":null,"action":"Cogent\\Controller\\Cogmin\\PermissionsController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions\/{permission}\/edit","name":"cogmin.permissions.edit","action":"Cogent\\Controller\\Cogmin\\PermissionsController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/permissions\/{permission}\/edit","name":null,"action":"Cogent\\Controller\\Cogmin\\PermissionsController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions\/{permission}\/delete","name":"cogmin.permissions.delete","action":"Cogent\\Controller\\Cogmin\\PermissionsController@deleteForm"},{"host":null,"methods":["POST"],"uri":"cogmin\/permissions\/{permission}\/delete","name":null,"action":"Cogent\\Controller\\Cogmin\\PermissionsController@delete"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/logs\/{file?}","name":"cogmin.logs","action":"Cogent\\Controller\\Cogmin\\LogViewerController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"dashboard","name":"dashboard.index","action":"PageController@dashboardPage"},{"host":null,"methods":["GET","HEAD"],"uri":"SOW","name":"inv.SOW","action":"PageController@sowPage"},{"host":null,"methods":["GET","HEAD"],"uri":"prov","name":"inv.prov","action":"PageController@provPage"},{"host":null,"methods":["GET","HEAD"],"uri":"building","name":"inv.building","action":"PageController@buildingPage"},{"host":null,"methods":["GET","HEAD"],"uri":"ticket","name":"inv.ticket","action":"PageController@ticketPage"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getSOWs","name":"api.getSOWs","action":"Api\\EventController@getSOWs"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getSOWsByMarket\/{market}","name":"api.getSOWsByMarket","action":"Api\\EventController@getSOWsByMarket"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getProvsByMarket\/{market}","name":"api.getProvsByMarket","action":"Api\\EventController@getProvsByMarket"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getBuildingsByMarket\/{market}","name":"api.getBuildingsByMarket","action":"Api\\EventController@getBuildingsByMarket"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getMarkets","name":"api.getMarkets","action":"Api\\HelperController@getMarkets"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getFEs","name":"api.getFEs","action":"Api\\HelperController@getFEs"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getSOWRegionCounts","name":"api.getSOWRegionCounts","action":"Api\\SOWController@getSOWRegionCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getProvRegionCounts","name":"api.getProvRegionCounts","action":"Api\\ProvController@getProvRegionCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getProvMarketCounts","name":"api.getProvMarketCounts","action":"Api\\ProvController@getProvMarketCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getSOWMarketCounts","name":"api.getSOWMarketCounts","action":"Api\\SOWController@getSOWMarketCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getProvFECounts","name":"api.getProvFECounts","action":"Api\\ProvController@getProvFECounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getSOWFECounts","name":"api.getSOWFECounts","action":"Api\\SOWController@getSOWFECounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getBuildingRegionCounts","name":"api.getBuildingRegionCounts","action":"Api\\BuildingController@getBuildingRegionCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getBuildingMarketCounts","name":"api.getBuildingMarketCounts","action":"Api\\BuildingController@getBuildingMarketCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getBuildingFECounts","name":"api.getBuildingFECounts","action":"Api\\BuildingController@getBuildingFECounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getTicketCounts","name":"api.getTicketCounts","action":"Api\\TicketController@getTicketCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getTicketMarketCounts","name":"api.getTicketMarketCounts","action":"Api\\TicketController@getTicketMarketCounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getTicketFECounts","name":"api.getTicketFECounts","action":"Api\\TicketController@getTicketFECounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getTicketCloses","name":"api.getTicketCloses","action":"Api\\TicketController@getTicketCloses"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getSOWsDisplay\/{by}\/{type}\/{q}","name":"api.getSOWsDisplay","action":"Api\\SOWController@getSOWsDisplay"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getProvsDisplay\/{by}\/{type}\/{q}","name":"api.getProvsDisplay","action":"Api\\ProvController@getProvsDisplay"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getBuildingsDisplay\/{by}\/{type}\/{q}","name":"api.getBuildingsDisplay","action":"Api\\BuildingController@getBuildingsDisplay"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getTicketsDisplay\/{by}\/{type}\/{q}","name":"api.getTicketsDisplay","action":"Api\\TicketController@getTicketsDisplay"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/test","name":"api.test","action":"Api\\HelperController@test"}],

			/**
			 * Get the URL to a named route.
			 *
			 * @param  string name
			 * @param  Array parameters
			 * @param  PlainObject route
			 * @return  string
			 * @throws  Error
			 */
			route: function(name, parameters, route)
			{
				route = route || this.getByName(name);

				if ( route )
				{
					return this.toRoute(route, parameters);
				}

				throw new Error('Route [' + name + '] not defined.');
			},

			/**
			 * Get the URL for a given route.
			 *
			 * @param  PlainObject route
			 * @param  Array parameters
			 * @return  string
			 */
			toRoute: function(route, parameters)
			{
				var uri   = this.replaceNamedParameters(route.uri, parameters),
					query = this.getRouteQueryString(parameters);

				return '/' + uri.replace(/^\/?/, '') + query;
			},

			/**
			 * Replace all of the named parameters in the path.
			 *
			 * @param  string uri
			 * @param  Array parameters
			 * @return  string
			 */
			replaceNamedParameters: function(uri, parameters)
			{
				return uri.replace(/\{(.*?)(\?)?\}/g, function(match, key, optional)
					{
						var isOptional = _.isEmpty(optional) === false,
							value;

						if ( _.has(parameters, key) )
						{
							value = _.result(parameters, key);

							delete parameters[ key ];
						}
						else if ( isOptional === true )
						{
							value = '';
						}

						return value;
					});
			},

			/**
			 * Get the query string for a given route.
			 *
			 * @param  Array parameters
			 * @return  string
			 */
			getRouteQueryString: function(parameters)
			{
				var query = _( parameters )
					.omit( _.isUndefined )
					.omit( _.isNull )
					.map(function(value, key)
					{
						return key + '=' + value;
					})
					.value();

				if ( query.length < 1 )
				{
					return '';
				}

				return '?' + query.join('&');
			},

			/**
			 * Get a route by its name.
			 *
			 * @param  string name
			 * @return  Object
			 */
			getByName: function(name)
			{
				return _.find(this.ROUTES, { name: name });
			},

			/**
			 * Get a route by its controller action.
			 *
			 * @param  string action
			 * @return  Object
			 */
			getByAction: function(action)
			{
				return _.find(this.ROUTES, { action: action });
			}
		};

		return {
			/**
			 * Retrieve collection of all routes
			 *
			 * @return  Array
			 */
			routes: function()
			{
				return router.ROUTES;
			},

			/**
			 * Generate a url for a given controller action.
			 *
			 * @param  string name
			 * @param  Array parameters
			 * @return  string
			 *
			 * @usage  app.router.action('Controller@method', [ parameters = {} ])
			 */
			action: function(name, parameters)
			{
				parameters = parameters || {};

				return router.route(name, parameters, router.getByAction(name));
			},

			/**
			 * Generate a url for a given named route.
			 *
			 * @param  string name
			 * @param  Array parameters
			 * @return  string
			 *
			 * @usage  app.router.route('routeName', [ parameters = {} ])
			 */
			route: function(route, parameters)
			{
				parameters = parameters || {};

				return router.route(route, parameters);
			},

			/**
			 * Generate a HTML link to the given url.
			 *
			 * @param  string url
			 * @param  string title
			 * @param  PlainObject attributes
			 * @return  string
			 *
			 * @usage  app.router.linkTo('url', [ title = url ], [ attributes = {} ])
			 */
			linkTo: function(url, title, attributes)
			{
				url        = '/' + url.replace(/^\/?/, '');
				title      = title || url;
				attributes = _.map(attributes, function(value, key)
				{
					return key + '="' + value + '"';
				}).join(' ');

				return '<a href="' + url + '" ' + attributes + '>' + title + '</a>';
			},

			/**
			 * Generate a HTML link to the given controller action.
			 *
			 * @param  string action
			 * @param  string title
			 * @param  PlainObject parameters
			 * @param  PlainObject attributes
			 * @return  string
			 *
			 * @usage  app.router.linkToAction('Controller@method', [ title = url ], [ parameters = {} ], [ attributes = {} ])
			 */
			linkToAction: function(action, title, parameters, attributes)
			{
				var url = this.action(action, parameters);

				title      = title || url;
				parameters = parameters || {};
				attributes = attributes || {};

				return this.linkTo(url, title, attributes);
			},

			/**
			 * Generate a HTML link to the given named route.
			 *
			 * @param  string route
			 * @param  string title
			 * @param  PlainObject parameters
			 * @param  PlainObject attributes
			 * @return  string
			 *
			 * @usage  app.router.linkToRoute('routeName', [ title = url ], [ parameters = {} ], [ attributes = {} ])
			 */
			linkToRoute: function(route, title, parameters, attributes)
			{
				var url = this.route(route, parameters);

				title      = title || url;
				parameters = parameters || {};
				attributes = attributes || {};

				return this.linkTo(url, title, attributes);
			}
		};
	}).call(this);

})(window.app, window._);