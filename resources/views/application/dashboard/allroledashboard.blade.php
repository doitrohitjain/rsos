@extends('layouts.logindefault')
@section('content')
@include('elements.dashboard_ui_notifications')
<div class="container"><div id="login-page" class="row">
	<div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
   	<div class="row">
		<div class="input-field col s12">
			<h5 class="ml-4">Select the correct login authority</h5>
		  </div>
		</div>
		
	  <div id="view-radio-buttons">
		  {{ Form::open(['route' => [request()->route()->getAction()['as']], 'class' => "login-form"]) }}
		  {!! Form::token() !!}
		  @php 
			$result = $result->sortBy('name');
		  @endphp
		   @foreach($result as $results)
			<p class="mb-1">
			 <label>
			   <input name="role" type="radio" value="{{@$results->role_id}}">
			   <span style="font-weight: bold;font-size:20px;">{{@$results->name}}</span>
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
