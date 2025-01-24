@extends('layouts.default')
@section('content') 
	@include('elements.dashboard_ui_notifications')
	@can('sessional_student_dashboard')
		@include('elements.dashbaord.sessional_dashboard')
	@endcan
@endsection 