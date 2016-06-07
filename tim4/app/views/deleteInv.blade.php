@extends('layouts.default')

@section('title')
TIM - Delete Invoice
@stop

@section('pageHeaderTitle')
Delete Invoice
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		gpInvoiceNum: 'TLINV_',
		reason: null
	},
	initialize: function() {

	},
	autoTriggerFilterFormSubmit: false,
	submitFilter: function(element, event) {
	this.ajaxRequest
		(
			{ route: [ 'api.deleteInv', { gpInvoiceNum: this.gpInvoiceNum(), reason: escape(this.reason()) } ] },	
			function(json)
			{
				if (json == 'No Invoice') {
					app.notify('No invoice with that number', { type: 'warning' });
				} else {
					app.notify(json + ' deleted', { type: 'success' });
				}
				//do nothing
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

<form action="/{{ $goBackRoutePath }}" method="get" id="filterForm" data-no-api-prefix="1" data-bind="submit: submitFilter.bind($root)">
<div class="row">
	
	<div class="col-md-2 form-group ">
		<label class="control-label" for="gpInvoiceNum">GP Number:</label>
		<input type="text" required name="gpInvoiceNum" id="gpInvoiceNum" class="form-control input-sm " data-bind="value: gpInvoiceNum"/>
	</div>
	<div class="col-md-4 form-group ">
		<label class="control-label" for="reason">Reason:</label>
		<input type="text" name="reason" id="reason" class="form-control input-sm" data-bind="value: reason"/>
	</div>
</div>
<div>
	
	<button type="submit" class="btn btn-primary" name="Submit" value="Submit" >
	<i class="fa fa-spinner fa-pulse" data-bind="visible: isLoading"></i>
	Submit
	</button>
	
</div>
</form>



@stop