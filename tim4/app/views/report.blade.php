@extends('layouts.default')

@section('title')
TIM - Report
@stop
@section('containerClass')
container big-container
@stop

@section('pageHeaderTitle')
TIM Report
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		lastUpdatedBy: null,
		invoiceDtFrom: null,
		invoiceDtTo: null,
		invoiceStatus: 4, //default this to submmited=4
		hitItOnce: false,
		createdDtFrom: null,
		createdDtTo: null,
		invoiceStatuses: [],
		analystList: [],
		regions: [{value: 'US'}, {value: 'CA'}],
		region: 'US' //default to US
	},
	initialize: function() {

	},
	dependencies: [{ observable: 'invoiceStatuses', route: 'api.getInvoiceStatuses', cache: true},
					{ observable: 'analystList', route: 'api.getTimAnalysts', cache: true}],
	autoTriggerFilterFormSubmit: false,
	submitFilter: function(element, event) {
		this.ajaxRequest
			(
				{
					route: 'api.timReport',
					data:  { invoiceDtFrom: this.invoiceDtFrom(), invoiceDtTo: this.invoiceDtTo(), invoiceStatus: this.invoiceStatus(), region: this.region(), lastUpdatedBy: this.lastUpdatedBy() }
				},
				function(json)
				{
					this.bindAjaxResponse(json);
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
		this.region('US');
		this.invoiceStatus(null);
		
	},
	setRegion: function(element, event) {
		var region = this.region();
	},
		//override this to deal with leading zeros on relevant columns
		exportCsv: function(options)
		{
			var exportTableOptions = $.extend({},
				{
					filename: _.slugify( this.pageHeader.text() )
				}, options);
				exportTableOptions.tbodyRowCallback = function(csvRow, rowElement, rowIndex, prepareColumn)
				{
					csvRow[2] = '=' + csvRow[2];
					csvRow[3] = '=' + csvRow[3];
					return csvRow;
				};
			this.dataTable.exportTable(exportTableOptions);
		},
});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent

<form action="/{{ $goBackRoutePath }}" method="get" id="filterForm" data-no-api-prefix="1" data-bind="submit: submitFilter.bind($root)">
<div class="row">
	
	<div class="col-md-2 form-group ">
		<label class="control-label" for="invoiceDtFrom">Last Updated Date From:</label>
		<input type="text" required name="invoiceDtFrom" id="invoiceDtFrom" class="form-control input-sm " data-bind="value: invoiceDtFrom"/>
	</div>
	<div class="col-md-2 form-group ">
		<label class="control-label" for="invoiceDtTo">Last Updated Date To:</label>
		<input type="text" required name="invoiceDtTo" id="invoiceDtTo" class="form-control input-sm" data-bind="value: invoiceDtTo"/>
	</div>
	<div class="col-md-2 form-group ">
		<label class="control-label" for="region">Region:</label>
		<select type="text" name="region" id="region" class="form-control input-sm" data-bind="options: $root.regions, optionsValue: 'value', optionsText: 'value', value: region, event: {change: $root.setRegion.bind($root)}"></select>
	</div>
</div>
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="status">Invoice Status:</label>
		<select type="text" name="status" id="status" class="form-control input-sm" data-bind="options: $root.invoiceStatuses, optionsValue: 'InvoiceStatusID', optionsText: 'Description', value: invoiceStatus, optionsCaption: 'All', valueAllowUnset: true"></select>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="createdBy">Last Updated By:</label>
		
		<select type="text" name="lastUpdatedBy" id="lastUpdatedBy" class="form-control input-sm" data-bind="options: $root.analystList, optionsValue: 'username', optionsText: 'username', value: lastUpdatedBy, optionsCaption: '', valueAllowUnset: true"></select>
	</div>
	
		
	
</div>
<div>
	
	<button type="submit" class="btn btn-primary" name="Submit" value="Submit" >
	<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
	Submit
	</button>
	<button  type="button" class="btn btn-primary pull-right" value="Clear" name="Clear" data-bind='click: $root.clear.bind($root)'>Clear</button>
</div>
</form>

<br>
<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<th>VendorID</th>
		<th>Vendor</th>
		<th>DocNumber</th>
		<th>PONumber</th>
		<th>VoucherNumber</th>
		<th>InvoiceDate</th>
		<th>TotalMRC</th>
		<th>TotalNRC</th>
		<th data-bind="css: { hide: $root.region() == 'US'}">PST Tax</th>
		<th data-bind="css: { hide: $root.region() == 'US'}">GST Tax</th>
		<th data-bind="css: { hide: $root.region() == 'US'}">HST Tax</th>
		<th data-bind="css: { hide: $root.region() == 'US'}">QST Tax</th>
		<th>O & M Total</th>
		<th>LPC</th>
		<th>InvoiceTotal</th>
		<th>DisputedTotal</th>
		<th>AcceptedTotal</th>
		<th>Status</th>
		<th>LastUpdatedBy</th>
		<th>LastUpdatedDt</th>
	</thead>
	<tbody data-bind="foreach: tableData, visible: tableData().length > 0">
		<tr>
			<td>
				<span data-bind="text: _.trim($data.VendorId)"></span>
			</td>
			<td>
				<span data-bind="text: _.trim($data.VendName)"></span>
			</td>
			<td>
				<span data-bind="text: $data.InvoiceNum"></span>
			</td>
			<td>
				<span data-bind="text: $data.TelcoAccNum"></span>
			</td>
			<td>
				<span data-bind="text: $data.GPInvoiceNum"></span>
			</td>
			<td>
				<span data-bind="text: moment($data.InvoiceDt).format('MM/DD/YYYY')"></span>
			</td>
			<td><span data-bind="number: $data.TotalMRC, decimals: 2"></span></td>
			<td><span data-bind="number: $data.TotalNRC, decimals: 2"></span></td>
			<td data-bind="css: { hide: $root.region() == 'US'}"><span data-bind="number: $data.PSTTax, decimals: 2"></span></td>
			<td data-bind="css: { hide: $root.region() == 'US'}"><span data-bind="number: $data.GSTTax, decimals: 2"></span></td>
			<td data-bind="css: { hide: $root.region() == 'US'}"><span data-bind="number: $data.HSTTax, decimals: 2"></span></td>
			<td data-bind="css: { hide: $root.region() == 'US'}"><span data-bind="number: $data.QSTTax, decimals: 2"></span></td>
			<td><span data-bind="number: $data.TotalOM, decimals: 2"></span></td>
			<td><span data-bind="number: $data.TotalLateFee, decimals: 2"></span></td>
			<td><span data-bind="number: $data.InvoiceTotal, decimals: 2"></span>
			<td><span data-bind="number: $data.DisputedTotal, decimals: 2"></span></td>
			<td><span data-bind="number: $data.AcceptedTotal, decimals: 2"></span></td>
			<td><span data-bind="text: $data.Status"></span></td>
			<td><span data-bind="text: $data.LastUpUser"></span></td>
			<td><span data-bind="text: $data.LastUpDt ? moment($data.LastUpDt).format('MM/DD/YYYY') : ''"></span></td>
		</tr>
	</tbody>
	<tbody data-bind="visible: tableData().length == 0 && $root.hitItOnce()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>


@stop