@extends('layouts.default')
@section('content')

<div id="main">
	<div class="row">
	<div class="col s12">
		@include('elements.breadcrumbs')
		@include('elements.material.download')
		@include('elements.material.generate_download')
		@if($user_role == $developeradminrole)
		@include('elements.material.generate_bulk')
		@endif
	</div>
</div> 
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/singleexamcenter_details.js') !!}"></script> 
@endsection 

