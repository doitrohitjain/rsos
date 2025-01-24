@extends('layouts.default')
@section('content')
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
      <p class="caption mb-0"><h6>User Edit From <span style="margin-left: 85%;"><a href="{{ route('users.index') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
<h6></p>
    </div>
	</div>
  <div class="row">
    <div class="col s12">
      <div id="html-validations" class="card card-tabs">
        <div class="card-content">
          <div id="html-view-validations">
           {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id],'id' => $model]) !!}
              <div class="row">
               <div class="col m4 s12">
				@php $lbl="कॉलेज का नाम (College Name)"; $fld='college_name'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2,]); !!}
					@include('elements.field_error')	
				</div>
			  </div>
               <div class="col m4 s12">
				@php $lbl="एआई केंद्र कोड (AI Center Code)"; $fld='ai_code'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
					@include('elements.field_error')	
				</div>
			</div>
             <div class="col m4 s12">
				@php $lbl="ssoid(SSOID)"; $fld='ssoid'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,$user->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
					@include('elements.field_error')	
				</div>
			</div>
            </div>
            <div class="row">
              <div class="col m4 s12">
				@php $lbl="ईमेल(Email)"; $fld='email'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
					@include('elements.field_error')	
				</div>
			</div>
            <div class="col m4 s12">
				@php $lbl="उपयोगकर्ता नाम(User name)"; $fld='username'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','maxlength'=>100,'minLength'=>2]); !!}
					@include('elements.field_error')	
				</div>
			</div>
            
             <div class="col m4 s12">
				@php $lbl="स्कूल खाता संख्या(School Account Number)"; $fld='school_account_number'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','maxlength'=>25,'minLength'=>12]); !!}
					@include('elements.field_error')	
				</div>

        
			

			</div>
                 </div>
                     <div class="row">
                      <div class="col m4 s12">
                        @php $lbl="स्कूल खाते का IFSC कोड(School Account's IFSC Code)"; $fld='school_account_ifsc'; @endphp 
                        <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                        <div class="input-field">
                                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>11,'minLength'=>11]); !!}
                          @include('elements.field_error')	
                        </div>
                      </div>

              <div class="col m4 s12">
				@php $lbl="प्रिंसिपल नाम(Principal Name)"; $fld='principal_name'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','maxlength'=>100,'minLength'=>2]); !!}
					@include('elements.field_error')	
				</div>
			</div>
            <div class="col m4 s12">
				@php $lbl="प्रिंसिपल मोबाइल नंबर(Principal Mobile Number)"; $fld='principal_mobile_number'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','maxlength'=>10,'minLength'=>10]); !!}
					@include('elements.field_error')	
				</div>
			</div>
            
             <div class="col m4 s12">
				@php $lbl="नोडल अधिकारी का नाम(Nodal Officer Name)"; $fld='nodal_officer_name'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','maxlength'=>100,'minLength'=>2]); !!}
					@include('elements.field_error')	
				</div>
			</div>
                  
              <div class="col m4 s12">
				@php $lbl="नोडल अधिकारी मोबाइल नंबर(Nodal Officer Mobile Number)"; $fld='nodal_officer_mobile_number'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','maxlength'=>10,'minLength'=>10]); !!}
					@include('elements.field_error')	
				</div>
			</div>
            <div class="col m4 s12">
				@php $lbl="पिन कोड(Pin Code)"; $fld='pincode'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','maxlength'=>6,'minLength'=>6]); !!}
					@include('elements.field_error')	
				</div>
			</div>
            </div>
             <div class="row">
            <div class="col m6 s12">
				@php $lbl='ज़िला (District)'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
				{!! Form::select($fld,@$district_list,null,['class' => 'select2 browser-default form-control district_id','placeholder' => $placeholder,]) !!}
				@include('elements.field_error')
			</div>
            </div>
             <div class="col m6 s12">
             @php $lbl='चयन  ब्लॉक (block):'; $placeholder = "Select ". $lbl; $fld='block_id'; @endphp
              <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
				{!! Form::select($fld,@$block_list,null,['class' => 'select2 browser-default form-control block_id','placeholder' => $placeholder,'id'=>'block']) !!}
				@include('elements.field_error')
           
				</div>
			</div>
                 </div>
                  <div class="row">
				 <div class="input-field col s12">
                  {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control','multiple')) !!}
				   <label for="uname0">Roles*</label>
				   @error('roles')
                  <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
                  @enderror
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
@section('customjs')
<script src="{!! asset('public/app-assets/js/bladejs/useradd_details.js') !!}"></script> 
@endsection





