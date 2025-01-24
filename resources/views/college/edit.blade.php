 @include('layouts.appheader')
 @include('layouts.appleft')
<!-- BEGIN: Page Main-->
    <div id="main">
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
      <p class="caption mb-0"><h6>Colleges Edit From <span style="margin-left: 85%;"><a href="{{ route('colleges.index') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
<h6></p>
    </div>
	</div>
  <div class="row">
    <div class="col s12">
      <div id="html-validations" class="card card-tabs">
        <div class="card-content">
          <div id="html-view-validations">
           {!! Form::model($college, ['method' => 'PATCH','route' => ['colleges.update', $college->id]]) !!}
              <div class="row">
                <div class="input-field col s12">
                  <label for="uname0">ssoid*</label>
                  {!! Form::text('ssoid', null, array('placeholder' => 'Name','class' => 'form-control','autocomplete'=>'off')) !!}
				   @error('ssoid')
                  <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
                </div>
				 <div class="input-field col s12">
                  <label for="uname0">Email*</label>
                    {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control','autocomplete'=>'off')) !!}
					 <input type="hidden" id="pass" name="password" value="12345678">
				   @error('email')
                  <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
                  @enderror
                </div>
				 <div class="input-field col s12">
                  {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control','multiple')) !!}
				   @error('roles')
				   <label for="uname0">Rols*</label>
                  <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
                  @enderror
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
@include('layouts.centerseeting')
@include('layouts.appfooter')





