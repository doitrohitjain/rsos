@extends('layouts.default')
@section('content')
	@include('elements.dashboard_ui_notifications')
	@can('verifier_verification_dashboard')	
		@include('elements.dashbaord.verification.verifier_verification_sumamry_dashboard')
	@endcan
@endsection 

 