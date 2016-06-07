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
				url:    '/api/vendorLookup?vendor=%QUERY&existing=true',//we only want vendors with accounts for this lookup
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
				url:    '/api/accountLookup?account=',
				replace: function(url, uriEncodedQuery) {
					return url + uriEncodedQuery + '&vendor=' + $('#vendor').val();
				},
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
