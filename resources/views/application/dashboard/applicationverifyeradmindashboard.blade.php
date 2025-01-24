@extends('layouts.default')
@section('content')  
	@include('elements.dashboard_ui_notifications')
	@can('ao_verification_dashboard') 
		@include('elements.dashbaord.verification.ao_verification_sumamry_dashboard')
	@endcan 
@endsection 

