@extends('layouts.default')

@section('title')
TIM - Management
@stop

@section('pageHeaderTitle')
Account/Circuit Management
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		circuit: {{json_encode($circuit)}},
		accountNum: {{json_encode($accountNum)}},
		accountOwner: {{json_encode($accountOwner)}},
		newAccount: false,
		allVendors: [],
		lcdbNote: null,
		vendorId: {{json_encode($vendor)}},
		fromForm: false,
		fromSearch: false,
		statusText: '',
		circuitStatusText: '',
		vendorAccountNum: {{json_encode($vendorAccountNum)}},
		circuitIndexNum: null,
		analystList: [],
	},
	initialize: function() {
		@include('autocompletes.linkAutos')
		if (this.accountNum()) {
			this.fromForm(true);
			
			//initilaize the typeaheads
			$('#vendor').typeahead('val', this.vendorId());
			$('#circuit').typeahead('val', this.circuit());
			$('#accountNum').typeahead('val', this.accountNum());
		} else if (this.circuit()) {
			this.fromSearch(true);
			this.circuitCheck();
			$('#circuit').typeahead('val', this.circuit());
		}
		
	},
	autoTriggerFilterFormSubmit: false,
	dependencies: [{ observable: 'allVendors', route: 'api.getAllVendors'},
					{ observable: 'analystList', route: 'api.getTimAnalysts', cache: true}],
	accountCheck: function(element, event)
	{
		//take the "fromForm" behavior out if user did anything with the account
		this.fromForm(false);
		var account = this.accountNum();
		if (account) {
			this.ajaxRequest
				(
					{
						route: 'api.accountCheck',
						data:  { account: account }
					},
					function(json)
					{
						if (json.length == 0) {
							this.vendorId('');
							this.accountOwner('');
							this.statusText('Yes');
						} else {
							this.vendorId(json[0].TelcoId);
							this.accountOwner(json[0].AccountOwner);
							this.statusText('No');
						}
					}.bind(this),
					this
				);
		}
	},
	circuitCheck: function(element, event) {
		var circuit = this.circuit();
		if (circuit) {
			this.ajaxRequest
				(
					{
						route: 'api.circuitCheck',
						data:  { circuit: circuit }
					},
					function(json)
					{
						if (json.length == 0) {
							this.circuitStatusText("NOT A CIRCUIT");
							this.circuitIndexNum(null);
							this.vendorAccountNum(null);
						} else {
							var status = _.result(json[0],'CircuitStatus');
							var vendorAccNum = _.result(json[0],'TelcoAccount#');
							this.circuitIndexNum(_.result(json[0],'IndexNum'));
							this.vendorAccountNum(vendorAccNum);
							this.circuitStatusText(status);
						}	
					}.bind(this),
					this
				);
		} else { //blank these for empty string circuit
			this.circuitStatusText(null);
			this.circuitIndexNum(null);
			this.vendorAccountNum(null);
		}
		
	},
	submitFilter: function(element, event) {
		//use the observable we loaded of all eligible vendors
		var realVendor = _.find(this.allVendors(), {'vendorId': this.vendorId()}, 'vendorId');
		if (realVendor) {
			return true;
		} else {
			app.notify(this.vendorId() + " is not a valid GP VendorId of Class: Colocation or Circuits.", { type: 'warning' });
			return false;
		}
	},

});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent
<form action="/{{ $goBackRoutePath }}" method="post" id="searchBox" data-no-api-prefix="1" data-bind="submit: submitFilter.bind($root)">
<div class="row">
	
	<div class="col-md-2 form-group">
		<label class="control-label" for="accountNum">Account Number:</label>
		<input type="text" required  name="accountNum" id="accountNum" class="form-control input-sm typeahead" data-bind="value: accountNum, event: { blur: $root.accountCheck } ">
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="existing">New Account?</label>
		<span name="existing" class="form-control no-border input-sm" data-bind="text: statusText"></span>
	</div>
	

	

	<div class="col-md-2 form-group">
		<label class="control-label" for="vendor">Set Vendor on Account:</label>
		<input type="text" name="vendor" required id="vendor" class="form-control input-sm typeahead"  data-bind="value: vendorId"/>
	</div>
	
	<div class="col-md-2 form-group">
		<label class="control-label" for="accountOwner">Set Account Owner:</label>
		<select type="text" name="accountOwner" id="accountOwner" class="form-control input-sm" data-bind="options: $root.analystList, optionsValue: 'username', optionsText: 'username', value: accountOwner, optionsCaption: '', valueAllowUnset: true"></select>
	</div>
	
	
</div>
<div class="row">
	<div class="col-md-3 form-group">
		<label class="control-label" for="circuit">CircuitId to link:</label>
		<input type="text" name="circuit" id="circuit" class="form-control input-sm typeahead" data-bind="value: circuit, event: { blur: $root.circuitCheck } ">
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="existing">Circuit Status</label>
		<span name="existing" class="form-control no-border input-sm" data-bind="text: circuitStatusText"></span>
	</div>
	<div class="col-md-3 form-group">
		<label class="control-label" for="circuit">Set Vendor Account Number:</label>
		<input type="text" name="vendorAccountNum" id="vendorAccountNum" class="form-control input-sm" data-bind="value: vendorAccountNum">
	</div>
	<div class="col-md-2 form-group">
		<label class="control-label" for="circuit">ProvTool Link:</label>
		<span type="text" name="vendorAccountNum" id="vendorAccountNum" class="form-control input-sm no-border" data-bind="if: circuitIndexNum">
			<a data-bind="attr: { href: '{{ Config::get('constants.props.provLink') . Config::get('constants.props.modPage')}}?IndexNum=' + $root.circuitIndexNum()}, text: 'PON = ' +$root.circuitIndexNum()" target="_provtool"></a>
		</span>
	</div>
	
	
</div>
	<div class="row">
		<div class="col-md-8 form-group">
			<label class="control-label" for="lcdbNote">LCDB Note for circuit:</label>
			<input type="text" name="lcdbNote" class="form-control input-sm" data-bind="value: lcdbNote">
	</div>
	<div class="col-md-1 form-group">
		<label class="control-label show">&nbsp;</label>
		<button type="submit" class="btn btn-sm btn-primary">
			Submit
		</button>
	</div>
	</div>
</form>
<br><br>
<div data-bind="if: fromForm()">
	<form action="/main" method="get" data-no-api-prefix="1">
		<input type="hidden" name="accountNum" data-bind="value: accountNum"></input>
		<input type="hidden" name="vendorId" data-bind="value: vendorId"></input>
		
		<button type="submit" class="btn btn-sm btn-primary">
			Jump to account
		</button>
	</form>
</div>

@stop