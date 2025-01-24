@extends('layouts.default')
@section('content')
<style>
.b{
   color:#080808;
}
</style>
@php $startMark = Config::get('global.starMark'); @endphp
@if(@$is_dgs_student && $is_dgs_student == true)
	@php $startMark = null; @endphp
@endif  

@php $readonlyVar = null;@endphp
@if($studentdata->student_change_requests == 2 && $changerequeststudent->student_update_application == 1)
@php $readonlyVar = 'readonly'; @endphp
@endif 
	<div id="main">
     <div class="row">
       	<div class="col s12">
			<div class="container">
			@if(@$studentdata->student_change_requests == 2)
			@else
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
								{!! Form::hidden('is_dgs_student',@$is_dgs_student,['type'=>'text','id'=>'is_dgs_student','value'=>@$is_dgs_student]); !!}
								<div class="row">
									<div class="col m12 s12">
									@php $lbl='एआई कोड  (Ai code)'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$aiCenters,@$studentdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')
									</div>
									</div>
								</div>
							<div class="row ">
								<div class="col m3 s12 ">
									@if($studentdata->stream == 1 || $studentdata->stream == 2)
										@php $lbl='प्रवेश प्रकार (Admission Type)'; $placeholder = "Select ". $lbl; $fld='adm_type'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$adm_types,@$studentdata->$fld,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype adm_type admtypes','placeholder' => $placeholder,'id'=>'admtype']) !!}
										@include('elements.field_error')

										@elseif($studentdata->stream == 2)

										@php $lbl='प्रवेश प्रकार (Admission Type)'; $placeholder = "Select ". $lbl; $fld='adm_type'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										<input id="" type="text" value="{{@$adm_types[@$studentdata->$fld]}}" disabled>
										<input type="hidden" name="adm_type" value="{{@$studentdata->$fld}}" class="adm_type" >
										@endif
									</div>
									</div>
									@php $lbl='नामांकन संख्या (Enrollment)'; $fld = "enrollment"; @endphp
									@if(!empty(@$studentdata->$fld)) 
									<div class="col m3 s12">
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									<input id="first_name01" type="text" value="{{@$studentdata->$fld}}" disabled>
									</div>
									</div>	
								@endif
								<div class="col m3 s12">
									@php $lbl='पाठ्यक्रम (Course)'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$course,@$studentdata->$fld, ['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype course','placeholder' => $placeholder,'id'=>'course' ]) !!}
									
									@include('elements.field_error')
									</div>
									</div>
								@php $fld = "enrollment"; @endphp
								@if(!empty(@$studentdata->$fld))
									<div class="col m3 s12">
									@php $lbl='परीक्षा (Exam)'; $fld = "exam_month"; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									<input id="first_name01" type="text" value="{{@$exam_session[@$studentdata->$fld]}}" disabled>
									</div>
									</div>			
								@else
								<div class="col m6 s12">
									@php $lbl='परीक्षा (Exam)'; $fld = "exam_month"; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									<input id="first_name01" type="text" value="{{@$exam_session[@$studentdata->$fld]}}" disabled>
									</div>
									</div> 
								@endif		
							</div>
							<div class="row">
								@if(@$allowSsoInput)
								<div class="col m3 s12">
									@php $lbl='एसएसओआईडी(SSOID)'; $placeholder = "Select ". $lbl; $fld='ssoid'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp <span class="validinvalidcls"></span> </span>
									<div class="input-field">
									{!!Form::text($fld,@$studentdata->ssoid,['type'=>'text','class'=>'form-control sso_id ssoinput','autocomplete'=>'off','placeholder' => $lbl]); !!}
									
									@include('elements.field_error')
									</div>
									</div>
								@else
								<div class="col m3 s12">
									@php $lbl='एसएसओआईडी (SSOID)'; $placeholder = "Select ". $lbl; $fld='ssoid'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									<input id="first_name01" type="text" name="ssoid" value="{{@$studentdata->ssoid}}" readonly>
									</div>
									</div>
								@endif
									<div class="col m3 s12">
										@php $lbl="प्रथम नाम (First Name)"; $fld='first_name'; @endphp 
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'form-control uppercase','autocomplete'=>'off','placeholder' => $lbl,$readonlyVar]); !!}
											@include('elements.field_error')	
										</div>
									</div>
									<div class="col m3 s12">
										@php $lbl="मध्य नाम (Middle Name)"; $fld='middle_name'; @endphp 
										<span class="small_lable">@php echo $lbl; @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'form-control  uppercase' ,'autocomplete'=>'off','placeholder' => $lbl,$readonlyVar]); !!}
											@include('elements.field_error')	
										</div>
									</div>
									<div class="col m3 s12">
										@php $lbl="अंतिम नाम (Last Name)"; $fld='last_name'; @endphp 
										<span class="small_lable">@php echo $lbl; @endphp </span>
										<div class="input-field">										{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'uppercase form-control','autocomplete'=>'off','placeholder' => $lbl,$readonlyVar]); !!}
											@include('elements.field_error')	
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col m3 s12">
									@php $lbl="पिता का नाम (Father's Name)"; $lbl; $fld='father_name'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
										{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'uppercase form-control','autocomplete'=>'off','placeholder' => $lbl,$readonlyVar]); !!}
										@include('elements.field_error')	
									</div>
									</div>
								<div class="col m3 s12">
									@php $lbl="माँ का नाम (Mother's Name)"; $lbl; $fld='mother_name'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
										{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>' uppercase form-control','autocomplete'=>'off','placeholder' => $lbl,$readonlyVar]); !!}
										@include('elements.field_error')	
									</div>
									</div>
									@php $fld1='are_you_from_rajasthan'; @endphp
									@if($studentdata->$fld1 == 1)
									<div class="col m3 s12">
									@php $lbl='राजस्थान राज्य से हैं? IS Rajasthani'; $placeholder = "Select ". $lbl; $fld='are_you_from_rajasthan'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									<input  type="text" name ="are_you_from_rajasthan" value="{{@$are_you_from_rajasthan[@$studentdata->$fld]}}" disabled>
									</div>
									</div>
									@elseif($studentdata->$fld1 == 2)
									<div class="col m6 s12">
									@php $lbl='राजस्थान राज्य से हैं? IS Rajasthani'; $placeholder = "Select ". $lbl; $fld='are_you_from_rajasthan'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									<input  type="text" name ="are_you_from_rajasthan" value="{{@$are_you_from_rajasthan[@$studentdata->$fld]}}" disabled>
									</div>
									</div>
									@endif
								@php $fld1='are_you_from_rajasthan'; @endphp
								@if($studentdata->$fld1 == 1)
								<div class="col m3 s12">
									@php $lbl='जन आधार नंबर (Jan Aadhar)'; $lbl; $fld='jan_aadhar_number'; @endphp
									<span class="small_lable">
										@php echo $lbl . Config::get('global.starMark'); @endphp 
									</span>
									<div class="input-field">
										{!!Form::text($fld,@$applicationdata->$fld,['class'=>' uppercase form-control','placeholder' => $lbl,'disabled'=>'disabled']); !!}
										@include('elements.field_error' )	
									</div>
									</div> 
									@endif
								</div>
								<div class="row">
								<div class="col m3 s12">
									@php $lbl='राष्ट्रीयता (Nationality)'; $placeholder = "Select ". $lbl; $fld='nationality'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$nationality,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')	
									</div>
									</div>
									<div class="col m3 s12">
									@php $lbl='धर्म (Religion)'; $placeholder = "Select ". $lbl; $fld='religion_id'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">

									{!! Form::select($fld,@$religion,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')	
									</div>
									</div>
									<div class="col m3 s12">
									@php $lbl='श्रेणी ए (Category A)'; $placeholder = "Select ". $lbl; $fld='category_a'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$categorya,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')
									</div>
									</div>
									<div class="col m3 s12">
									@php $fld='aadhar_number'; @endphp
									
									

									@if(!empty($applicationdata->$fld ))
										@php $lbl='आधार नंबर (Aadhar Number)'; $lbl; $fld='aadhar_number'; @endphp
										<span class="small_lable">@php echo $lbl . @$startMark; @endphp </span>
										<div class="input-field">
										{!!Form::text($fld,@$applicationdata->$fld,['type'=>'text','class'=>' uppercase form-control num','autocomplete'=>'off','maxlength' => 12,'placeholder' => $lbl,'readonly' => 'readonly']); !!}
										
										@include('elements.field_error')
										</div>
									@else
										@php $lbl='आधार नंबर (Aadhar Number)'; $lbl; $fld='aadhar_number'; @endphp
									 
										<span class="small_lable">@php echo $lbl . @$startMark; @endphp </span>
										<div class="input-field">
										{!!Form::text($fld,@$applicationdata->$fld,['type'=>'text','class'=>' uppercase form-control num','autocomplete'=>'off','maxlength' => 12,'placeholder' => $lbl]); !!}
										
										@include('elements.field_error')
										</div>
									@endif
									</div>
									</div>
							<div class="row">
								<div class="col m3 s12">
									@php $lbl='जन्म की तारीख (Date of Birth)(MM-DD-YYYY)'; $placeholder = "Select ". $lbl; $fld='dob'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field"> 
										@if(!empty(@$studentdata->$fld))
										@php
											$dobFormat = @$studentdata->$fld;
											$dobFormat = date("M d, Y",strtotime(@$dobFormat));
										@endphp
										@endif
										{!!Form::text($fld,@$dobFormat,['class'=>'dob form-control datepicker','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,$readonlyVar]); !!}	
								
									</div>
									</div>
								<div class="col m3 s12">
									@php $extra = null;  @endphp
									@if(@$studentdata->is_otp_verified)
										@php $extra = "<span style='color:green;font-size:14px;'><i class='material-icons'>check</i>verified</span>"; @endphp
									@endif
									@php $lbl='मोबाइल (Mobile)'; $fld='mobile'; @endphp
									<span class="small_lable">@php echo $lbl . $startMark . $extra; @endphp </span>
									<div class="input-field">
										{!!Form::text($fld,@$studentdata->$fld,['readonly' => 'readonly','type'=>'text','class'=>'form-control num','autocomplete'=>'off','maxlength' => 10,'placeholder' => $lbl]); !!}
										@include('elements.field_error')	
									</div>
									</div>
								<div class="col m3 s12">
								@php $lbl='लैंडलाइन नंबर (Landline Number)';$fld='landline'; @endphp
									<h8>@php echo $lbl @endphp </h8>
									<div class="input-field">
									{!!Form::text($fld,@$applicationdata->$landline,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>15,'minlength'=>10]); !!}
									@include('elements.field_error')	
									</div>
									</div>
									<div class="col m3 s12">
									@php $lbl='लिंग (Gender)'; $placeholder = "Select ". $lbl; $fld='gender_id'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$gender_id,@$studentdata->$fld, ['class' => 'select2 browser-default form-control center-align gendercls','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')	
									</div>
									</div>
							</div>
							<div class="row">
								<div class="col m3 s12">
									@php $lbl='स्ट्रीम  (Stream)'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$stream_id,@$studentdata->$fld, ['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype stream streams','placeholder' => $placeholder,'id'=>'stream']) !!}
									@include('elements.field_error')
									</div>
								</div>
								<div class="col m3 s12">
									@php $lbl='ईमेल (Email)'; $lbl; $fld='email'; @endphp
									<span class="small_lable">@php echo $lbl; @endphp </span>
									<div class="input-field">
									{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>' uppercase form-control ','autocomplete'=>'off','placeholder' => $lbl]); !!}
									
									@include('elements.field_error')	
									</div>
									</div>
							<div class="col m3 s12">
									@php $lbl='वंचित वर्ग (Disadvantage Group)'; $placeholder = "Select ". $lbl; $fld='disadvantage_group'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$dis_adv_group,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align dis_adv_group_cls','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')
									</div>
									</div>
							<div class="col m3 s12">
								@php $lbl='अध्ययन का माध्यम (Medium of Study)'; $placeholder = "Select ". $lbl; $fld='medium'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$midium,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')	
									</div>
									</div>
									</div>
									<div class="row">
									 <div class="col m3 s12">
									 @php $lbl='शहरी /ग्रामीण (Rural/Urban)'; $placeholder = "Select ". $lbl; $fld='rural_urban'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$rural_urban,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')	
									</div>
									</div>
										<div class="col m3 s12">
									@php $lbl='रोज़गार (Employment)'; $placeholder = "Select ". $lbl; $fld='employment'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
									{!! Form::select($fld,@$employment,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
									
									@include('elements.field_error')	
									</div>
									</div>
									<div class="col m3 s12">
										@php $lbl='दिव्यांगता (Special Abilities)'; $placeholder = "Select ". $lbl; $fld='disability'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$disability,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder,'id'=>$fld]) !!}
										
										@include('elements.field_error')	
										</div>
									</div>
									<div class="row" >
									  @php 
										$fld='disability'; 
										$jansDivShowHide = "display: none;";
										if($applicationdata->$fld != 10 ){
											$jansDivShowHide = "";
										}
										@endphp
									    <div class="col m3 s12" id='source_others_disability' style="{{$jansDivShowHide}}">
										@php $lbl='दिव्यांगता  प्रतिशत(Special Abilities %)'; $placeholder = "Select ". $lbl; $fld='disability_percentage'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$special_abilities_percentage,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
										@include('elements.field_error')	
										</div>
									  </div>
									</div>
									</div>
									
									<div class="row" >
										@php 
										$fld='course'; 
										$janDivShowHide = "";
										if($studentdata->$fld != 12 ){
											$janDivShowHide = "display: none;";
										}
										@endphp
										<div class="col m4 s12" id='source_other1' style="{{$janDivShowHide}}">
										@if($studentdata->stream == 2)
										@php $lbl='10वीं उत्तीर्ण करने का वर्ष (Year of Passing 10th):'; $placeholder = "Select ". $lbl; $fld='year_pass'; @endphp
										@elseif($studentdata->stream == 1)
										@php $lbl='10वीं उत्तीर्ण करने का वर्ष  (Year of Passing 10th):'; $placeholder = "Select ". $lbl; $fld='year_pass'; @endphp
										@endif
										
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$rsos_years,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align year_pass','placeholder' => $placeholder]) !!}
										@include('elements.field_error')	
										</div>
										</div>
										<div class="col m4 s12" id='source_other' style="{{$janDivShowHide}}">
										@if($studentdata->stream == 2)
										@php $lbl='10वीं बोर्ड  (Board 10th)'; $placeholder = "Select ". $lbl; $fld='board'; @endphp
										@elseif($studentdata->stream == 1)
										@php $lbl='10वीं बोर्ड  (Board 10th)'; $placeholder = "Select ". $lbl; $fld='board'; @endphp
										@endif
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$getBoardList,@$applicationdata->$fld, ['id' => $fld,'class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
										@include('elements.field_error')	
										</div>
										</div>
										<div class="col m4 s12">
										@php $lbl='पिछली योग्यता (Previous Qualification)'; $placeholder = "Select ". $lbl; $fld='pre_qualification'; @endphp
										@if($studentdata->course == 12)
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										@elseif($studentdata->course == 10)
										<span class="small_lable">@php echo $lbl; @endphp </span>
										@endif
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
