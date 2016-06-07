@extends("cogent::layouts/default")

@section('title')
TIM
@stop

@section('brand-title')
TIM
@stop

@section('navBar')
<ul class="nav navbar-nav">
	<li class="{{ HTML::isRouteActive('main.index') }}">
		<a href="{{ route('main.index') }}">Main</a>
	</li>
	<li class="{{ HTML::isRouteActive('search.index') }}">
		<a href="{{ route('search.index') }}">Search</a>
	</li>
	<li class="{{ HTML::isRouteActive('link.index') }}">
		<a href="{{ route('link.index') }}">Acc/Cir Mgmt</a>
	</li>
	<li class="{{ HTML::isRouteActive('invAssign.index') }}">
		<a href="{{ route('invAssign.index') }}">Invoice Assignment</a>
	</li>
	<li class="{{ HTML::isRouteActive('invListing.index') }}">
		<a href="{{ route('invListing.index') }}">Invoice Listing</a>
	</li>
	<li class="{{ HTML::isRouteActive('disputes.index') }}">
		<a href="{{ route('disputes.index') }}">Disputes</a>
	</li>
	<!--need a null check here because this banner gets called pre-login and Auth could be null-->
	@if (Auth::user() && Auth::user()->can('tim_admin_update_invoices'))
	<li class="{{ HTML::isRouteActive('deleteInv.index') }}">
		<a href="{{ route('deleteInv.index') }}">Delete Invoice</a>
	</li>
	@endif
	

	<li class="dropdown ">
		<a aria-expanded="false" href="#" class="dropdown-toggle" data-toggle="dropdown">
			Reports <span class="caret"></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li class="{{ HTML::isRouteActive('report.index') }}">
				<a href="{{ route('report.index') }}">TIM Report</a>
			</li>
			<li class="divider"></li>
			<li><a target="_us" href="http://reportingservices/Reports/Pages/Report.aspx?ItemPath=%2fCogent+Reports%2fFinance%2fTIM-Accrual">US Accrual</a></li>
			<li class="divider"></li>
			<li><a target="_ca" href="http://reportingservices/Reports/Pages/Report.aspx?ItemPath=%2fCogent+Reports%2fFinance%2fTIM-Accrual-CA">Canada Accrual</a></li>
			<li class="divider"></li>
			<li><a target="_audit" href="http://reportingservices/Reports/Pages/Report.aspx?ItemPath=%2fCogent+Reports%2fFinance%2fLCDB-Update-AuditTrail">LCDB Update Audit Trail</a></li>
		</ul>
	</li>

	
</ul>
@stop



@section("filterRow")
<form action="/{{ $routePath }}" method="get" id="filterForm" data-bind="submit: submitFilter">
</form>
@stop

@section("pageHeaderButtons")
<a href="#" class="btn btn-default" title="{{ Lang::get('cogent::buttons.downloadToCsv') }}" data-bind="tooltip, click: exportCsv, attr: { disabled: tableData().length <= 0 }"><i class="fa fa-file-excel-o"></i></a>
@stop

@section("content")
@parent
<div class="page-header">
	<div class="pull-right btn-group">
		@yield("pageHeaderButtons")
	</div>

	<h1>
		@yield("pageHeaderTitle")
		<small>@yield("pageHeaderSubTitle")</small>
	</h1>
</div>

@yield("filterRow")
@stop