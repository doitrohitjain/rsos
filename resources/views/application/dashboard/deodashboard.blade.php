@extends('layouts.default')
@section('content') 
	@include('elements.dashboard_ui_notifications')
    @can('deo_examiner_section')
        @include('elements.dashbaord.deo_examiner_section')
    @endcan
@endsection 