@extends('layouts.default')

@section('title')
SOWs
@stop

@section('pageHeaderTitle')
SOWs - {{$type}} for {{$q}}
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		sowEvents: [],
		noResults: false,
//		navBarLoginLoading: false,
//		navBarLoginSuccess: true,
//		navBarLoginError: false,
	},

	autoTriggerFilterFormSubmit: true,
//	navBarLoginSubmit: function(form, event)
//	{
//		return true;
//	},
	initialize: function()
	{
		this.ajaxRequest
		(
			{ route: [ 'api.getSOWsDisplay', { by: '{{$by}}', type: '{{$type}}', q: '{{$q}}' } ] },	
			function(json)
			{
				if (json.length == 0) {
					this.noResults(true);
				} else {
					this.sowEvents(json);
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



<h4>SOWs:</h4>
<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<th data-bind="orderable: { collection: 'sowEvents', field: 'WorkId'}">WorkId</th>
		<th data-bind="orderable: { collection: 'sowEvents', field: 'Status'}">Status</th>
		<th data-bind="orderable: { collection: 'sowEvents', field: 'Subject'}">Subject</th>
		<th data-bind="orderable: { collection: 'sowEvents', field: 'Location'}">Location</th>
		<th data-bind="orderable: { collection: 'sowEvents', field: 'date'}">CreateDate</th>
		<th data-bind="orderable: { collection: 'sowEvents', field: 'dueby'}">DueBy</th>
		<th data-bind="orderable: { collection: 'sowEvents', field: 'rstop'}">StopDate</th>
		<th>FEs</th>
	</thead>
	<tbody data-bind="foreach: sowEvents, visible: sowEvents().length > 0">
		<tr>
			<td>
				<a data-bind="attr: { href: '{{ Config::get('constants.props.starfishLink')}}' + $data.WorkId}, text: $data.WorkId" target="_starfish"></a>
			</td>
			<td>
				<span data-bind="text: $data.Status"></span>
			</td>
			<td>
				<span data-bind="text: _.trunc($data.Subject,60)"></span>
			</td>
			<td>
				<span data-bind="text: _.trunc($data.Location,40)"></span>
			</td>
			<td>
				<span data-bind="text: moment($data.date).format('YYYY-MM-DD HH:mm')"></span>
			</td>
			<td>
				<span data-bind="text: $data.dueby == '1900-01-01 00:00:00.000' ? '' : moment($data.dueby).format('YYYY-MM-DD HH:mm')"></span>
			</td>
			<td>
				<span data-bind="text: $data.rstop == '1970-01-01 00:00:00.000' ? '' : moment($data.rstop).format('YYYY-MM-DD HH:mm')"></span>
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