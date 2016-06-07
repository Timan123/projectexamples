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

//force no caching so IE will work right
app.ajaxOptions =
	{
		dataType: 'json',
		cache: false
	};

})(window.jQuery, window._, window.app);


//put this on ice for now
//;(function(ko, $, _)
//{
//	/**
//	 * Binding: numberValue like number for values
//	 *
//	 * @usage  { number: value[, decimals = 0, decPoint = '.', thousandsSep = ',' ] }
//	 */
//	ko.bindingHandlers.numberValue =
//	{
//		init: function(element, valueAccessor, allBindingsAccessor)
//		{
//			$(element).value(numberFormat(valueAccessor, allBindingsAccessor));
//		},
//
//		update: function(element, valueAccessor, allBindingsAccessor)
//		{
//			$(element).value(numberFormat(valueAccessor, allBindingsAccessor));
//		}
//	};
//	
//})(window.ko, window.jQuery, window._);



