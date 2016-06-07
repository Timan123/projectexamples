@extends('layouts.default')

@section('title')
TIM - Invoice Assignment
@stop

@section('pageHeaderTitle')
Invoice Assignment
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		accountNum: null,
		accountOwner: null,
		kwikTag: null,
		vendorId:   null,
		invoiceType: {{json_encode($invoiceType)}},
		invoiceDt: null,
		dueDt: null,
		prevBal: 0,
		assignTo: null,
		invoiceTypeList: [{value: 'Paper'},{value: 'Email'}, {value: 'Web'}],
		analystList: []
	},
	dependencies:	[ 
					{ observable: 'analystList', route: 'api.getTimAnalysts', cache: true}
					],
	initialize: function() {
		//use the autocomplete block that only brings back vendors that have accounts on them
		//as the main screen also does
		@include('autocompletes.assignAutos');
		$('#vendor').typeahead('val', this.vendorId());
		$('#accountNum').typeahead('val', this.accountNum());
	},
	autoTriggerFilterFormSubmit: false,
	clear: function(element, event) {
		this.assignTo(null);
		this.invoiceDt(null);
		this.vendorId(null);
		this.accountOwner(null);
		this.accountNum(null);
		this.kwikTag(null);
	},
	accountLookup: function(element, event) {
		this.ajaxRequest
			(
				{
					route: 'api.accounts',
					data:  { accountNum: this.accountNum(), vendor: this.vendorId()}
				},
				function(data, json)
				{
					//only do stuff with 1 account, if we looked up only by vendor we could have gotten more than 1 account
					if (data.length == 1) {
						this.accountNum(data[0].TelcoAccNum);
						$('#accountNum').typeahead('val', data[0].TelcoAccNum);
						//fill in vendor as a reverse lookup, so to speak
						this.vendorId(data[0].TelcoId);
						//use the latestTotalInvoice here because we want to go strictly by latest invoice, not newest non-new invoice
						var lastDate = _.result(data[0], 'latestTotalInvoice.InvoiceDt', null);
						if (lastDate) {
							var invoiceDt = moment(lastDate).add(1, 'M');
							var dueDt = moment(lastDate).add(2, 'M');
							invoiceDt = invoiceDt.format('MM/DD/YYYY');
							dueDt = dueDt.format('MM/DD/YYYY');
							this.invoiceDt(invoiceDt);
							this.dueDt(dueDt);
							var accountOwner = _.result(data[0], 'AccountOwner');
							this.accountOwner(accountOwner);
							//if the accountowner exists prefill the assign to filed with this user
							if (accountOwner) {
								this.assignTo(accountOwner);
							}
							
							//do the previous balance from last month stuff on a hidden field then get it in the db that way
							var totalDue = _.result(data[0], 'latestTotalInvoice.TotalDue', 0);
							var acceptedTotal = _.result(data[0], 'latestTotalInvoice.AcceptedTotal', 0);
							var prevBal = totalDue - acceptedTotal;
							prevBal = _.round(prevBal, 2);
							this.prevBal(prevBal);
						}
					}
					
		
				}.bind(this)
			);
	}

});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent
<form action="/{{ $goBackRoutePath }}" method="post" id="searchBox" data-no-api-prefix="1">
<input type="hidden" name="dueDt" id="dueDt" data-bind="value: dueDt"></input>
<input type="hidden" name="prevBal" id="prevBal" data-bind="value: prevBal"></input>
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="vendor">Vendor:</label>
		<input type="text" name="vendor" id="vendor" class="form-control input-sm typeahead" data-bind="value: vendorId, event: {blur: $root.accountLookup}"/>
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="accountNum">Account Number:</label>
		<input type="text" name="accountNum" required id="accountNum" class="form-control input-sm typeahead" data-bind="value: accountNum, event: {blur: $root.accountLookup}">
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="accountOwner">Account Owner:</label>
		<span class="form-control input-sm no-border" data-bind="text: accountOwner"></span>
	</div>
	<div class="col-md-1 form-group">
		<label class="control-label" for="invoiceType">Type:</label>
		<select type="text" name="invoiceType" id="invoiceType" class="form-control input-sm" data-bind="options: $root.invoiceTypeList, optionsValue: 'value', optionsText: 'value', value: invoiceType"></select>
	</div>
	
	
	
</div>
<div class="row">
	<div class="col-md-2 form-group">
		<label class="control-label" for="kwikTag">KwikTag:</label>
		<input type="text" name="kwikTag" id="kwikTag" class="form-control input-sm" data-bind="value: kwikTag">
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="invoiceDt">New Invoice Date:</label>
		<input type="text" name="invoiceDt" required id="invoiceDt" class="form-control input-sm" data-bind="value: invoiceDt">
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="assignTo">Assign To:</label>
		<select type="text" name="assignTo" id="assignTo" class="form-control input-sm" data-bind="options: $root.analystList, optionsValue: 'username', optionsText: 'username', value: assignTo"></select>
	</div>
</div>

<div>
	
	<button type="submit" class="btn btn-primary" name="Submit" value="Submit" >
	<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
	Submit
	</button>
	<button  type="button" class="btn btn-primary pull-right " value="Clear" name="Clear" data-bind='click: $root.clear.bind($root)'>Clear</button>
</div>
</form>


@stop