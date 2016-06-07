@extends('layouts.default')

@section('title')
Tickets
@stop

@section('pageHeaderTitle')
Tickets - {{$type}} for {{$q}}
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		noResults: false,
		tickets: [],
	},
	navBarLoginSubmit: function(form, event)
	{
		return true;
	},
	autoTriggerFilterFormSubmit: true,

	initialize: function()
	{
		this.ajaxRequest
		(
			{ route: [ 'api.getTicketsDisplay', { by: '{{$by}}', type: '{{$type}}', q: '{{$q}}' } ] },	
			function(json)
			{
				if (json.length == 0) {
					this.noResults(true);
				} else {
					this.tickets(json);
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



<h4>Dispatch Remedy Tickets:</h4>
<table class="table table-striped table-responsive table-condensed small" id="provTable">
	<thead>
		<th data-bind="orderable: { collection: 'tickets', field: 'Case_ID'}">Case_ID</th>
		<th data-bind="orderable: { collection: 'tickets', field: 'statusDesc'}">Status</th>
		<th data-bind="orderable: { collection: 'tickets', field: 'Summary'}">Summary</th>
		<th data-bind="orderable: { collection: 'tickets', field: 'Dispatch_Area'}">Dispatch Area</th>
		<th data-bind="orderable: { collection: 'tickets', field: 'FE_Assigned'}">FE Assigned</th>
		<th data-bind="orderable: { collection: 'tickets', field: 'Dispatch_Request_Tally'}">Dispatch Request Tally</th>
		<th data-bind="orderable: { collection: 'tickets', field: 'Severity'}">Severity</th>
		<th data-bind="orderable: { collection: 'tickets', field: 'PendingNext'}">Next Action</th>
		<th>MostRecentDispatchEmail</th>
		
	</thead>
	<tbody data-bind="foreach: tickets, visible: tickets().length > 0">
		<tr>
			<td>
				<a data-bind="attr: { href: '{{ Config::get('constants.props.remedyLink')}}' + $data.Case_ID}, text: $data.Case_ID" target="_remedy"></a>
			</td>
			<td>
				<span data-bind="text: $data.statusDesc"></span>
			</td>
			<td>
				<span data-bind="text: _.trunc($data.Summary,60)"></span>
			</td>

			<td>
				<span data-bind="text: $data.Dispatch_Area"></span>
			</td>
			<td>
				<span data-bind="text: $data.FE_Assigned"></span>
			</td>
			<td>
				<span data-bind="text: $data.Dispatch_Request_Tally"></span>
			</td>
			<td>
				<span data-bind="text: $data.Severity"></span>
			</td>
			<td>
				<span data-bind="text: $data.PendingNext"></span>
			</td>
			<td>
				<span data-bind="text: _.result($data, 'mostRecentDispatchEmail.Date_Sent','')"></span>
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