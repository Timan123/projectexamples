@extends('layouts.default')

@section('title')
Provs
@stop

@section('pageHeaderTitle')
Provs - {{$type}} for {{$q}}
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		noResults: false,
		provEvents: [],
		statusCounts: [],
		metaStatuses: [],
//		navBarLoginLoading: false,
//		navBarLoginSuccess: true,
//		navBarLoginError: false,
	},
//	navBarLoginSubmit: function(form, event)
//	{
//		return true;
//	},
	autoTriggerFilterFormSubmit: true,

	initialize: function()
	{
		this.ajaxRequest
		(
			{ route: [ 'api.getProvsDisplay', { by: '{{$by}}', type: '{{$type}}', q: '{{$q}}' } ] },	
			function(json)
			{
				if (json.length == 0) {
					this.noResults(true);
				} else {
					this.provEvents(json);
					
				}
				
			},
			this
		);
		
	}
	

});
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent

<h4>Status counts:</h4>
<!-- ko foreach: $root.metaStatuses -->
<span data-bind="text: $data.status"></span>: <span data-bind="text: $data.count"></span><br>
<!-- /ko -->
<h4>Provisioning Work Orders:</h4>
<table class="table table-striped table-responsive table-condensed small" id="provTable">
	<thead>
		<th data-bind="orderable: { collection: 'provEvents', field: 'OrderId'}">OrderId</th>
		<th data-bind="orderable: { collection: 'provEvents', field: 'ByUser'}">ByUser</th>
		<th data-bind="orderable: { collection: 'provEvents', field: 'OnDate'}">OnDate</th>
		<th data-bind="orderable: { collection: 'provEvents', field: 'StatusCode'}">CurrentOrderStatus</th>
		<th data-bind="orderable: { collection: 'provEvents', field: 'Market'}">Market</th>
		<th>FEs</th>
	</thead>
	<tbody data-bind="foreach: provEvents, visible: provEvents().length > 0">
		<tr>
			<td>
				<a data-bind="attr: { href: '{{ Config::get('constants.props.provLink')}}' + $data.OrderId}, text: $data.OrderId" target="_prov"></a>
			<td>
				<span data-bind="text: $data.ByUser"></span>
			<td>
				<span data-bind="text: $data.OnDate"></span>
			</td>
			<td>
				<span data-bind="text: $data.StatusCode"></span>
			</td>
			<td>
				<span data-bind="text: $data.Market"></span>
			</td>
			<td>
				<span data-bind="foreach: $data.FEs">
					<span data-bind="text: $data.FE"></span>
				</span>
			</td>
			
		</tr>
	</tbody>
	<tbody data-bind="visible: noResults()">
		<tr>
			<td colspan="100%">No Results Found</td>
		</tr>
	</tbody>
</table>

	

@stop