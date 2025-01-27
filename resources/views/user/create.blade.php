@extends('layouts.default')
@section('content')
<div id="main">
	<div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
        <div class="container">
            <div class="row">
				<div class="col s12 m6 l6">
					<h5 class="breadcrumbs-title mt-0 mb-0"><span>Form Layouts</span></h5>
				</div>
				<div class="col s12 m6 l6 right-align-md">
					<ol class="breadcrumbs mb-0">
						<li class="breadcrumb-item"><a href="index-2.html">Home</a></li>
						<li class="breadcrumb-item"><a href="#">Form</a></li>
						<li class="breadcrumb-item active">Form Layouts</li>
					</ol>
				</div>
            </div>
        </div>
    </div>
	<div class="col s12 m6 l6">
		<div class="container">
			<div class="seaction">
				<div class="card">
					<div class="card-content">
						<p class="caption mb-0">
							<h6>User From 
								<span style="margin-left: 85%;">
									<a href="{{ route('users.index') }}" class="btn btn-xs btn-info pull-right">Back</a>
								</span>
							</h6>
						</p>
					</div>
				</div>
				<div class="row">
					<div class="col s12">
						<div id="html-validations" class="card card-tabs">
							<div class="card-content">
								<h6>User From </h6></br>
								<div id="html-view-validations">
									{!! Form::open(array('route' => 'users.store','method'=>'POST','id' => $model)) !!}
										<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
										<div class="row">
											<div class="col m4 s12">
												@php $lbl="SSOID( SSOID)"; $fld='ssoid'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!!Form::text($fld,null,['type'=>'text','class'=>'form-control sso_id','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
													<input type="hidden" name='type' value='1'>
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="User Name( उपयोगकर्ता नाम)"; $fld='name'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!!Form::text($fld,null,['type'=>'text','class'=>'form-control name','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="Father Name( पिता का नाम)"; $fld='father_name'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
													@include('elements.field_error')	
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col m4 s12">
												@php $lbl="Mother Name( माँ का नाम)"; $fld='mother_name'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">											{!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>100,'minLength'=>2]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl='Date of Birth ( DD-MM-YYYY जन्म की तारीख )'; $placeholder = "Select ". $lbl; $fld='dob'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field"> 
													@if(!empty(@$studentdata->$fld))
													@php 
														$dobFormat = @$studentdata->$fld;
														$dobFormat = date("M d, Y",strtotime(@$dobFormat));
													@endphp
													@endif
													{!!Form::text($fld,@$dobFormat,['class'=>'dob form-control datepicker dateOfBirth','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m4 s12">
												@php $lbl="Email( ईमेल)"; $fld='email'; @endphp 
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!!Form::text($fld,null,['type'=>'text','class'=>'form-control mailPersonal','autocomplete'=>'off','placeholder' => $lbl]); !!}
													@include('elements.field_error')	
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col m3 s12">
												@php $lbl='Gender ( लिंग)'; $placeholder = "Select ". $lbl; $fld='gender_id'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
													{!! Form::select($fld,@$gender_id,null, ['class' => 'select2 browser-default form-control center-align gender','placeholder' => $placeholder]) !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m3 s12">
												@php $lbl='Stream( स्ट्रीम)'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">
													{!! Form::select($fld,@$stream_id,null, ['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype stream streams','placeholder' => $placeholder,'id'=>'stream']) !!}
													@include('elements.field_error')
												</div>
										   </div>
											<div class="col m3 s12">
												@php $lbl="Mobile(मोबाइल)"; $fld='mobile'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">
													{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num mobile','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>10,'minLength'=>10]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m3 s12">
												@php $lbl="Account holder Name ( खाता धारक का नाम)"; $fld='account_holder_name'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-contro','autocomplete'=>'off','placeholder' => $lbl]); !!}
													@include('elements.field_error')	
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col m3 s12">
												@php $lbl="Bank Name( बैंक का नाम)"; $fld='bank_name'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
												  @include('elements.field_error')	
												</div>
											</div>
											<div class="col m3 s12">
												@php $lbl="Account Number( खाता संख्या)"; $fld='account_number'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">
													{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>30,'minLength'=>12]); !!}
													@include('elements.field_error')	
												</div>
											</div>
											<div class="col m3 s12">
												@php $lbl="Account's IFSC Code( खाते का IFSC कोड)"; $fld='ifsc'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">{!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>11,'minLength'=>11]); !!}
												  @include('elements.field_error')	
												</div>
											</div>
											<div class="col m3 s12">
												@php $lbl="Pincode( पिन कोड)"; $fld='pincode'; @endphp 
												<span class="small_lable">@php echo $lbl; @endphp </span>
												<div class="input-field">
													{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num nu','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>6,'minLength'=>6]); !!}
													@include('elements.field_error')	
												</div>
										  </div>
										</div>
										<div class="row">
											<div class="col m6 s12">
												@php $lbl='District(ज़िला )'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!! Form::select($fld,@$district_list,null,['class' => 'select2 browser-default form-control district_id','placeholder' => $placeholder,]) !!}
													@include('elements.field_error')
												</div>
											</div>
											<div class="col m6 s12">
												@php $lbl='Block ( चयन  ब्लॉक):'; $placeholder = "Select ". $lbl; $fld='block_id'; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
													{!! Form::select($fld,@$block_list,null,['class' => 'select2 browser-default form-control block_id','placeholder' => $placeholder,'id'=>'block']) !!}
													@include('elements.field_error')
												</div>
											</div>
										</div>
										@can('role_show_user')
											<div class="row">
												<div class="input-field col s12">
													@php $lbl='Roles ( भूमिकाओं):'; $placeholder = "Select ". $lbl; $fld='Roles'; @endphp
													<span class="small_lable">@php echo $lbl; @endphp </span>
													{!! Form::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}
													@error('roles')
														<span class="invalid-feedback" role="alert">
														  <strong>{{ $message }}</strong>
														</span>
													@enderror
												</div>
											</div>
										@endcan
										<div class="row">
											<div class="col m10 s12 mb-3">
												<a href="{{ route('users.create') }}" class="btn cyan waves-effect waves-light right"> <i class="material-icons right">clear</i>Reset</a>
											</div>
											<div class="col m2 s12 mb-3">
											  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
												<i class="material-icons right">send</i>
											  </button>
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
