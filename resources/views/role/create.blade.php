@extends('layouts.default')
@section('content')
<!-- BEGIN: Page Main-->
    <div id="main">
  <div class="row">
  <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Form Layouts</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
                  </li>
                  <li class="breadcrumb-item active">Form Layouts
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
		<div class="col s12">
        <div class="container">
        <div class="seaction">
        	<div class="card">
			<div class="card-content">
	 <h6><a href="{{route('roles.index')}}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6>Role From<h6>
	 </div>
  </div>
    <div class="col s12">
      <div id="html-validations" class="card card-tabs">
        <div class="card-content">
          <div id="html-view-validations">
            {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
              <div class="row">
                <div class="input-field col s12">
                  <label for="uname0">Name*</label>
                  {!! Form::text('name', null, array('class' => 'form-control','autocomplete'=>'off')) !!}
				  <input type="hidden" id="guard_name" name="guard_name" value="web">
				   @error('name')
                  <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
                </div>
                <div class="word-break">
                  <label>T&C *</label>
                  <p>
				   @foreach($permission as $value)
                    <label>
					  {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'validate filled-in')) }}
                     <span>{{ $value->name }}</span>
                     </label>
					 @endforeach
					  @error('permission')
                  <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
                  </p>
                </div>
				<div class="row">
					<div class="col m10 s12 mb-3">
                      <button class="btn cyan waves-effect waves-light right" type="reset">
                        <i class="material-icons right">clear</i>Reset
                      </button>
                    </div>
					<div class="col m2 s12 mb-3">
				  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
              </div>
          {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
 </div>   
 </div>   
 </div>   
 </div>    
@endsection