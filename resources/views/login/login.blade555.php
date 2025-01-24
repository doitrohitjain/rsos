@extends('layouts.guest')
@section('content')
<div class="row">
      <div class="col s12">
        <div class="container"><div id="login-page" class="row login-page-fit-size">
  <div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
    <form action="{{route('sdlogin')}}" method="POST">
     @csrf
      <div class="row">
        <div class="input-field col s12">
          <h5 class="ml-3">All Login</h5>
        </div>
      </div>
      <div class="row margin">
        <div class="input-field col s12">
          <i class="material-icons prefix pt-2">person_outline</i>
		   @if($showStatus == true)
			<input id="ssoid" type="text" name="ssoid" autocomplete="off" required value="lokeshprojects">   
		   
		   @else 
			 <input id="ssoid" type="text" name="ssoid" autocomplete="off" required value="">
		   @endif
          
          <label for="ssoid" class="center-align">SSO</label>
        </div>
      </div>
      <div class="row">
        <div class="input-field col s12">
        <button type="submit" class="btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12">Login
        </button>
         
        </div>
      </div>
    </form>
  </div>
</div>
        </div>
        <div class="content-overlay"></div>
      </div>
    </div>@endsection 