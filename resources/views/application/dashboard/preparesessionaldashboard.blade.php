@extends('layouts.default')
@section('content') 
	@include('elements.dashboard_ui_notifications')
	@can('prepare_sessional_student_dashboard')
		@include('elements.dashbaord.prepare_sessional_dashboard')
	@endcan
@endsection 