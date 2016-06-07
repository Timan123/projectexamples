@extends('layouts.default')

@section('pageHeaderTitle')
TIM
@stop

@section('javascript')
@parent
<script>
app.viewModel = app.baseViewModel.fullExtend(
{
	observables:
	{
		disputeId: null
	},

	autoTriggerFilterFormSubmit: false,

	modalFormSubmit: function(element, event)
	{
		var scope = this,
			form  = $('#modalForm');

		$.ajax(
		{
			method: 'POST',
			url:    '/path/to/submit/form',
			data:   form.serialize()
		}).done(function()
		{
			// Yay, assuming we got a 200 and a success
			window.location = '/path/to/page';

			// Or reload the current page
			window.location = window.location.href;

			// Or just close the modal and move along
			$('#myModal [data-dismiss="modal"]').trigger('click');
		}).fail(function()
		{
			// Assuming the server spit out an error, (ie 404, 500 status code)
			alert('Man, we got an error!');
		});
	}
});   
</script>
@stop

@section("filterRow")
@stop

@section('content')
@parent

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button>


<form action="main/submitDispute" method="post">
	<input type="text" name="field"></input>
	
	<input type="submit" name="submit">Submit</input>
</form>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Modal title</h4>
			</div>

			<form action="#" method="get" id="modalForm" data-bind="submit: modalFormSubmit.bind($root)">
				<div class="modal-body">
					<label for="disputeId" class="control-label">DisputeId</label>
					<input class="input-sm" name="disputeId" id="disputeId" data-bind="value: disputeId">
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Save Changes</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

@stop