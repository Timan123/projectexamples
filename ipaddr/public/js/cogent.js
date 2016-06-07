/**
 * Cogent Communications
 *
 * @author Shawn Dean <sdean@cogentco.com>
 * @date   2015-03-11
 */
;

window.app = window.app || {};

;(function($, _, app)
{
	//----------------------------------
	// ViewModel API Handlers
	//----------------------------------

	app.api = app.api || {};
	
	var $body     = $('body');
	$body.find('.datepicker').datepicker(
			{
				startDate: new Date(app.START_YEAR, 1, 1),
				autoclose: true,
				endDate:   null,


			});

	/**
	 * Basic Data APIs
	 */
	_.each([ ], app.registerApiDataEndpoint);

})(window.jQuery, window._, window.app);

