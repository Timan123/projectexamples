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
		provEvents: [],
		buildings: [],
		hitItOnce: false
	},

	autoTriggerFilterFormSubmit: false,
	dependencies: [{ observable: 'markets', route: 'api.getMarkets', cache: true}],
	//				{ observable: 'analystList', route: 'api.getTimAnalysts', cache: true}],
	searchByMarket: function (element, event) {
		var market = this.market();
		this.ajaxRequest
		(
			{ route: [ 'api.getSOWsByMarket', { market: market } ] },	
			function(json)
			{
				this.sowEvents(json);
			},
			this
		);

		this.ajaxRequest
		(
			{ route: [ 'api.getProvsByMarket', { market: market } ] },	
			function(json)
			{
				this.provEvents(json);
			},
			this
		);

		this.ajaxRequest
		(
			{ route: [ 'api.getBuildingsByMarket', { market: market } ] },	
			function(json)
			{
				this.buildings(json);
			},
			this
		);
		this.hitItOnce(true);
	},

});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent

<div class="row">
	<form action="/{{ $goBackRoutePath }}" method="get" id="searchByMarket" data-no-api-prefix="1" data-bind="submit: searchByMarket.bind($root)">
		<div class="col-md-2 form-group">
			<label class="control-label" for="market">Market:</label>
			<select type="text" name="market" id="market" class="form-control input-sm" data-bind="options: $root.markets, optionsValue: 'Market', optionsText: 'Market', value: market"></select>
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

<h4>SOWs:</h4>
<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<th>WorkId</th>
		<th>Subject</th>
		<th>Location</th>
		<th>StartDate</th>
	</thead>
	<tbody data-bind="foreach: sowEvents, visible: sowEvents().length > 0">
		<tr>
			<td>
				<span data-bind="text: $data.WorkId"></span>
			<td>
				<span data-bind="text: $data.Subject"></span>
			<td>
				<span data-bind="text: $data.Location"></span>
			</td>
			<td>
				<span data-bind="text: $data.rstart"></span>
			</td>
		</tr>
	</tbody>
	<tbody data-bind="visible: sowEvents().length == 0 && $root.hitItOnce()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>

<h4>ProvTool Work Orders:</h4>
<table class="table table-striped table-responsive table-condensed small" id="provTable">
	<thead>
		<th>OrderId</th>
		<th>ByUser</th>
		<th>OnDate</th>
	</thead>
	<tbody data-bind="foreach: provEvents, visible: provEvents().length > 0">
		<tr>
			<td>
				<span data-bind="text: $data.OrderId"></span>
			<td>
				<span data-bind="text: $data.ByUser"></span>
			<td>
				<span data-bind="text: $data.OnDate"></span>
			</td>
		</tr>
	</tbody>
	<tbody data-bind="visible: provEvents().length == 0 && $root.hitItOnce()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>

	

@stop