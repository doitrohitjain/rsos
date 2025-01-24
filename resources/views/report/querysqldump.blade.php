@extends('layouts.default')
@section('content')
  <div id="main">
    <div class="row">
	 <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Enter SQL Query Get Mysql Backup</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                  <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
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
	  <h6></h6>
      <h6>Enter SQL Query Get Mysql Backup<h6>
	 </div>
  </div>
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
			{{ Form::open(['route' => 'querysqldump', 'method' => 'post']) }}
			{!! Form::token() !!}
			<div class="row">
				<div class="input-field col m12 s12">
					@php $lbl='टेबल का नाम (Table Name)'; $placeholder = "Select ". $lbl; $fld='tablename'; @endphp
					<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
					{!! Form::select($fld,$result, @$master->$fld, ['id' => $fld,'class' => 'form-control state_id select2 browser-default center-align','required' => 'required','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
					@include('elements.field_error')
				</div>  
				<div class="input-field col m12 s12">
					@php $lbl='SQL Query दर्ज करें Mysql बैकअप प्राप्त करें (Enter SQL Query Get Mysql Backup)'; $placeholder = "Select ". $lbl; $fld='tablename'; @endphp
					<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
					{!! Form::textarea('query',null, array('class'=>'form-control','rows' => 50, 'cols' => 10, 'style' => 'height:20rem;', 'required' => 'required', 'placeholder' => 'select id from rs_states limit 1;')) !!}
				</div>
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
          {{ Form::close() }}
        </div>
      </div>
    </div> 
	</div>
</div>
</div>
</div>
</div>
@endsection







