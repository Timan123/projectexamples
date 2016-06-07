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
			ROUTES: [{"host":null,"methods":["GET","HEAD"],"uri":"login","name":"login.form","action":"Cogent\\Controller\\LoginController@form"},{"host":null,"methods":["POST"],"uri":"login","name":"login","action":"Cogent\\Controller\\LoginController@login"},{"host":null,"methods":["GET","HEAD","POST","PUT","PATCH","DELETE"],"uri":"login\/forgot","name":"login.forgot","action":"Cogent\\Controller\\LoginController@forgot"},{"host":null,"methods":["GET","HEAD","POST","PUT","PATCH","DELETE"],"uri":"logout","name":"logout","action":"Cogent\\Controller\\LoginController@logout"},{"host":null,"methods":["GET","HEAD","POST","PUT","PATCH","DELETE"],"uri":"noperms","name":"noperms","action":"Cogent\\Controller\\SystemController@noperms"},{"host":null,"methods":["GET","HEAD"],"uri":"\/","name":null,"action":"Closure"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin","name":"cogmin","action":"Cogent\\Controller\\Cogmin\\IndexController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/users","name":"cogmin.users.listing","action":"Cogent\\Controller\\Cogmin\\UsersController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/users\/{user}\/edit","name":"cogmin.users.edit","action":"Cogent\\Controller\\Cogmin\\UsersController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/users\/{user}\/edit","name":null,"action":"Cogent\\Controller\\Cogmin\\UsersController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/users\/{user}\/login","name":"cogmin.users.login","action":"Cogent\\Controller\\Cogmin\\UsersController@login"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles","name":"cogmin.roles.listing","action":"Cogent\\Controller\\Cogmin\\RolesController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles\/create","name":"cogmin.roles.create","action":"Cogent\\Controller\\Cogmin\\RolesController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/roles\/create","name":null,"action":"Cogent\\Controller\\Cogmin\\RolesController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles\/{role}\/edit","name":"cogmin.roles.edit","action":"Cogent\\Controller\\Cogmin\\RolesController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/roles\/{role}\/edit","name":null,"action":"Cogent\\Controller\\Cogmin\\RolesController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/roles\/{role}\/delete","name":"cogmin.roles.delete","action":"Cogent\\Controller\\Cogmin\\RolesController@deleteForm"},{"host":null,"methods":["POST"],"uri":"cogmin\/roles\/{role}\/delete","name":null,"action":"Cogent\\Controller\\Cogmin\\RolesController@delete"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions","name":"cogmin.permissions.listing","action":"Cogent\\Controller\\Cogmin\\PermissionsController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions\/create","name":"cogmin.permissions.create","action":"Cogent\\Controller\\Cogmin\\PermissionsController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/permissions\/create","name":null,"action":"Cogent\\Controller\\Cogmin\\PermissionsController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions\/{permission}\/edit","name":"cogmin.permissions.edit","action":"Cogent\\Controller\\Cogmin\\PermissionsController@form"},{"host":null,"methods":["POST"],"uri":"cogmin\/permissions\/{permission}\/edit","name":null,"action":"Cogent\\Controller\\Cogmin\\PermissionsController@save"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/permissions\/{permission}\/delete","name":"cogmin.permissions.delete","action":"Cogent\\Controller\\Cogmin\\PermissionsController@deleteForm"},{"host":null,"methods":["POST"],"uri":"cogmin\/permissions\/{permission}\/delete","name":null,"action":"Cogent\\Controller\\Cogmin\\PermissionsController@delete"},{"host":null,"methods":["GET","HEAD"],"uri":"cogmin\/logs\/{file?}","name":"cogmin.logs","action":"Cogent\\Controller\\Cogmin\\LogViewerController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"main","name":"main.index","action":"PageController@mainPage"},{"host":null,"methods":["POST"],"uri":"main","name":"main.index","action":"SubmitController@updateInvoice"},{"host":null,"methods":["POST"],"uri":"main\/submitDispute","name":"dispute.index","action":"SubmitController@submitDispute"},{"host":null,"methods":["GET","HEAD"],"uri":"link","name":"link.index","action":"PageController@linkPage"},{"host":null,"methods":["POST"],"uri":"link","name":"link.index","action":"SubmitController@updateLink"},{"host":null,"methods":["GET","HEAD"],"uri":"disputes","name":"disputes.index","action":"PageController@disputesPage"},{"host":null,"methods":["POST"],"uri":"disputes","name":"disputes.index","action":"SubmitController@updateDisputes"},{"host":null,"methods":["GET","HEAD"],"uri":"invAssign","name":"invAssign.index","action":"PageController@invAssign"},{"host":null,"methods":["POST"],"uri":"invAssign","name":"invAssign.index","action":"SubmitController@assignAndCreateInv"},{"host":null,"methods":["GET","HEAD"],"uri":"deleteInv","name":"deleteInv.index","action":"PageController@deleteInv"},{"host":null,"methods":["GET","HEAD"],"uri":"invListing","name":"invListing.index","action":"PageController@invListing"},{"host":null,"methods":["GET","HEAD"],"uri":"kwikTag","name":"kwikTag.index","action":"PageController@kwikTagPage"},{"host":null,"methods":["GET","HEAD"],"uri":"report","name":"report.index","action":"PageController@reportPage"},{"host":null,"methods":["GET","HEAD"],"uri":"search","name":"search.index","action":"PageController@searchPage"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/invoiceSearch","name":"api.invoiceSearch","action":"Api\\InvoiceController@invoiceSearch"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/timReport","name":"api.timReport","action":"Api\\InvoiceController@timReport"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/invoices\/{accountNum}","name":"api.invoices.account","action":"Api\\InvoiceController@invoices"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/currentInvoice\/{indexNum}","name":"api.currentInvoice","action":"Api\\InvoiceController@getCurrentInvoice"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/newLinesAndPons\/{accountNum}","name":"api.newLinesAndPons","action":"Api\\InvoiceController@newLinesAndPons"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/test\/{accountNum}","name":null,"action":"Api\\InvoiceController@test"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/linesAndPons\/{indexNum}","name":"api.linesAndPons","action":"Api\\InvoiceController@getLinesAndPons"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getOldDisputes\/{pon}","name":"api.getOldDisputes","action":"Api\\DisputeController@getOldDisputesByPon"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getOldDisputesInv\/{accountNum}","name":"api.getOldDisputesInv","action":"Api\\DisputeController@getOldInvoiceLevelDisputes"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getDisputesByAccountNum\/{accountNum}","name":"api.getDisputesByAccountNum","action":"Api\\DisputeController@getDisputesByAccountNum"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/accounts","name":"api.accounts","action":"Api\\AccountsController@accounts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/vendor","name":"api.vendor","action":"Api\\VendorController@vendor"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/newInvLevelBillingLines\/{invIndexNum}","name":"api.newInvLevelBillingLines","action":"Api\\InvoiceController@newInvLevelBillingLines"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/deleteInv\/{gpInvoiceNum}","name":"api.deleteInv","action":"Api\\InvoiceController@deleteInv"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/vendorLookup","name":null,"action":"Api\\VendorController@vendorLookup"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/accountLookup","name":null,"action":"Api\\AccountsController@accountLookup"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/circuitLookup","name":null,"action":"Api\\CircuitController@circuitLookup"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/disputes","name":null,"action":"Api\\DisputeController@getAllDisputes"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getTimAnalysts","name":"api.getTimAnalysts","action":"Api\\AccountsController@getTimAnalysts"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getAllVendors","name":"api.getAllVendors","action":"Api\\VendorController@getAllVendors"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/accountCheck","name":"api.accountCheck","action":"Api\\AccountsController@accountCheck"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/circuitCheck","name":"api.circuitCheck","action":"Api\\CircuitController@circuitCheck"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getInvoiceStatuses","name":"api.getInvoiceStatuses","action":"Api\\InvoiceController@getInvoiceStatuses"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/getCategories","name":"api.getCategories","action":"Api\\DisputeController@getCategories"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/dd\/telcoNames","name":"api.telcoNames","action":"Api\\CircuitController@telcoNames"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/dd\/circuitStatuses","name":"api.circuitStatuses","action":"Api\\CircuitController@circuitStatuses"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/dd\/circuitTypes","name":"api.circuitTypes","action":"Api\\CircuitController@circuitTypes"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/dd\/circuitClasses","name":"api.circuitClasses","action":"Api\\CircuitController@circuitClasses"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/dd\/orderCurrencies","name":"api.orderCurrencies","action":"Api\\CircuitController@orderCurrencies"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/dd\/telcoSpeeds","name":"api.telcoSpeeds","action":"Api\\CircuitController@telcoSpeeds"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/dd\/serviceCoordinators","name":"api.serviceCoordinators","action":"Api\\CircuitController@serviceCoordinators"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/bigSearch","name":"api.bigSearch","action":"Api\\CircuitController@bigSearch"}],

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