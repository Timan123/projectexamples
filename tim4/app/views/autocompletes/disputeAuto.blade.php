
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
					return url + uriEncodedQuery + '&vendor=';
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