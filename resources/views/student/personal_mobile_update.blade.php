@extends('layouts.default')
@section('content')
<style>
.b{
   color:#080808;
}
</style>
	<div id="main">
     <div class="row">
       	<div class="col s12">
			<div class="container">
			@if($docrejectednotification == true)
				@if(@$studentdata->enrollment && !empty($studentdata->enrollment))
			    <div class="col s12 m12 l12">
					<div class="card card card-default scrollspy"> 
						<div class="card-content">
						<span style="color:red;font-size:20px;">
						@php 
							echo Config::get('global.student_doc_rejected_notification');
						@endphp
						</span>
						</div>
					</div>
				</div>
				  @endif
				  @endif
				<div class="col s12 m12 l12">
					<div class="card card card-default scrollspy"> 
						@include('elements.student_top_navigation')
					</div>
				</div>
				<div class="col s12 m12 l12">
					<div class="card card card-default scrollspy"> 
						<div class="card-content">
							<h4 class="card-title">{{ $page_title; }} <span class="light right" style="font-size:18px; font-weight: bold;color:#228B22;">{{@$aiCenters[@$studentdata->ai_code]}}</span></h4>
						</div>
					</div>
				</div>  
				
				@if(@$studentdata->is_otp_verified)
							@else 
				<div class="col s12">
					<div class="card">
						<div class="card-content">
						<h4 class="card-title">Mobile Number Verificaton</h4>
						<span>
							@if(@$studentdata->is_otp_verified)
							@else 
								<span style="font-size:16px;color:blue;">
								<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/> Note : To complete the form filling process should verify your registered mobile number.
									(नोट: फॉर्म भरने की प्रक्रिया को पूरा करने के लिए अपना पंजीकृत मोबाइल नंबर सत्यापित करें।)
								</span>
							@endif
						</span>
						@include('elements.student_mobile_verification')
						</div>
					</div>
				</div>
				@endif
				@if(@$studentdata->is_otp_verified)
					<div class="col s12 m12 l12 b basic_details_cls ">
						<div id="Form-advance" class="card card card-default scrollspy">
						<div class="card-content">
						<h4 class="card-title">Basic Details</h4> 
							@include('elements.ajax_validation_block')
							{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
							{!! method_field('PUT') !!}
								<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
								<input type="hidden" name='sid' value='{{ $estudent_id }}' class='student_id' id='sid'>
							<div class="row ">
								<div class="col m3 s12 ">
			
									@php $lbl='मोबाइल (Mobile)'; $fld='mobile'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark') . $extra; @endphp </span>
									<div class="input-field">
										{!!Form::text($fld,@$studentdata->$fld,['readonly' => 'readonly','type'=>'text','class'=>'form-control num','autocomplete'=>'off','maxlength' => 10,'placeholder' => $lbl]); !!}
										@include('elements.field_error')	
									</div>
									</div>
								
										<div class="input-field">
										{!! Form::select($fld,@$pre_qualifi,@$applicationdata->$fld, ['id' => $fld,'class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
										
										@include('elements.field_error')	
										</div>
										</div>
									</div>

							<br>
							<div class="row">
								<div class="col m10 s12 mb-3">
									<button class="btn cyan waves-effect waves-light right  btn_disable" type="submit" name="action"> Save & Continue
									</button>
								</div>
								<div class="col m2 s10 mb-3 ">
										<button class="btn cyan waves-effect waves-light right" type="reset">Reset
										</button>
									</div>
								</div> 
						{{ Form::close() }}
						</div>
						</div>
						</div>
					</div>
				@else
					
				@endif
			</div>
		</div>
	</div>
</div>
<script>
	var form_edit_msg='';
	@if(!empty($studentdata->course)) 
    var form_edit_msg = "Warning : If you save the changed data the upcoming fields will also be reflected according to the changes made.(यदि आप परिवर्तित डेटा सेव करते हैं तो आने वाले फ़ील्ड भी किए गए परिवर्तनों के अनुसार परिवर्तित दिखाई देंगे।)";
	@endif  
	var course = '<?php echo $studentdata->course; ?>';
	
</script>

@endsection 
@section('customjs')
<script src="{!! asset('public/app-assets/js/bladejs/personal_details.js') !!}"></script> 
@endsection
