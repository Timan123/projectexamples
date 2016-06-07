@extends('layouts.default')

@section('filterRow')
@stop

@section('loadingIcon')
<span class="btn" data-bind="visible: isLoading"><i class="fa fa-spinner fa-pulse"></i></span>
@stop

@section('pageHeaderButtons')
@stop

@section('pageHeaderRight')
<div class="btn-group">
	@yield('pageHeaderButtons')
</div>
@stop

@section('content')
@parent
<div class="page-header">
	<div class="pull-right">
		@yield('loadingIcon')
		@yield('pageHeaderRight')
	</div>

	<h1>
		@yield('pageHeaderTitle')
		<small>@yield('pageHeaderSubTitle')</small>
	</h1>
</div>

@yield('filterRow')
@stop