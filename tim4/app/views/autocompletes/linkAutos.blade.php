var vendorsBH = new Bloodhound(
{
	limit:    50,
	datumTokenizer: function(d)
	{
		return Bloodhound.tokenizers.whitespace(d.vendorId);
	},
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote:
	{
		url:    '/api/vendorLookup?vendor=%QUERY', //any vendor here
		filter: function(json)	{ return json.data;	}
	}
});

vendorsBH.initialize();

$('#vendor')
	.typeahead(
	{
		minLength: 3,
		hint: false
	},
	{
		name:     'vendor-search',
		display:  'vendorId',
		source:   vendorsBH.ttAdapter(),
		templates:
		{
			suggestion: function(d)
			{
				return '<p>' + d.vendorShortDesc + '</p>';
			}
		}
	})
	.on('typeahead:selected typeahead:autocompleted', function(e, val)
	{
		this.vendorId( _.result(val, 'vendorId') );
	}.bind(this));

this.vendorId.subscribe(function(newValue)
{
	$('#vendor').typeahead('val', newValue);
});

var accountsBH = new Bloodhound(
{
	limit:    50,
	datumTokenizer: function(d)
	{
		return Bloodhound.tokenizers.whitespace(d.value);
	},
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote:
	{
		url:    '/api/accountLookup?account=%QUERY',
		filter: function(json)	{ return json;	}
	}
});
accountsBH.initialize();
$('#accountNum')
	.typeahead(
	{
		minLength: 3,
		hint: false
	},
	{
		name:     'account-search',
		display:  'label',
		source:   accountsBH.ttAdapter(),
	})
	.on('typeahead:selected typeahead:autocompleted', function(e, val)
	{
		this.accountNum( _.result(val, 'value') );
	}.bind(this));
var circuitsBH = new Bloodhound(
{
	limit:    50,
	datumTokenizer: function(d)
	{
		return Bloodhound.tokenizers.whitespace(d.value);
	},
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	remote:
	{
		url:    '/api/circuitLookup?circuit=%QUERY',
		filter: function(json)	{ return json;	}
	}
});
circuitsBH.initialize();
$('#circuit')
	.typeahead(
	{
		minLength: 3,
		hint: false
	},
	{
		name:     'circuit-search',
		display:  'label',
		source:   circuitsBH.ttAdapter(),
	})
	.on('typeahead:selected typeahead:autocompleted', function(e, val)
	{
		this.circuit( _.result(val, 'value') );
	}.bind(this));