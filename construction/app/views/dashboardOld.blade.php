@extends('layouts.default')

@section('title')
Construction Dashboard
@stop

@section('pageHeaderTitle')
Dashboard
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		market: null,
		markets: [],
		sowEvents: [],
		FE: null,
		FEs: [],
		sowMarketEvents: [],
		buildingsByRegion: [],
		buildingsByFE: [],
		buildingsByMarket: [],
		provEvents: [],
		provMarketEvents: [],
		provFEEvents: [],
		sowFEEvents: [],
		buildings: [],
		tickets: [],
		ticketsByMarket: [],
		ticketsByFE: [],
		hitItOnce: false
	},

	autoTriggerFilterFormSubmit: true,
	dependencies: [{ observable: 'markets', route: 'api.getMarkets', cache: true},
					{ observable: 'FEs', route: 'api.getFEs', cache: true}],
	submitFilter: function (element, event) {
		
		this.ajaxRequest
		(
			{ route: [ 'api.getSOWRegionCounts', {  } ] },	
			function(json)
			{
				this.sowEvents(json);
			},
			this
		);

		this.ajaxRequest
		(
			{ route: [ 'api.getProvRegionCounts', {  } ] },	
			function(json)
			{
				this.provEvents(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getSOWMarketCounts', {  } ] },	
			function(json)
			{
				this.sowMarketEvents(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getProvMarketCounts', {  } ] },	
			function(json)
			{
				this.provMarketEvents(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getSOWFECounts', {  } ] },	
			function(json)
			{
				this.sowFEEvents(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getProvFECounts', {  } ] },	
			function(json)
			{
				this.provFEEvents(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getBuildingRegionCounts', {  } ] },	
			function(json)
			{
				this.buildingsByRegion(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getBuildingMarketCounts', {  } ] },	
			function(json)
			{
				this.buildingsByMarket(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getBuildingFECounts', {  } ] },	
			function(json)
			{
				this.buildingsByFE(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getTicketCounts', {  } ] },	
			function(json)
			{
				this.tickets(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getTicketMarketCounts', {  } ] },	
			function(json)
			{
				this.ticketsByMarket(json);
			},
			this
		);
		this.ajaxRequest
		(
			{ route: [ 'api.getTicketFECounts', {  } ] },	
			function(json)
			{
				this.ticketsByFE(json);
			},
			this
		);


		
	},

});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent

<div class="row">
	<form action="/{{ $goBackRoutePath }}" method="get" id="searchByMarket" data-no-api-prefix="1" data-bind="submit: submitFilter.bind($root)">
		<div class="col-md-2 form-group">
			<span class="control-label show">Everything displaying for last 2 months, except 4 months for Buildings</span>
		</div>
		<div class="col-md-2 form-group">
			<label class="control-label" for="market">Market:</label>
			<select type="text" name="market" id="market" class="form-control input-sm" data-bind="options: $root.markets, optionsValue: 'Market', optionsText: 'Market', value: market"></select>
		</div>
		<div class="col-md-2 form-group">
			<label class="control-label" for="FE">Field Engineer:</label>
			<select type="text" name="FE" id="FE" class="form-control input-sm" data-bind="options: $root.FEs, optionsValue: 'Username', optionsText: 'Username', value: FE"></select>
		</div>
		
		<div class="col-md-4 form-group">
			<label class="control-label show">&nbsp;</label>
			<button type="submit" class="btn btn-sm btn-primary">
				<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
				Go
			</button>
		</div>
	</form>
</div>

<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<th>SOWs</th>
		<th>Count</th>
	</thead>
	<tbody >
		<tr>
			<td>
				<span>NA</span>
			</td>
			<td>
				<span data-bind="text: $root.sowEvents().NA"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span>EU</span>
			</td>
			<td>
				<span data-bind="text: $root.sowEvents().EU"></span>
			</td>
		</tr>
		<!-- ko foreach: $root.sowMarketEvents -->
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
		<!-- ko foreach: $root.sowFEEvents -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
</table>

<table class="table table-striped table-responsive table-condensed small" id="dataTable2">
		<thead>
			<th>Provisioning Work Orders</th>
			<th>Count</th>
		</thead>
		<tr>
			<td>
				<span>NA</span>
			</td>
			<td>
				<span data-bind="text: $root.provEvents().NA"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span>EU</span>
			</td>
			<td>
				<span data-bind="text: $root.provEvents().EU"></span>
			</td>
		</tr>
		<!-- ko foreach: $root.provMarketEvents -->
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
		<!-- ko foreach: $root.provFEEvents -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
	</tbody>
</table>

<table class="table table-striped table-responsive table-condensed small" id="dataTable3">
		<thead>
			<th>New Building Lights (FE for this is Manager only)</th>
			<th>Count</th>
		</thead>
		<tr>
			<td>
				<span>NA</span>
			</td>
			<td>
				<span data-bind="text: $root.buildingsByRegion().NA"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span>EU</span>
			</td>
			<td>
				<span data-bind="text: $root.buildingsByRegion().EU"></span>
			</td>
		</tr>
		<!-- ko foreach: $root.buildingsByMarket -->
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
		<!-- ko foreach: $root.buildingsByFE -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
</table>

<table class="table table-striped table-responsive table-condensed small" id="dataTable4">
		<thead>
			<th>Dispatch Remedy Tickets (NA Only)</th>
			<th>Count</th>
		</thead>
		<tr>
			<td>
				<span>Total</span>
			</td>
			<td>
				<span data-bind="text: $root.tickets().count"></span>
			</td>
		</tr>
		<!-- ko foreach: $root.ticketsByMarket -->
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
		<!-- ko foreach: $root.ticketsByFE -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<span data-bind="text: $data.count"></span>
			</td>
		</tr>
		<!-- /ko -->
</table>
	

@stop