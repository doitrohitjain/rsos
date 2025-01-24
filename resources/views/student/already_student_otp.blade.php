@extends('layouts.defaultwithguest')
@section('content')
 <div id="login-page">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<p class="caption mb-0">
										<h4>
										<span>{{ @$title }}</span>
										</h4>
									</p>
								</div>
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
								{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id,$essoid], 'id' => $model]) }}
								  
								   	<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name="student" value="{{$estudent_id}}">
									<input type="hidden" name="ssoid" value="{{$essoid}}">
								   <fieldset>
										<legend><span style="font-weight:800;font-size:18px;">SSO Details(एसएसओ विवरण)</span></legend>
										<div class="row">
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='एसएसओआईडी(SSOID)'; $fld='enrollment11'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>
												{!!Form::text($fld,@$ssoDetails['SSOID'],['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='नाम(Name)'; $fld='enrollment'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>											
												{!!Form::text($fld,@$ssoDetails['firstName'] . " " . @$ssoDetails['lastName'],['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='लिंग(Gender)'; $fld='enrollment'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>
												{!!Form::text($fld,@$ssoDetails['gender'],['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'minLength'=>2,'disabled'=>'disabled','autocomplete'=>'off']); !!}
											</div>
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='मोबाइल(Mobile)'; $fld='enrollment'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>
												{!!Form::text($fld,@$ssoDetails['mobile'],['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70, 'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
										</div>
									
									</fieldset>
									<br>
									
									<fieldset>
										<legend><span style="font-weight:800;font-size:18px;">Application Form Details(आवेदन पत्र विवरण)</span></legend>
										<div class="row">
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='एसएसओआईडी(SSOID)'; $fld='enrollment11'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>
												{!!Form::text($fld,$ssoid,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='नामांकन(Enrollment)'; $fld='enrollment'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>
												{!!Form::text($fld,$student->enrollment,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'minLength'=>2,'disabled'=>'disabled','autocomplete'=>'off']); !!}
											</div>
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='नाम(Name)'; $fld='enrollment'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>											
												{!!Form::text($fld,$student->name,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70,'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
											<div class="col m3 s12">
												@php 
												$star = Config::get('global.starMark');
												$lbl='मोबाइल(Mobile)'; $fld='enrollment'; @endphp 
												<span class="extraCss">@php echo $lbl @endphp </span>
												{!!Form::text($fld,"XXXXXX".$mobilenumber,['type'=>'text','class'=>'txtOnly form-control','maxlength'=>70, 'disabled'=>'disabled','minLength'=>2,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
										</div>
									
									</fieldset>
									<div class="row">
										<div class="input-field col s12">
											<span style="color: red;font-size: 20px;">
												Please enter the OTP(one time password) to verify your profile.
												</span><br><span style="color:blue;font-size: 16px;">A code has been sent to {{ "XXXXXX".$mobilenumber}}
											</span>
										</div>
									</div>
									<div class="row">
										<div class="input-field col s12">
											@php 
											$star = Config::get('global.starMark');
											$lbl='ओ.टी.पी(OTP)'; $fld='otp'; @endphp 
											<span class="extraCss">@php echo $lbl @endphp </span>
											{!!Form::text($fld,null,['type'=>'text','class'=>'num  form-control','maxlength'=>6,'placeholder' => 'XXXXXX','minLength'=>6,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
										</div>
									</div>
									 <div class="step-actions">
									 <div class="row">
										<div class="input-field col s12">
											@if(@$oldOtp) 
												Didn't get the OTP <a href="javascript:void(none);" data-link="{{ route('resend_student_otp',[@$estudent_id]) }}" class="disabledCustom">Resend OTP</a>
												<span>Click on resend OTP to get OTP on mobile after </span>
												<span id="div_timer"></span> seconds.
											@endif
										</div>
										   <div class="step-actions right">
										  <div class="row">
											<div class="col m6 s12 mb-3">
											  <button class="green btn btn-reset" type="submit" name="action">
												Validate & <i class="material-icons right">send</i>Continue
											  </button>
											</div>
											<div class="col m3 s12 mb-3">
											  <a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text " style="">Cancel</a> 
											</div>
											<div class="col m1 s12 mb-3">
											  <button class="waves-effect waves dark btn btn-primary next-step" type="reset">
												Reset
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
				</div>
			</div>
		</div>
	</div> 
</div>
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/already_student_otp.js') !!}"></script> 
@endsection 