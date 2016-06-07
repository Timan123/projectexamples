@extends("cogent::layouts/default")

@section('title') Construction @stop

@section('brand-title') Construction @stop


@section('navBar')
<ul class="nav navbar-nav">
	<li class="{{ HTML::isRouteActive('dashboard.index') }}">
		<a href="{{ route('dashboard.index') }}">Dashboard</a>
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