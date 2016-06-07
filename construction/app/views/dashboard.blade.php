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
//		navBarLoginLoading: false,
//		navBarLoginSuccess: true,
//		navBarLoginError: false,
		allMarkets: false,
		allFEs: false,
		allNA: false,
		allEU: false,
		markets: null,
		marketsForDropdown: [],
		sowRegionEvents: [],
		FEs: null,
		FEsForDropdown: [],
		sowMarketEvents: [],
		buildingsByRegion: [],
		buildingsByFE: [],
		buildingsByMarket: [],
		provRegionEvents: [],
		provMarketEvents: [],
		provFEEvents: [],
		sowFEEvents: [],
		buildings: [],
		tickets: [],
		ticketsByMarket: [],
		ticketsByFE: [],
		hitItOnce: false
	},
	hitMarkets: function() {
		if (this.allMarkets() || this.markets() || this.allNA() || this.allEU()) {
			return true;
		} else {
			return false;
		}
	},
	hitFEs: function() {
		if (this.allFEs() || this.FEs()  || this.allNA() || this.allEU()) {
			return true;
		} else {
			return false;
		}
	},

	autoTriggerFilterFormSubmit: true,
	dependencies: [	{ observable: 'marketsForDropdown', route: 'api.getMarkets', cache: true},
					{ observable: 'FEsForDropdown', route: 'api.getFEs', cache: true}],
//	navBarLoginSubmit: function(form, event)
//	{
//		return true;
//	},
	submitFilter: function (element, event) {
		this.isLoading(true);
		//do all of this at the top instead of 4 times each
		var marketData = null;
		if (this.allNA()) {
			marketData = 'NA';
		} else if (this.allEU()) {
			marketData = 'EU'
		} else {
			marketData = this.markets();
		}
		var FEData = null;
		if (this.allNA()) {
			FEData = 'NA';
		} else if (this.allEU()) {
			FEData = 'EU'
		} else {
			FEData = this.FEs();
		}
		this.ajaxRequest
		(
			{ route: [ 'api.getSOWRegionCounts', {  } ] },	
			function(json)
			{
				this.sowRegionEvents(json);
			},
			this
		);

		this.ajaxRequest
		(
			{ route: [ 'api.getProvRegionCounts', {  } ] },	
			function(json)
			{
				this.provRegionEvents(json);
			},
			this
		);
		if (this.hitMarkets()) {
			this.ajaxRequest
			(
				{ route: 'api.getSOWMarketCounts', data: { markets: marketData } },	
				function(json)
				{
					this.sowMarketEvents(json);
					this.isLoading(false);
				},
				this
			);
		}
		if (this.hitMarkets()) {
			this.ajaxRequest
			(
				{ route: 'api.getProvMarketCounts', data: { markets: marketData }  },	
				function(json)
				{
					this.provMarketEvents(json);
				},
				this
			);
		}
		if (this.hitFEs()) {
			this.ajaxRequest
			(
				{ route: 'api.getSOWFECounts', data: { FEs: FEData } } ,	
				function(json)
				{
					this.sowFEEvents(json);
				},
				this
			);
		}
		if (this.hitFEs()) {
			this.ajaxRequest
			(
				{ route: 'api.getProvFECounts', data: { FEs: FEData } },	
				function(json)
				{
					this.provFEEvents(json);
				},
				this
			);
		}
		this.ajaxRequest
		(
			{ route: [ 'api.getBuildingRegionCounts', {  } ] },	
			function(json)
			{
				this.buildingsByRegion(json);
			},
			this
		);
		if (this.hitMarkets()) {
			this.ajaxRequest
			(
				{ route: 'api.getBuildingMarketCounts', data: { markets: marketData }  },	
				function(json)
				{
					this.buildingsByMarket(json);
				},
				this
			);
		}
		if (this.hitFEs()) {
			this.ajaxRequest
			(
				{ route: 'api.getBuildingFECounts', data: { FEs: FEData } },	
				function(json)
				{
					this.buildingsByFE(json);
				},
				this
			);
		}
		this.ajaxRequest
		(
			{ route: [ 'api.getTicketCounts', {  } ] },	
			function(json)
			{
				this.tickets(json);
			},
			this
		);
		if (this.hitMarkets()) {
			this.ajaxRequest
			(
				{ route: 'api.getTicketMarketCounts', data: { markets: marketData }  },	
				function(json)
				{
					this.ticketsByMarket(json);
				},
				this
			);
		}
		if (this.hitFEs()) {
			this.ajaxRequest
			(
				{ route: 'api.getTicketFECounts', data: { FEs: FEData } },	
				function(json)
				{
					this.ticketsByFE(json);
				},
				this
			);
		}


		
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
		<div class="col-md-1 form-group">
			<label class="control-label" for="allMarkets">Show All Markets:</label>
			<input type="checkbox" name="allMarkets" value="Show All Markets" class="checkbox" data-bind="checked: allMarkets"></input>
		</div>
		<div class="col-md-1 form-group">
			<label class="control-label" for="allMarkets">Show All FE's:</label>
			<input type="checkbox" name="allFEs" value="Show All FEs" class="checkbox" data-bind="checked: allFEs"></input>
		</div>
		<div class="col-md-1 form-group">
			<label class="control-label" for="allNA">Show All NA:</label>
			<input type="checkbox" name="allNA" value="Show All NA" class="checkbox" data-bind="checked: allNA"></input>
		</div>
		<div class="col-md-1 form-group">
			<label class="control-label" for="allEU">Show All EU:</label>
			<input type="checkbox" name="allEU" value="Show All EU" class="checkbox" data-bind="checked: allEU"></input>
		</div>
		<div class="col-md-2 form-group">
			<label class="control-label" for="market">Market:</label>
			<select type="text" name="market" id="market" class="form-control input-sm" data-bind="options: $root.marketsForDropdown, optionsValue: 'Market', optionsText: 'Market', selectedOptions: markets, disable: $root.allMarkets() || $root.allNA() || $root.allEU()" multiple='true'></select>
		</div>
		<div class="col-md-2 form-group">
			<label class="control-label" for="FE">Field Engineer:</label>
			<select type="text" name="FE" id="FE" class="form-control input-sm" data-bind="options: $root.FEsForDropdown, optionsValue: 'Username', optionsText: 'Username', selectedOptions: FEs, disable: $root.allMarkets() || $root.allNA() || $root.allEU()" multiple='true'></select>
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

<script type="text/html" id="sowHeader">
		<th></th>
		<th>Completed Last 5 Days</th>
		<th>Requested Next 10 Days</th>
		<th>Uncompleted > 30 Days Old, 6 Mo Old Max</th>
		<th>Total Outstanding Uncompleted, 6 Mo Old Max</th>
</script>

<h4>SOWs:</h4>
<table class="table table-striped table-responsive table-condensed small" id="dataTable">
	<thead>
		<th></th>
		<th>Completed Last 5 Days</th>
		<th>Requested Next 10 Days</th>
		<th>Uncompleted > 30 Days Old, 6 Mo Old Max</th>
		<th>Total Outstanding Uncompleted, 6 Mo Old Max</th>
	</thead>
	<tbody>
		<!-- ko foreach: $root.sowRegionEvents -->
		

		<tr>
			<td>
				<span data-bind="text: $data.region"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=region&type=compLast5Days&q=' + $data.region}, text:  $data.compLast5Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=region&type=reqNext10Days&q=' + $data.region}, text:  $data.reqNext10Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=region&type=unCompOver30DaysOld&q=' + $data.region}, text:  $data.unCompOver30DaysOld"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=region&type=totalUnComp&q=' + $data.region}, text:  $data.totalUnComp"></a>
			</td>
		</tr>
		<!-- /ko -->
		<tr class="black" data-bind="visible: $root.sowMarketEvents().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.sowMarketEvents -->
		<!-- ko if: ($index() % 25 === 0 && $index() != 0  ) -->
			<tr data-bind="template: 'sowHeader'"></tr>
        <!-- /ko -->
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=market&type=compLast5Days&q=' + $data.market}, text:  $data.compLast5Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=market&type=reqNext10Days&q=' + $data.market}, text:  $data.reqNext10Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=market&type=unCompOver30DaysOld&q=' + $data.market}, text:  $data.unCompOver30DaysOld"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=market&type=totalUnComp&q=' + $data.market}, text:  $data.totalUnComp"></a>
			</td>
		</tr>
		<!-- /ko -->
		
		<tr class="black" data-bind="visible: $root.sowFEEvents().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.sowFEEvents -->
		<!-- ko if: ($index() % 25 === 0  ) -->
			<tr data-bind="template: 'sowHeader'"></tr>
        <!-- /ko -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=FE&type=compLast5Days&q=' + $data.FE}, text:  $data.compLast5Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=FE&type=reqNext10Days&q=' + $data.FE}, text:  $data.reqNext10Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=FE&type=unCompOver30DaysOld&q=' + $data.FE}, text:  $data.unCompOver30DaysOld"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'SOW?by=FE&type=totalUnComp&q=' + $data.FE}, text:  $data.totalUnComp"></a>
			</td>
		</tr>
		<!-- /ko -->
		
		
</table>
<br>
<script type="text/html" id="provHeader">
		<th></th>
		<th>Active Work Orders</th>
		<th>Number of Open Work Orders</th>
		<th>Open Work Orders older than 14 days</th>
		<th>Customer Hold Work Orders</th>
</script>
<h4>Provisioning Work Orders:</h4>
<table class="table table-striped table-responsive table-condensed small" id="dataTable2">
	<thead>
		<th></th>
		<th>Active Work Orders</th>
		<th>Open Work Orders</th>
		<th>Open Work Orders older than 14 days</th>
		<th>Customer Hold Work Orders</th>
	</thead>
	<tbody>
		<!-- ko foreach: $root.provRegionEvents -->
		<tr>
			<td>
				<span data-bind="text: $data.region"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=region&type=totalActive&q=' + $data.region}, text:  $data.totalActive"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=region&type=totalOpen&q=' + $data.region}, text:  $data.totalOpen"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=region&type=totalOpenOlder14Days&q=' + $data.region}, text:  $data.totalOpenOlder14Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=region&type=totalHold&q=' + $data.region}, text:  $data.totalHold"></a>
			</td>
		</tr>
		<!-- /ko -->
		<tr class="black" data-bind="visible: $root.provMarketEvents().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.provMarketEvents -->
		<!-- ko if: ($index() % 25 === 0 && $index() != 0  ) -->
			<tr data-bind="template: 'provHeader'"></tr>
        <!-- /ko -->
		
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=market&type=totalActive&q=' + $data.market}, text:  $data.totalActive"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=market&type=totalOpen&q=' + $data.market}, text:  $data.totalOpen"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=market&type=totalOpenOlder14Days&q=' + $data.market}, text:  $data.totalOpenOlder14Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=market&type=totalHold&q=' + $data.market}, text:  $data.totalHold"></a>
			</td>
		</tr>
		<!-- /ko -->
		<tr class="black" data-bind="visible: $root.provFEEvents().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.provFEEvents -->
		<!-- ko if: ($index() % 25 === 0 ) -->
			<tr data-bind="template: 'provHeader'"></tr>
        <!-- /ko -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=FE&type=totalActive&q=' + $data.FE}, text:  $data.totalActive"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=FE&type=totalOpen&q=' + $data.FE}, text:  $data.totalOpen"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=FE&type=totalOpenOlder14Days&q=' + $data.FE}, text:  $data.totalOpenOlder14Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'prov?by=FE&type=totalHold&q=' + $data.FE}, text:  $data.totalHold"></a>
			</td>
		</tr>
		<!-- /ko -->
		
		
		
	</tbody>
</table>
<br>
<script type="text/html" id="buildingHeader">
		<th></th>
		<th>New Lights Last 15 days</th>
		<th>New Lights Next 30 days</th>
		<th>Buildings In Progress (Scheduled, In Progress, or On Hold)</th>
		<th>RE1 and LicenseAgreement Last 30 Days</th>
</script>
<h4>New Building Lights:</h4>
<table class="table table-striped table-responsive table-condensed small" id="dataTable3">
	<thead>
		<th></th>
		<th>New Lights Last 15 days</th>
		<th>New Lights Next 30 days</th>
		<th>Buildings In Progress (Scheduled, In Progress, or On Hold)</th>
		<th>RE1 and LicenseAgreement Last 30 Days</th>
	</thead>
	<tbody>
		<!-- ko foreach: $root.buildingsByRegion -->
		<tr>
			<td>
				<span data-bind="text: $data.region"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=region&type=last15Days&q=' + $data.region}, text:  $data.last15Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=region&type=next30Days&q=' + $data.region}, text:  $data.next30Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=region&type=inProgress&q=' + $data.region}, text:  $data.inProgress"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=region&type=RE1&q=' + $data.region}, text:  $data.RE1"></a>
			</td>
		</tr>
		<!-- /ko -->
		<tr class="black" data-bind="visible: $root.buildingsByMarket().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.buildingsByMarket -->
		<!-- ko if: ($index() % 25 === 0 && $index() != 0  ) -->
			<tr data-bind="template: 'buildingHeader'"></tr>
        <!-- /ko -->
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=market&type=last15Days&q=' + $data.market}, text:  $data.last15Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=market&type=next30Days&q=' + $data.market}, text:  $data.next30Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=market&type=inProgress&q=' + $data.market}, text:  $data.inProgress"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=market&type=RE1&q=' + $data.market}, text:  $data.RE1"></a>
			</td>
		</tr>
		<!-- /ko -->
		<tr class="black" data-bind="visible: $root.buildingsByFE().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.buildingsByFE -->
		<!-- ko if: ($index() % 25 === 0) -->
			<tr data-bind="template: 'buildingHeader'"></tr>
        <!-- /ko -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=FE&type=last15Days&q=' + $data.FE}, text:  $data.last15Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=FE&type=next30Days&q=' + $data.FE}, text:  $data.next30Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=FE&type=inProgress&q=' + $data.FE}, text:  $data.inProgress"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'building?by=FE&type=RE1&q=' + $data.FE}, text:  $data.RE1"></a>
			</td>
		</tr>
		<!-- /ko -->
	</tbody>
	
</table>
<br>
<script type="text/html" id="ticketHeader">
		<th></th>
		<th>P1</th>
		<th>P2</th>
		<th>P3</th>
		<th>Tickets closed within the last 5 days</th>
		<th>Open Tickets older than 15 days</th>
		<th>Next Action = NOC Awaiting FE</th>
</script>
<h4>Dispatch Remedy Tickets:</h4>
<table class="table table-striped table-responsive table-condensed small" id="dataTable4">
	<thead>
		<th></th>
		<th>P1</th>
		<th>P2</th>
		<th>P3</th>
		<th>Tickets closed within the last 5 days</th>
		<th>Open Tickets older than 15 days</th>
		<th>Next Action = NOC Awaiting FE</th>
	</thead>
	<tbody>
		<!-- ko foreach: $root.tickets -->
		<tr>
			<td>
				<span data-bind="text: $data.region"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=region&type=P1&q=' + $data.region}, text:  $data.P1"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=region&type=P2&q=' + $data.region}, text:  $data.P2"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=region&type=P3&q=' + $data.region}, text:  $data.P3"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=region&type=closedLast5Days&q=' + $data.region}, text:  $data.closedLast5Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=region&type=older15Days&q=' + $data.region}, text:  $data.older15Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=region&type=nocAwaiting&q=' + $data.region}, text:  $data.nocAwaiting"></a>
			</td>
		</tr>
		<!-- /ko -->
		<tr class="black" data-bind="visible: $root.ticketsByMarket().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.ticketsByMarket -->
		<!-- ko if: ($index() % 25 === 0 && $index() != 0  ) -->
			<tr data-bind="template: 'ticketHeader'"></tr>
        <!-- /ko -->
		<tr>
			<td>
				<span data-bind="text: $data.market"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=market&type=P1&q=' + $data.market}, text:  $data.P1"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=market&type=P2&q=' + $data.market}, text:  $data.P2"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=market&type=P3&q=' + $data.market}, text:  $data.P3"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=market&type=closedLast5Days&q=' + $data.market}, text:  $data.closedLast5Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=market&type=older15Days&q=' + $data.market}, text:  $data.older15Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=market&type=nocAwaiting&q=' + $data.market}, text:  $data.nocAwaiting"></a>
			</td>
		</tr>
		<!-- /ko -->
		<tr class="black" data-bind="visible: $root.ticketsByFE().length > 0">
			<td colspan="100%"></td>
		</tr>
		<!-- ko foreach: $root.ticketsByFE -->
		<!-- ko if: ($index() % 25 === 0 ) -->
			<tr data-bind="template: 'ticketHeader'"></tr>
        <!-- /ko -->
		<tr>
			<td>
				<span data-bind="text: $data.FE"></span>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=FE&type=P1&q=' + $data.FE}, text:  $data.P1"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=FE&type=P2&q=' + $data.FE}, text:  $data.P2"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=FE&type=P3&q=' + $data.FE}, text:  $data.P3"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=FE&type=closedLast5Days&q=' + $data.FE}, text:  $data.closedLast5Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=FE&type=older15Days&q=' + $data.FE}, text:  $data.older15Days"></a>
			</td>
			<td>
				<a data-bind="attr: { href: 'ticket?by=FE&type=nocAwaiting&q=' + $data.FE}, text:  $data.nocAwaiting"></a>
			</td>
		</tr>
		<!-- /ko -->
	</tbody>
</table>


@stop