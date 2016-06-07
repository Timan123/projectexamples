@extends("cogent::layouts/default")

@section('title') ipaddr @stop

@section('brand-title') ipaddr @stop


@section('navBar')
<ul class="nav navbar-nav">
	<li class="">
		<a href="./">IPUse</a>
	</li>
	<li class="">
		<a href="customers">Customers</a>
	</li>
	<li class="">
		<a href="orders">Orders</a>
	</li>
	<li class="">
		<a href="blocks">Blocks</a>
	</li>
</ul>
@stop

@section('navBarRight')
@stop


@section("filterRow")

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