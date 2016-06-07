@extends('layouts.default')

@section('title')
TIM - Invoice Listing
@stop
@section('containerClass')
container big-container
@stop


@section('pageHeaderTitle')
Invoice Listing
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		assignedTo: {{json_encode(Auth::user()->username)}}, //default the assignedTo dropdown selection to who is currently logged in
		invoiceDtFrom: null,
		invoiceDtTo: null,
		invoiceStatus: 1, //default this to 1=New
		hitItOnce: false,
		createdDtFrom: null,
		createdDtTo: null,
		invoiceStatuses: [],
		analystList: []
	},
	dependencies: [
		{ observable: 'invoiceStatuses', route: 'api.getInvoiceStatuses', cache: true},
		{ observable: 'analystList', route: 'api.getTimAnalysts', cache: true}
		],
	autoTriggerFilterFormSubmit: false,
	submitFilter: function(element, event) {
		this.ajaxRequest
			(
				{
					route: 'api.invoiceSearch',
					data:  { assignedTo: this.assignedTo(), invoiceDtFrom: this.invoiceDtFrom(), invoiceDtTo: this.invoiceDtTo(), invoiceStatus: this.invoiceStatus(), createdDtFrom: this.createdDtFrom(), createdDtTo: this.createdDtTo() }
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
<div class="container" id="everythingButResultsDiv">
<form action="/{{ $goBackRoutePath }}" method="get" id="filterForm" data-no-api-prefix="1" data-bind="submit: $root.submitFilter.bind($root)">
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="assignedTo">Assigned To:</label>
		
		<select type="text" name="assignedTo" id="assignedTo" class="form-control input-sm" data-bind="options: $root.analystList, optionsValue: 'username', optionsText: 'username', value: assignedTo, optionsCaption: '', valueAllowUnset: true"></select>
	</div>
	<div class="col-md-2 form-group col-md-offset-1">
		<label class="control-label" for="assignedTo">InvoiceDt From:</label>
		<input type="text" name="invoiceDtFrom" id="invoiceDtFrom" class="form-control input-sm " data-bind="value: invoiceDtFrom"/>
	</div>
	<div class="col-md-2 form-group ">
		<label class="control-label" for="assignedTo">InvoiceDt To:</label>
		<input type="text" name="invoiceDtTo" id="invoiceDtTo" class="form-control input-sm" data-bind="value: invoiceDtTo"/>
	</div>
</div>
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="status">Invoice Status:</label>
		<select type="text" name="status" id="status" class="form-control input-sm" data-bind="options: $root.invoiceStatuses, optionsValue: 'InvoiceStatusID', optionsText: 'Description', value: invoiceStatus, optionsCaption: 'All', valueAllowUnset: true"></select>
	</div>
	<div class="col-md-2 form-group col-md-offset-1">
		<label class="control-label" for="assignedTo">Created From:</label>
		<input type="text" name="createdDtFrom" id="createdDtFrom" class="form-control input-sm " data-bind="value: createdDtFrom"/>
	</div>
	<div class="col-md-2 form-group ">
		<label class="control-label" for="assignedTo">Created To:</label>
		<input type="text" name="createdDtTo" id="createdDtTo" class="form-control input-sm " data-bind="value: createdDtTo"/>
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
</div>
<br>
<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<th>VendorId</th>
		<th>VendorName</th>
		<th>AccountNum</th>
		<th>Invoice #</th>
		<th>InvoiceDt</th>
		<th>CreatedDt</th>
		<th>LastUpdated</th>
		<th>AssignedTo</th>
		<th>1stDraftDt</th>
		<th>1stSubmitDt</th>
		<th>Status</th>
		<th>TotalDue</th>
	</thead>
	<tbody data-bind="foreach: tableData, visible: tableData().length > 0">
		<tr>
			<td>
				<span><a data-bind="attr: { href: 'main?vendorId=' + escape(_.result($data, 'account.vendor.vendorId'))}, text: _.result($data, 'account.vendor.vendorId')"></a>
				</span></td>
			<td>
				<span><span data-bind="text: _.trunc(_.result($data, 'account.vendor.vendorName'),20)"></span>
				</span></td>
			<td>
				<span><a data-bind="attr: { href: 'main?vendorId=' + escape(_.result($data, 'account.vendor.vendorId')) + '&accountNum=' + escape($data.TelcoAccNum)}, text: $data.TelcoAccNum"></a>
				</span>
			</td>
			<td>
				<span>
					<a data-bind="attr: { href: 'main?vendorId=' + escape(_.result($data, 'account.vendor.vendorId')) + '&accountNum=' + escape($data.TelcoAccNum) + '&invIndexNum=' + $data.IndexNum}, text: $data.InvoiceNum"></a>
					
				</span>
			</td>
			<td><span data-bind="text: $data.InvoiceDt ? moment($data.InvoiceDt).format('MM/DD/YYYY') : ''"></span></td>
			<td><span data-bind="text: $data.CreatedDt ? moment($data.CreatedDt).format('MM/DD/YYYY') : ''"></span></td>
			<td><span data-bind="text: $data.LastUpDt ? moment($data.CreatedDt).format('MM/DD/YYYY') : ''"></span></td>
			<td><span data-bind="text: $data.AssignedTo"></span></td>
			<td><span data-bind="text: $data.FirstDraftDt ? moment($data.FirstDraftDt).format('MM/DD/YYYY') : ''"></span></td>
			<td><span data-bind="text: $data.FirstSubmitDt ? moment($data.FirstSubmitDt).format('MM/DD/YYYY') : ''"></span></td>
			<td><span data-bind="text: $data.invoiceStatusDesc"></span></td>
			<td class="text-right"><span data-bind="number: $data.TotalDue, decimals: 2"></span></td>
		</tr>
	</tbody>
	<tbody data-bind="visible: tableData().length == 0 && $root.hitItOnce()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>


@stop