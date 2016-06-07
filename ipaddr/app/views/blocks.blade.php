@extends('layouts.default')

@section('title')
IPAddr - Blocks
@stop
@section('containerClass')

@stop

<div class="container" id="everythingButResultsDiv">
@section('pageHeaderTitle')
Blocks
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		globalLogoID: {{json_encode($gid)}},
		customerName: null,
		batch: null,
		hitItOnce: false,
		ipBlock: null,
	},
	autoTriggerFilterFormSubmit: false,
	initialize: function(element, event) {
		var gidBH = new Bloodhound(
		{
			limit:    50,
			datumTokenizer: function(d)
			{
				return Bloodhound.tokenizers.whitespace(d);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote:
			{
				url:    '/api/gidLookup?gid=',
				replace: function(url, uriEncodedQuery) {
					return url + uriEncodedQuery;
				},
				filter: function(json)	{ return json;	}
			}
		});
		gidBH.initialize();
		$('#globalLogoID')
			.typeahead(
			{
				minLength: 3,
				hint: false
			},
			{
				name:     'gid-search',
				display:  'value',
				source:   gidBH.ttAdapter(),
			})
			.on('typeahead:selected typeahead:autocompleted', function(e, val)
			{
				this.globalLogoID( _.result(val, 'value') );
			}.bind(this));
		var customersBH = new Bloodhound(
		{
			limit:    50,
			datumTokenizer: function(d)
			{
				return Bloodhound.tokenizers.whitespace(d);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote:
			{
				url:    '/api/customerLookup?customer=',
				replace: function(url, uriEncodedQuery) {
					return url + uriEncodedQuery + '&vendor=' + $('#vendor').val();
				},
				filter: function(json)	{ return json;	}
			}
		});
		customersBH.initialize();
		$('#customerName')
			.typeahead(
			{
				minLength: 3,
				hint: false
			},
			{
				name:     'customer-search',
				display:  'value',
				source:   customersBH.ttAdapter(),
			})
			.on('typeahead:selected typeahead:autocompleted', function(e, val)
			{
				this.customerName( _.result(val, 'value') );
			}.bind(this));
			if (this.globalLogoID()) {
				this.submitFilter();
			}
	},
	submitFilter: function(element, event) {
		
		this.ajaxRequest
			(
				{
					route: 'api.getBlockInfo',
					data:  { globalLogoID: this.globalLogoID(), customerName: this.customerName(), ipBlock: this.ipBlock()}
				},
				function(json)
				{
					this.tableData(json);
					this.hitItOnce(true);	
				},
				this
			);
		return false;
	},
	clear: function(element, event) {
		$('#filterForm').trigger('reset');
		this.tableData([]);
		this.hitItOnce(false);
	},
});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent

<form action="/blocks" method="get" id="filterForm" data-no-api-prefix="1" data-bind="submit: $root.submitFilter.bind($root)">
	
	<div class="row">
		<div class="col-md-2 form-group">
			<label class="control-label" for="globalLogoID">GlobalLogoID:</label>
			<input type="text" name="globalLogoID" id="globalLogoID" class="form-control input-sm typeahead" data-bind="value: globalLogoID"/>
		</div>
		<div class="col-md-2 form-group ">
			<label class="control-label" for="customerName">Customer Name*:</label>
			<input type="text" name="customerName" id="customerName" class="form-control input-sm typeahead" data-bind="value: customerName"/>
		</div>
		<div class="col-md-2 form-group ">
			<label class="control-label" for="batch">IP Block*:</label>
			<input type="text" name="ipBlock" id="ipBlock" class="form-control input-sm" data-bind="value: ipBlock"/>
		</div>
		
	</div>
	<div class="row">


		<div class="col-md-4 form-group">
		<button type="submit" class="btn btn-primary" name="Submit" value="Submit" >
		<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
		Submit
		</button>&nbsp;&nbsp;* = Partial Matching
		</div>
		<button  type="button" class="btn btn-primary pull-right" value="Clear" name="Clear" data-bind='click: $root.clear.bind($root)'>Clear</button>
	</div>
</div>
</form>
</div>
<br>
<div class="left-container">
<table class="table table-striped table-responsive table-condensed small" style="text-align: left" id="dataTable">
	<thead>

		<th data-bind="orderable: { collection: 'tableData', field: 'id'}">id</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'GlobalLogoID'}">GlobalLogoID</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'OrderId'}">OrderId</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'Address'}">Block</th>
	</thead>
	<tbody data-bind="foreach: tableData, visible: tableData().length > 0">
		<tr>
			<td><span data-bind="text: $data.id"></span></td>
			<td><a data-bind="attr: { href: 'customers?gid=' + $data.GlobalLogoID}, text: $data.GlobalLogoID"></a></td>
			<td><span data-bind="text: $data.OrderId"></span></td>
			<td><span data-bind="text: $data.Address"></span></td>
		</tr>
	</tbody>
	<tbody data-bind="visible: tableData().length == 0 && $root.hitItOnce()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>
</div>


@stop