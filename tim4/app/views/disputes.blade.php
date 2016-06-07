@extends('layouts.default')

@section('title')
TIM - Disputes
@stop

@section('pageHeaderTitle')
Disputes
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		accountNum: {{json_encode($accountNum)}},
		circuit: {{json_encode($circuit)}},
		inv: {{json_encode($inv)}} == "true", //make this a boolean, not a string, even on load
		linesAndDisputes: [],
		hitItOnce: false,
		disputeStatuses: [{value: 'Open'},{value: 'Awaiting Credit'}, {value: 'Closed-Won'}, {value: 'Closed-PaidBack'}],
	},
	initialize: function() {
		$('#accountNum').val(this.accountNum()).typeahead('val', this.accountNum());
		$('#circuit').val(this.circuit()).typeahead('val', this.circuit());
		if (this.accountNum()) {
			this.fillInDisputes();
		}
		@include('autocompletes.disputeAuto')
	},
	autoTriggerFilterFormSubmit: false,
	closeAll: function(element, event) {
		var lines = _.map(this.linesAndDisputes(), function(line) {
			var status = line.Status();
			if (!_.startsWith(status,'Closed')) {
				var won = line.WonAmount();
				var rem = line.Remaining();
				won = _.add(won,rem);
				line.WonAmount(won);
				line.Remaining(0);
				line.Status('Closed-Won');
			}	
			return line;
		});
		this.linesAndDisputes(lines);
		
	},
	fillInDisputes: function(element, event) {
		this.hitItOnce(true);
		this.ajaxRequest
			(
				{ route: [ 'api.getDisputesByAccountNum', { accountNum: this.accountNum(), circuit: this.circuit(), inv: this.inv() } ] },
				function(json)
				{
					json = _.map(json, function(entry) {
						entry.Remaining = ko.observable(entry.Remaining);
						//bump these up a level in the process of making them observables
						entry.WonAmount = ko.observable(entry.dispute.WonAmount);
						entry.PaidBack = ko.observable(entry.dispute.WonAmount);
						entry.Status = ko.observable(entry.dispute.Status);
						return entry;
					});
					this.linesAndDisputes(json);
				},
				this
			);
			return false;
		
	},
});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent

<form action="/{{ $goBackRoutePath }}" method="get" id="filterForm" data-no-api-prefix="1" data-bind="submit: fillInDisputes.bind($root)">
<div class="row">
	
		<div class="col-md-2 form-group">
			<label class="control-label" for="accountNum">Account Number:</label>
			<input required type="text" name="accountNum" id="accountNum" class="form-control input-sm typeahead" data-bind="value: accountNum">
		</div>
		<div class="col-md-2 form-group">
			<label class="control-label" for="circuit">CircuitId:</label>
			<input type="text" name="circuit" id="circuit" class="form-control input-sm typeahead" data-bind="value: circuit">
		</div>
		<div class="col-md-2 form-group">
				<label class="control-label" for="invoiceLevel">Invoice Level Only</label>
				<input type="checkbox" name="invoiceLevel" class="checkbox" data-bind="checked: $root.inv"></input>
		</div>
</div>
<div>
	
	<button type="submit" class="btn btn-primary" name="Submit" value="Submit" >
	<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
	Find Disputes
	</button>
	
</div>
</form>
<br>
<form action="/{{ $goBackRoutePath }}" method="post" id="filterForm" data-no-api-prefix="1" data-bind="submit: submitFilter.bind($root)">
	<input type="hidden" name="accountNum" data-bind="value: $root.accountNum()"/>
	<input type="hidden" name="inv" data-bind="value: $root.inv()"/>
	<input type="hidden" name="circuit" data-bind="value: $root.circuit()"/>
<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<th>DisputeId</th>
		<th>InvoiceDt</th>
		<th>PON</th>
		<th>CircuitId</th>
		<th>Category</th>
		<th>Status</th>
		<th>Remaining</th>
		<th>WonAmount</th>
		<th>PaidBack</th>
		<th>VendorDisputeNum</th>
		<th>RemedyTicket</th>
	</thead>
	<tbody data-bind="foreach: linesAndDisputes, visible: linesAndDisputes().length > 0">
		<tr>
			<!--put this here so we can update the remaining open dispute value on the invoice level-->
			<input type="hidden" name="InvIndexNum[]" data-bind="value: $data.invoice.IndexNum"/>
			<input type="hidden" name="LineIndexNum[]" data-bind="value: $data.IndexNum"/>
			<input type="hidden" name="DisputeId[]" data-bind="value: $data.dispute.DisputeId"/>
			
			<td>
				<span data-bind="text: $data.dispute.DisputeId"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'main?accountNum=' + escape($root.accountNum()) + '&invIndexNum=' + $data.invoice.IndexNum}, text: moment($data.invoice.InvoiceDt).format('MM/DD/YYYY')" target='_main'></a>
				<span data-bind=""></span>
			</td>
			<td>
				<span data-bind="text: _.result($data,'charge.PON')"></span>
			</td>
			<td>
				<span data-bind="text: _.trunc(_.result($data,'charge.telcoOrderDetails.CircuitID'),20)"></span>
			</td>
			<td>
				<span data-bind="text: _.result($data,'dispute.Category.Category')"></span>
			</td>
			<td>
				<select name="Status[]" class="input-sm input-smaller form-control form-control-lite" data-bind="options: $root.disputeStatuses, optionsValue: 'value', optionsText: 'value', value: $data.Status"></select>
			</td>
			<td>
				<input type="text" name="Remaining[]" size="10" class="input-sm input-smaller form-control form-control-lite" data-bind="value: $data.Remaining, number: $data.Remaining, decimals: 2"></input>
			</td>
			<td>
				<input type="text" name="WonAmount[]" size="10" class="input-sm input-smaller form-control form-control-lite" data-bind="value: $data.WonAmount, number: $data.WonAmount, decimals: 2"></input>
			</td>
			<td>
				<input type="text" name="PaidBack[]" size="10" class="input-sm input-smaller form-control form-control-lite" data-bind="value: $data.PaidBack, number: $data.PaidBack, decimals: 2"></input>
			</td>
			<td>
				<input type="text" name="VendorDisputeNum[]" class="input-sm input-smaller form-control form-control-lite" data-bind="value: $data.dispute.VendorDisputeNum"></input>
			</td>
			<td>
				<input type="text" name="RemedyTicket[]" class="input-sm input-smaller form-control form-control-lite" data-bind="value: $data.dispute.RemedyTicket"></input>
			</td>
			
		</tr>
	</tbody>
	<tbody data-bind="visible: tableData().length == 0 && $root.hitItOnce()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>
	
	<br>
	<button type="submit" class="btn btn-primary" name="Submit" value="Submit" data-bind="visible: linesAndDisputes().length > 0">
	<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
	Submit
	</button>
	<button type="button" value="Close All" class="btn btn-primary pull-right" data-bind="click: $root.closeAll, visible: linesAndDisputes().length > 0">Close All</button>
</form>




@stop