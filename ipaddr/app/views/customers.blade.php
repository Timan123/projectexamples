@extends('layouts.default')

@section('title')
IPAddr - Customers
@stop
@section('containerClass')

@stop

<div class="container" id="everythingButResultsDiv">
@section('pageHeaderTitle')
Customers
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
		firstFrom: null,
		firstTo: null,
		billFrom: null,
		billTo: null,
		AM: null,
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
					route: 'api.getCustomerInfo',
					data:  { globalLogoID: this.globalLogoID(), 
							customerName: this.customerName(), 
							firstFrom: this.firstFrom(),
							firstTo: this.firstTo(),
							billFrom: this.billFrom(),
							billTo: this.billTo(),
							AM: this.AM()}
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
	//override this to deal with leading zeros on relevant columns
	//override this to deal with leading zeros on relevant columns
	exportCsv: function(options)
	{
		var exportTableOptions = $.extend({},
			{
				ignoreColumn: [6,7,8],
				filename: 'IPv4Customers'
			}, options);
		this.dataTable.exportTable(exportTableOptions);
	},
});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent


<form action="/customers" method="get" id="filterForm" data-no-api-prefix="1" data-bind="submit: $root.submitFilter.bind($root)">
	
	<div class="row">
		<div class="col-md-2 form-group">
			<label class="control-label" for="globalLogoID">GlobalLogoID:</label>
			<input type="text" name="globalLogoID" id="globalLogoID" class="form-control input-sm typeahead" data-bind="value: globalLogoID"/>
		</div>
		<div class="col-md-2 form-group ">
			<label class="control-label" for="customerName">Customer Name*:</label>
			<input type="text" name="customerName" id="customerName" class="form-control input-sm typeahead" data-bind="value: customerName"/>
		</div>
		<div class="col-md-3 form-group ">
			<label class="control-label" >Email By Date:</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm datepicker" title="From" data-bind="tooltip, value: firstFrom, datePicker">
				<span class="input-group-addon">to</span>
				<input type="text" class="form-control input-sm datepicker" title="To" data-bind="tooltip, value: firstTo, datePicker">
			</div>
		</div>
		<div class="col-md-3 form-group ">
			<label class="control-label" >Bill Date:</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm datepicker" title="From" data-bind="tooltip, value: billFrom, datePicker">
				<span class="input-group-addon">to</span>
				<input type="text" class="form-control input-sm datepicker" title="To" data-bind="tooltip, value: billTo, datePicker">
				
			</div>
		</div>
		<div class="col-md-2 form-group">
			<label class="control-label" for="AM">Account Manager:</label>
			<input type="text" name="AM" id="AM" class="form-control input-sm" data-bind="value: AM"/>
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
		<th data-bind="orderable: { collection: 'tableData', field: 'CreatedOrder'}">CreatedOrder</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'CustomerName'}">CustomerName</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'LatestPosEmailDt'}">EmailByDate</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'EmailSent'}">EmailSent</th>
		<th >Emails</th>
		<th >Orders</th>
		<th >Blocks</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'EffFirstBillDt'}">EffFirstBillDt</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'EffLastBillDt'}">EffLastBillDt</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'AM'}">AM</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'MGR'}">MGR</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'NumPorts'}">NumPorts</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'DiscountSize'}">DiscountSize</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'MRC'}">MRC</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'CustIPCount'}">CustIPCount</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'IPCount'}">IPCount</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'BasisOrder'}">BasisOrder</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'TotalCDR'}">TotalCDR</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'ImportStep'}">ImportStep</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'SetupStep'}">SetupStep</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'Region'}">Region</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'LineInsert'}">LineInsert</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'CCDBInsert'}">CCDBInsert</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'QuotedCurrency'}">QuotedCurrency</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'SFReassign'}">SFReassign</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'ExistingAllocOrder'}">ExistingAllocOrder</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'GotRider'}">GotRider</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'Recips'}">Recips</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'Batch'}">Batch</th>
		<th data-bind="orderable: { collection: 'tableData', field: 'CorrectionEmail'}">CorrectionEmail</th>
	</thead>
	<tbody data-bind="foreach: tableData, visible: tableData().length > 0">
		<tr>
			<td><span data-bind="text: $data.id"></span></td>
			<td><span data-bind="text: $data.GlobalLogoID"></span></td>
			<td><span data-bind="text: $data.CreatedOrder"></span></td>
			<td><span data-bind="text: $data.CustomerName"></span></td>
			<td><span data-bind="text: $data.LatestPosEmailDt"></span></td>
			<td><span data-bind="text: $data.EmailSent"></span></td>
			<td><a data-bind="attr: { href: 'email/' + $data.id}, text: 'Email'" target='_email'></a></td>
			<td><a data-bind="attr: { href: 'orders?gid=' + $data.GlobalLogoID}, text: 'Orders'"></a></td>
			<td><a data-bind="attr: { href: 'blocks?gid=' + $data.GlobalLogoID}, text: 'Blocks'"></a></td>
			<td><span data-bind="text: $data.EffFirstBillDt"></span></td>
			<td><span data-bind="text: $data.EffLastBillDt"></span></td>
			<td><span data-bind="text: $data.AM"></span></td>
			<td><span data-bind="text: $data.MGR"></span></td>
			<td><span data-bind="text: $data.NumPorts"></span></td>
			<td><span data-bind="text: $data.DiscountSize"></span></td>
			<td><span data-bind="number: $data.MRC, decimals: 2"></span></td>
			<td><span data-bind="text: $data.CustIPCount"></span></td>
			<td><span data-bind="text: $data.IPCount"></span></td>
			<td><span data-bind="text: $data.BasisOrder"></span></td>
			<td><span data-bind="text: $data.TotalCDR"></span></td>
			<td><span data-bind="text: $data.ImportStep"></span></td>
			<td><span data-bind="text: $data.SetupStep"></span></td>
			<td><span data-bind="text: $data.Region"></span></td>
			<td><span data-bind="text: $data.LineInsert"></span></td>
			<td><span data-bind="text: $data.CCDBInsert"></span></td>
			<td><span data-bind="text: $data.QuotedCurrency"></span></td>
			<td><span data-bind="text: $data.SFReassign"></span></td>
			
			<td><span data-bind="text: $data.ExistingAllocOrder"></span></td>
			<td><span data-bind="text: $data.GotRider"></span></td>
			<td><span data-bind="text: _.trunc($data.Recips, 20)"></td>
			<td><span data-bind="text: $data.Batch"></span></td>
			<td><span data-bind="text: $data.CorrectionEmail"></span></td>
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