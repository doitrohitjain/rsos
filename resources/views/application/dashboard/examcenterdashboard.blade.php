@extends('layouts.default')
@section('content') 
	@include('elements.dashboard_ui_notifications')
    @can('examcenter_section')
        @include('elements.dashbaord.examcenter_section')
    @endcan
@endsection 