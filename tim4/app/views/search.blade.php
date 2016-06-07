@extends('layouts.default')

@section('title')
TIM - Search
@stop
@section('containerClass')
container big-container
@stop


@section('pageHeaderTitle')
Search
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		telcoNames: [],
		circuitStatuses: [],
		circuitTypes: [],
		circuitClasses: [],
		orderCurrencies: [],
		telcoSpeeds: [],
		serviceCoordinators: [],
		hitItOnce: false
	},
	dependencies:	[ //caching causes problems in IE, but it's helpful here since these dropdown values are very constant	
					{ observable: 'telcoNames', route: 'api.telcoNames', cache: true},
					{ observable: 'circuitStatuses', route: 'api.circuitStatuses', cache: true},
					{ observable: 'circuitTypes', route: 'api.circuitTypes', cache: true},
					{ observable: 'circuitClasses', route: 'api.circuitClasses', cache: true},
					{ observable: 'orderCurrencies', route: 'api.orderCurrencies', cache: true},
					{ observable: 'telcoSpeeds', route: 'api.telcoSpeeds', cache: true},
					{ observable: 'serviceCoordinators', route: 'api.serviceCoordinators', cache: true}
					],
	autoTriggerFilterFormSubmit: false,
	mySubmitFunction: function(element, event) {
		var form = $('#filterForm').serializeObject();
		this.ajaxRequest
			(
				{
					route: 'api.bigSearch',
					data: { form: form }
				},
				function(json)
				{
					this.bindAjaxResponse(json);
					this.hitItOnce(true);
				},
				this
			);
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
				csvRow[0] = '=' + csvRow[0];
				csvRow[1] = '=' + csvRow[1];
				csvRow[2] = '=' + csvRow[2];
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
<form action="/{{ $goBackRoutePath }}" method="get" id="filterForm" data-no-api-prefix="1" data-bind="submit: $root.mySubmitFunction.bind($root)">
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="telco">Telco:</label>
		<select type="text" name="telco" id="telco" class="form-control input-sm input-smaller" data-bind="options: $root.telcoNames, optionsValue: 'TelcoId', optionsText: 'TelcoName', optionsCaption: '', valueAllowUnset: true"></select>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="circuitStatus">Circuit Status:</label>
		<select type="text" name="circuitStatus" id="circuitStatus" class="form-control input-sm input-smaller" data-bind="options: $root.circuitStatuses, optionsValue: 'CircuitStatusCode', optionsText: 'CircuitStatus', optionsCaption: '', valueAllowUnset: true"></select>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="term">Term:</label>
		<select type="text" name="term" id="term" class="form-control input-sm input-smaller">
			<option value=""></option>
			<option value="0 Months">0 Months</option>
			<option value="12 Months">12 Months</option>
			<option value="24 Months">24 Months</option>
			<option value="36 Months">36 Months</option>
			<option value="48 Months">48 Months</option>
			<option value="60 Months">60 Months</option>
		</select>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="circuitId">CircuitId: *</label>
		<input type="text" name="circuitId" id="circuitId" class="form-control input-sm input-smaller"></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="circuitType">Circuit Type:</label>
		<select type="text" name="circuitType" id="circuitType" class="form-control input-sm input-smaller" data-bind="options: $root.circuitTypes, optionsValue: 'CircuitTypeCode', optionsText: 'CircuitType', optionsCaption: '', valueAllowUnset: true"></select>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="circuitClass">Circuit Class:</label>
		<select type="text" name="circuitClass" id="circuitClass" class="form-control input-sm input-smaller" data-bind="options: $root.circuitClasses, optionsValue: 'CircuitClassCode', optionsText: 'CircuitClass', optionsCaption: '', valueAllowUnset: true"></select>
	</div>
</div>
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="accountNum">Account Number: *</label>
		<input type="text" name="accountNum" id="accountNum" class="form-control input-sm input-smaller" ></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="vendorAccountNum">Vendor AccNum: *</label>
		<input type="text" name="vendorAccountNum" id="vendorAccountNum" class="form-control input-sm input-smaller" ></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="pon">PON:</label>
		<input type="text" name="pon" id="pon" class="form-control input-sm input-smaller" ></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="telcoSpeed">Currency:</label>
		<select type="text" name="currency" id="currency" class="form-control input-sm input-smaller" data-bind="options: $root.orderCurrencies, optionsValue: 'BillCurrencyId', optionsText: 'Currency', optionsCaption: '', valueAllowUnset: true"></select>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="voucherNumber">GP Number: *</label>
		<input type="text" name="voucherNumber" id="voucherNumber" class="form-control input-sm input-smaller" ></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="telcoSpeed">Telco Speed:</label>
		<select type="text" name="telcoSpeed" id="telcoSpeed" class="form-control input-sm input-smaller" data-bind="options: $root.telcoSpeeds, optionsValue: 'TelcoSpeedId', optionsText: 'TelcoSpeeds', optionsCaption: '', valueAllowUnset: true"></select>
	</div>
</div>
<hr class="less-margin">
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="orderId">OrderId: *</label>
		<input type="text" name="orderId" id="orderId" class="form-control input-sm input-smaller"></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="customerName">Customer Name: *</label>
		<input type="text" name="customerName" id="customerName" class="form-control input-sm input-smaller" ></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="orderInProgress">Order in Progress?:</label>
		<input type="checkbox" name="orderInProgress" value="orderInProgress" class="checkbox" data-bind="checked: false"></input>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="servCoord">Service Coordinator:</label>
		<select type="text" name="servCoord" id="servCoord" class="form-control input-sm input-smaller" data-bind="options: $root.serviceCoordinators, optionsValue: 'Userid', optionsText: 'Userid', optionsCaption: '', valueAllowUnset: true"></select>
	</div>
</div>
<hr class="less-margin">
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="startBillDtFrom">Circuit StartBillDt From:</label>
		<input type="text" name="startBillDtFrom" id="startBillDtFrom" class="form-control input-sm input-smaller" >
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="startBillDtFrom">Circuit StartBillDt To:</label>
		<input type="text" name="startBillDtTo" id="startBillDtTo" class="form-control input-sm input-smaller">
	</div>
	<div class="col-md-2 form-group col-md-offset-2">
		<label class="control-label" for="startBillDtFrom">Circuit StopBillDt From:</label>
		<input type="text" name="stopBillDtFrom" id="stopBillDtFrom" class="form-control input-sm input-smaller" >
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="stopBillDtFrom">Circuit StopBillDt To:</label>
		<input type="text" name="stopBillDtTo" id="stopBillDtTo" class="form-control input-sm input-smaller" >
	</div>
</div>
<div >
	
	<button type="submit" class="btn btn-primary" name="Submit" value="Submit" >
	<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
	Submit
	</button>
	
	<span>&nbsp;&nbsp;* = wildcards accepted</span>
	
	<button  type="button" class="btn btn-primary pull-right" value="Clear" name="Clear" data-bind='click: $root.clear.bind($root)'>Clear</button>
	
</div>
</form>
</div>
<br>
<table class="table table-striped table-responsive table-condensed small" id="dataTable" >
	<thead>
		<th>CircuitId</th>
		<th>AccountNum</th>
		<th>VendorAccNum</th>
		<th>Telco</th>
		<th>Customer</th>
		<th>PON</th>
		<th>MRC</th>
		<th>NRC</th>
		<th>CircuitType</th>
		<th>CircuitClass</th>
		<th>TelcoSpeed</th>
		<th>CircuitStatus</th>
		<th>StartBillDt</th>
		<th>StopBillDt</th>
		<th>OrderId</th>
		<th>Currency</th>
		<th>Entity</th>
		
	</thead>
	<tbody data-bind="foreach: tableData, visible: tableData().length > 0">
		<tr>
			<td>
				
				<a data-bind="attr: { href: '{{ Config::get('constants.props.provLink') . Config::get('constants.props.modPage')}}?IndexNum=' + $data.IndexNum}, text: $data.CircuitId ? _.trunc($data.CircuitId, 20) : 'Unknown'" target="_prov"></a>
			</td>
			<td>
				<!-- ko if: $data.AccountNum -->
				<a data-bind="attr: { href: 'main?accountNum=' + escape($data.AccountNum)}, text: _.trunc($data.AccountNum, 20)" target='_main'></a>
				<!-- /ko -->
				<!-- ko if: !$data.AccountNum && $data.CircuitId-->
				<strong><a data-bind="attr: { href: 'link?circuit=' + escape($data.CircuitId)}, text: 'Link Account'" target='_link'></a></strong>
				<!-- /ko -->
			</td>
			<td><span data-bind="text: _.trunc($data.BillingNum, 20)"></span></td>
			<td><span data-bind="text: _.trunc($data.TelcoName, 20)"></span></td>
			<td><span data-bind="text: _.trunc($data.CustomerName, 20)"></span></td>
			<td><span data-bind="text: $data.IndexNum"></span></td>
			<td class="text-right"><span data-bind="number: $data.MonthlyCost, decimals: 2"></span></td>
			<td class="text-right"><span data-bind="number: $data.InstallationCost, decimals: 2"></span></td>
			<td><span data-bind="text: $data.CircuitType"></span></td>
			<td><span data-bind="text: $data.CircuitClass"></span></td>
			<td><span data-bind="text: $data.TelcoSpeed"></span></td>
			<td><span data-bind="text: $data.CircuitStatus"></span></td>
			<td><span data-bind="text: $data.TelcoStartBillDt ? moment($data.TelcoStartBillDt).format('MM/DD/YYYY') : ''"></span></td>
			<td><span data-bind="text: $data.TelcoStopBillDt ? moment($data.TelcoStopBillDt).format('MM/DD/YYYY') : ''"></span></td>
			<td><span data-bind="text: $data.OrderId"></span></td>
			<td><span data-bind="text: $data.CurrencyCode"></span></td>
			<td><span data-bind="text: $data.CogentEntity"></span></td>
			
	</tbody>
	<tbody data-bind="visible: tableData().length == 0 && $root.hitItOnce()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>

@stop