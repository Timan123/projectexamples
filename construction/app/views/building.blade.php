@extends('layouts.default')

@section('title')
Buildings
@stop

@section('pageHeaderTitle')
Buildings - {{$type}} for {{$q}}
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		buildings: [],
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
			{ route: [ 'api.getBuildingsDisplay', { by: '{{$by}}', type: '{{$type}}', q: '{{$q}}' } ] },	
			function(json)
			{
				if (json.length == 0) {
					this.noResults(true);
				} else {
					this.buildings(json);
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


<h4>Buildings:</h4>
<table class="table table-striped table-responsive table-condensed small" id="buildingsTable">
	<thead>
		<th data-bind="orderable: { collection: 'buildings', field: 'BuildingID'}">Building</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'NodeNumber'}">Node</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'Address'}">Address</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'City'}">City</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'State'}">State</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'ZipCD'}">Zip</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'OnNetDt'}">OnNetDt</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'ConstructionStatusDesc'}">ConstructionStatus</th>
		<th data-bind="orderable: { collection: 'buildings', field: 'ConstructionManager'}">ConstructionManager</th>
	</thead>
	<tbody data-bind="foreach: buildings, visible: buildings().length > 0">
		<tr>
			<td>
				<a data-bind="attr: { href: '{{ Config::get('constants.props.bobLink')}}' + $data.BoBID}, text: $data.BuildingID" target="_bob"></a>
			</td>
			<td>
				<span data-bind="text: $data.NodeNumber"></span>
			</td>	
			
			<td>
				<span data-bind="text: $data.Address"></span>
			</td>
			<td>
				<span data-bind="text: $data.City"></span>
			<td>
				<span data-bind="text: $data.State"></span>
			</td>
			<td>
				<span data-bind="text: $data.ZipCD"></span>
			</td>
			<td>
				<span data-bind="text: $data.OnNetDt"></span>
			</td>
			<td>
				<span data-bind="text: $data.ConstructionStatusDesc"></span>
			</td>
			<td>
				<span data-bind="text: $data.ConstructionManager"></span>
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