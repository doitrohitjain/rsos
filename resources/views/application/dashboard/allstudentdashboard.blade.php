@extends('layouts.logindefault')
@section('content')
@include('elements.dashboard_ui_notifications')
@php
$role=config("global.student");
@endphp


<div class="container"><div id="login-page" class="row">
	<div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
   	<div class="row">
		<div class="input-field col s12">
			<h5 class="ml-4">नामांकन चुनें(Choose Enrollment)</h5>
		  </div>
		</div>
	  <div id="view-radio-buttons">
		  {{ Form::open(['route' => ['dashboard'], 'class' => "login-form"]) }}
		  {!! Form::token() !!}
		   @foreach($enrollmentLable as $results => $v)
		   <input name="role" type="hidden" value="{{$role}}">
		   <input name="student_multi_login" type="hidden" value=true>
			<p class="mb-1">
			 <label>
			   <input name="enrollment" type="radio" value="{{$results}}">
		
			   <span style="font-weight: bold;font-size:20px;">{{@$v}}</span>
			 </label>
		   </p>
		   @endforeach<br>
		   <div class="row">
		    <div class="step-actions right">
										  <div class="row">
											<div class="col m5 s12 mb-3">
											  <a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text " style="">Cancel</a> 
											</div>
											<div class="col m1 s12 mb-3">
											<button class="btn cyan waves-effect waves-light " type="submit" name="action">Login
											<i class="material-icons right">send</i>
											</button>
											</div>
										  </div>
										</div>
										</div>
		                {{ Form::close() }}
	                     </div>
						 </div>
  </div>
		  </div>
@endsection
