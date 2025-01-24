@extends('layouts.default')
@section('content')

	@can('dept_verification_dashboard')	
		@include('elements.dashbaord.verification.department_verification_sumamry_dashboard')
	@endcan
	@can('dept_change_request_dashboard')	
		@include('elements.dashbaord.department_change_request_sumamry_dashboard')
	@endcan
	@can('supp_change_request_dashboard')	
	{{--@include('elements.dashbaord.supp_change_request_sumamry_dashboard')--}}	@include('elements.dashbaord.examination_department_change_request_sumamry_supp_dashboard')-
	
	@endcan
	@can('Student_registration_dashboard')
		@include('elements.dashbaord.student_applications')
	@endcan
	@can('Supplementary_student_dashboard')
		@include('elements.dashbaord.spplementary_student_applications')
	@endcan
	@can('Reval_student_dashboard')
		{{--@include('elements.dashbaord.reval_student_applications') --}}
	@endcan
	@can('deo_examiner_section')
		{{-- @include('elements.dashbaord.deo_examiner_section') --}}
	@endcan
	
@endsection 