@extends('layouts.default')
@section('content')

@php use App\Component\CustomComponent;  @endphp

 <link href="https://fonts.googleapis.com/css?family=Noto+Sans&subset=devanagari" rel="stylesheet">

@php if(@$isPartAdmissionStudent==1) {  @endphp
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">   
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.student_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card-default scrollspy">
							
								<div class="card-content">
									<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										<strong>
										@if($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										 </strong>
									</span>
									
									<h4 class="card-title">{{ $page_title; }}</h4>
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='student_id' value="{{ $estudent_id }}" id='student_id'>
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name="adm_type" class="adm_type" value="<?php echo $studentdata->adm_type; ?>">
									<input type="hidden" name="stream" class="stream" value="<?php echo $studentdata->stream; ?>">
									<input type="hidden" name="course" class="course" value="<?php echo $studentdata->course; ?>">
									
									@php  
									$hideDiv=''; 
									if((isset($application_master->toc) && $application_master->toc!=1) || !isset($application_master->toc)){ 
										$hideDiv='style="display:none;"'; 
									} 
									@endphp
									<div class="row">
										<div class="input-field col s4 board-section">
											@php $lbl='बोर्ड का नाम (Name Of Board) :'; $placeholder = "Select Board"; $fld='board'; @endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$board_dropdown, @$master->$fld, ['class' => 'form-control select2 select2a board browser-default center-align','required'=>'required','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
									
									
										<div class="input-field col s4 pass-year-section" <?php if(empty($master->year_pass)) { echo 'style="display:none;"'; } ?> >
											@php
											$lbl='सफल होने के वर्ष (Years of Passing) :'; $placeholder = "Select Year"; $fld='year_pass';
											@endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$rsos_years_dropdown, @$master->$fld, ['class' => 'form-control select2 select2a browser-default center-align toc-section-field','required'=>'required','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
										
										<div class="input-field col s4 fail-year-section" <?php if(empty($master->year_fail)) { echo 'style="display:none;"'; } ?> >
											@php 
											$lbl='असफल होने के वर्ष (Years of Failing) :'; $placeholder = "Select Year"; $fld='year_fail';
											@endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$rsos_years_fail_dropdown, @$master->$fld, ['class' => 'form-control select2 select2a browser-default center-align toc-section-field','required'=>'required','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
										
										
										<div class="input-field col s4 toc-section" <?php if(empty($master->roll_no)) { echo 'style="display:none;"'; } ?> >
											@php $lbl='अनुक्रमांक (Roll No.) :'; $placeholder = "Select TOC"; $fld='roll_no'; @endphp
											<h6>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h6>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'toc_roll_no form-control toc-section-field','maxlength'=>20,'required'=>'required','minLength'=>5,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
											<span style="color:red" class="toc_roll_no_error"></span>
										</div>
									</div>
									
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Save & Continue
											</button>
										</div>
										<div class="col m2 s10 mb-3">
											<button class="btn cyan waves-effect waves-light right" type="reset">Reset
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
	
	<script>
	var form_edit_msg='You have submitted succesfully.';
	//@if(!empty($application_master->toc) && $application_master->toc!='null') 
    //var form_edit_msg = "Warning : If you save the changed data the upcoming fields will also be reflected according to the changes made.(यदि आप परिवर्तित डेटा सेव करते हैं तो आने वाले फ़ील्ड भी किए गए परिवर्तनों के अनुसार परिवर्तित दिखाई देंगे।)";
	//@endif 
	</script>

	@endsection 
	@section('customjs')
		<script src="{!! asset('public/app-assets/js/bladejs/toc_subject_part_admission_details.js') !!}"></script> 
	@endsection 
	
	@php } else if(@$isImprovementStudent==1){  @endphp
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.student_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
							
								<div class="card-content">
									<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										<strong>
										@if($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										 </strong>
									</span>
									
									<h4 class="card-title">{{ $page_title; }}</h4>
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='student_id' value="{{ $estudent_id }}" id='student_id'>
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name="adm_type" class="adm_type" value="<?php echo $studentdata->adm_type; ?>">
									<input type="hidden" name="stream" class="stream" value="<?php echo $studentdata->stream; ?>">
									<input type="hidden" name="is_toc" class="is_toc" value="1">
									<input type="hidden" name="course" class="course" value="<?php echo $studentdata->course; ?>">
									
									<div class="row">
										<div class="input-field col s8">
											@php $lbl='क्रेडिट का स्थानांतरण (Whether you are applying for Transfer of Credit) :'; $placeholder = "Select TOC"; $fld='is_toc'; @endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$toc_yes_no, '1', ['class' => 'form-control is_toc select2 select2a browser-default center-align','placeholder' =>$placeholder,'required' =>'required','disabled'=>'disabled']) !!}
											@include('elements.field_error')
										</div>
									</div>
									
									@php  
									$hideDiv=''; 
									if((isset($application_master->toc) && $application_master->toc!=1) || !isset($application_master->toc)) { 
										// $hideDiv='style="display:none;"'; 
									} 
									@endphp
									<div class="row">
										<div class="input-field col s4 board-section" <?php echo $hideDiv; ?>>
											@php $lbl='बोर्ड का नाम (Name Of Board) :'; $placeholder = "Select Board"; $fld='board'; @endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$board_dropdown, @$master->$fld, ['class' => 'form-control select2 select2a board browser-default center-align','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
										
										<div class="input-field col s4 pass-year-section" >
											@php
											$improvement_years_dropdown = array('123'=>'2021-22');
											$lbl='सफल होने के वर्ष (Years of Passing) :'; $placeholder = "Select Year"; $fld='year_pass';
											@endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$improvement_years_dropdown, @$master->$fld, ['class' => 'form-control select2 select2a browser-default center-align toc-section-field','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
										
										<div class="input-field col s4 toc-section" <?php echo $hideDiv; ?>>
											@php $lbl='अनुक्रमांक (Roll No.) :'; $placeholder = "Select TOC"; $fld='roll_no'; @endphp
											<h6>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h6>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'toc_roll_no form-control toc-section-field','maxlength'=>20,'minLength'=>5,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
											<span style="color:red" class="toc_roll_no_error"></span>
										</div>
									</div>
									
									
									<div class="row toc-section" <?php echo $hideDiv; ?>>
										<table class="table" style="border:1px solid #444;margin-bottom:1%">
											<thead>
												<th>Sr. No.</th>
												<th>Subject</th> 
												<th>Theory Marks</th>
												<th>Practical Marks</th>
												<th>Total Marks</th>
											</thead>
											 
											<tbody>
												@for($i = 0; $i < $student_subject_count; $i++) 
												<tr>
													<td>{{  $i+1; }}</td>
													<td>
														@php $placeholder = "Select Subject"; $fld='toc_subject['.$i.'][subject_id]'; @endphp
														{!! Form::select($fld,$student_subject_dropdown, @$toc_marks_master[$i]->subject_id, ['class' => 'toc_subject_dropdown form-control select2 select2a browser-default center-align toc-section-field-select','placeholder' =>$placeholder,'subjectsrno'=>$i ]) !!}
														@include('elements.field_error')
													</td>
													<td>
														@php
														$placeholder = "Theory Marks"; $fld='toc_subject['.$i.'][theory]'; @endphp
														{!!Form::text($fld,@$toc_marks_master[$i]->theory,['type'=>'text','id'=>'theory_'.$i,'class'=>'toc_total_calculation form-control num toc-section-field','maxlength'=>3,'minLength'=>1,'autocomplete'=>'off','placeholder'=>$placeholder,'theorysrno'=>$i]); !!}
														@include('elements.field_error')
													</td>
													<td>
														@php $placeholder = "Practical Marks"; $fld='toc_subject['.$i.'][practical]'; @endphp
														{!!Form::text($fld,@$toc_marks_master[$i]->practical,['type'=>'text','id'=>'practical_'.$i,'class'=>'toc_total_calculation form-control num toc-section-field','maxlength'=>3,'minLength'=>1,'autocomplete'=>'off','placeholder'=>$placeholder,'practicalsrno'=>$i]); !!}
														@include('elements.field_error')
													</td>
													<td>
														@php $placeholder = "Total"; $fld='toc_subject['.$i.'][total]'; @endphp
														{!!Form::text($fld,@$toc_marks_master[$i]->total_marks,['type'=>'text','id'=>'total_'.$i,'class'=>'form-control num toc-section-field readonly-field','maxlength'=>3,'minLength'=>1,'autocomplete'=>'off','placeholder'=>$placeholder,'readonly'=>'readonly','totalsrno'=>$i]); !!}
														@include('elements.field_error')
													</td>
												</tr>
												@endfor
											</tbody>
										</table>
									</div>
									
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Save & Continue
											</button>
										</div>
										<div class="col m2 s10 mb-3">
											<button class="btn cyan waves-effect waves-light right" type="reset">Reset
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
	
<script>
	var form_edit_msg='';
	@if(!empty($application_master->toc) && $application_master->toc!='null') 
    var form_edit_msg = "Warning : If you save the changed data the upcoming fields will also be reflected according to the changes made.(यदि आप परिवर्तित डेटा सेव करते हैं तो आने वाले फ़ील्ड भी किए गए परिवर्तनों के अनुसार परिवर्तित दिखाई देंगे।)";
	@endif 
</script>

@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/toc_subject_improvement_details.js') !!}"></script> 
@endsection 

@php } else { @endphp
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
							
								<div class="card-content">
									<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										<strong>
										@if($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										 </strong>
									</span>
									@php
										$route=route('student_applications');
										if(@$role_id == $aoRole){	$route=route('verifying_student_applications',['exam_month' => 'total','ao_status' => 1]);
										}
									@endphp
									<h4 class="card-title">{{ $page_title; }}</h4>
									<h6><a href="{{@$route}}" class="btn btn-xs btn-info right mb-2 mr-1">Back </a></h6>
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'0']); !!}
									<input type="hidden" name='student_id' value="{{ $estudent_id }}" id='student_id'>
									<input type="hidden" name='ajaxRequest' value='0' id='ajaxRequest'>
									<input type="hidden" name="adm_type" class="adm_type" value="<?php echo $studentdata->adm_type; ?>">
									<input type="hidden" name="stream" class="stream" value="<?php echo $studentdata->stream; ?>">
									<input type="hidden" name="course" class="course" value="<?php echo $studentdata->course; ?>">
									
									<div class="row">
										<div class="input-field col s8">
											@php $lbl='क्रेडिट का स्थानांतरण (Whether you are applying for Transfer of Credit) :'; $placeholder = "Select TOC"; $fld='is_toc'; @endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$toc_yes_no, @$application_master->toc, ['class' => 'form-control is_toc select2 select2a browser-default center-align','placeholder' =>$placeholder,'required' =>'required']) !!}
											@include('elements.field_error')
										</div>
									</div>
									
									@php  
									$hideDiv=''; 
									if((isset($application_master->toc) && $application_master->toc!=1) || !isset($application_master->toc)) { 
										$hideDiv='style="display:none;"'; 
									} 
									@endphp
									<div class="row">
										<div class="input-field col s4 board-section" <?php echo $hideDiv; ?>>
											@php $lbl='बोर्ड का नाम (Name Of Board) :'; $placeholder = "Select Board"; $fld='board'; @endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$board_dropdown, @$master->$fld, ['class' => 'form-control select2 select2a board browser-default center-align','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
										
										<div class="input-field col s4 pass-year-section" <?php if(empty($master->year_pass)) { echo 'style="display:none;"'; } ?> >
											@php
											$lbl='सफल होने के वर्ष (Years of Passing) :'; $placeholder = "Select Year"; $fld='year_pass';
											@endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$rsos_years_dropdown, @$master->$fld, ['class' => 'form-control select2 select2a browser-default center-align toc-section-field','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
										
										<div class="input-field col s4 fail-year-section" <?php if(empty($master->year_fail)) { echo 'style="display:none;"'; } ?> >
											@php 
											$lbl='असफल होने के वर्ष (Years of Failing) :'; $placeholder = "Select Year"; $fld='year_fail';
											@endphp
											<h6>@php echo $lbl.Config::get('global.starMark'); @endphp </h6>
											{!! Form::select($fld,$rsos_years_fail_dropdown, @$master->$fld, ['class' => 'year_fail_field form-control select2 select2a browser-default center-align toc-section-field','placeholder' =>$placeholder]) !!}
											@include('elements.field_error')
										</div>
										
										<div class="input-field col s4 toc-section" <?php echo $hideDiv; ?>>
											@php $lbl='अनुक्रमांक (Roll No.) :'; $placeholder = "Select TOC"; $fld='roll_no'; @endphp
											<h6>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h6>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'toc_roll_no form-control toc-section-field','maxlength'=>20,'minLength'=>5,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
											<span style="color:red" class="toc_roll_no_error"></span>
										</div>
									</div>
									
									<div class="row toc-section" >
										<table class="table" style="border:1px solid #444;margin-bottom:1%">
											<thead>
												<th>
													<span style="color:red;font-size:14px;">Note: In case of abnormal cases of TOC marks.Theory, Practical, and Total Marks Should be entered by the user i.e. No auto calculate.if student is absent please enter numeric number zero('0')
													<br>नोट: टीओसी अंकों के असामान्य मामलों के मामले में।थ्योरी, प्रैक्टिकल और कुल अंक उपयोगकर्ता द्वारा दर्ज किए जाने चाहिए यानी कोई ऑटो गणना नहीं।
													यदि छात्र अनुपस्थित है तो कृपया संख्यात्मक संख्या शून्य ('0') दर्ज करें।
												</th>
											</thead>
										</table>
									</div> 

									<div class="row toc-section" <?php echo $hideDiv; ?>>
										<table class="table" style="border:1px solid #444;margin-bottom:1%">
											<thead>
												<th>Sr. No.</th>
												<th>Subject</th> 
												<th>Theory Marks</th>
												<th>Practical Marks</th>
												<th>Total Marks</th>
											</thead>
											 
											<tbody>
												@for($i = 0; $i < $student_subject_count; $i++) 
												<tr>
													<td>{{  $i+1; }}</td>
													<td>
														@php $placeholder = "Select Subject"; $fld='toc_subject['.$i.'][subject_id]'; @endphp
														{!! Form::select($fld,$student_subject_dropdown, @$toc_marks_master[$i]->subject_id, ['class' => 'toc_subject_dropdown form-control select2 select2a browser-default center-align toc-section-field-select','placeholder' =>$placeholder,'subjectsrno'=>$i ]) !!}
														@include('elements.field_error')
													</td>
													<td>
														@php
														$placeholder = "Theory Marks"; $fld='toc_subject['.$i.'][theory]'; @endphp
														{!!Form::text($fld,@$toc_marks_master[$i]->theory,['type'=>'text','id'=>'theory_'.$i,'class'=>' form-control num toc-section-field','maxlength'=>3,'minLength'=>1,'autocomplete'=>'off','placeholder'=>$placeholder,'theorysrno'=>$i]); !!}
														@include('elements.field_error')
													</td>
													<td>
														@php $placeholder = "Practical Marks"; $fld='toc_subject['.$i.'][practical]'; @endphp
														{!!Form::text($fld,@$toc_marks_master[$i]->practical,['type'=>'text','id'=>'practical_'.$i,'class'=>' form-control num toc-section-field','maxlength'=>3,'minLength'=>1,'autocomplete'=>'off','placeholder'=>$placeholder,'practicalsrno'=>$i]); !!}
														@include('elements.field_error')
													</td>
													<td>
														@php $placeholder = "Total"; $fld='toc_subject['.$i.'][total]'; @endphp
														{!!Form::text($fld,@$toc_marks_master[$i]->total_marks,['type'=>'text','id'=>'total_'.$i,'class'=>'form-control num toc-section-field readonly-field','maxlength'=>3,'minLength'=>1,'autocomplete'=>'off','placeholder'=>$placeholder,'totalsrno'=>$i]); !!}
														@include('elements.field_error')
													</td>
												</tr>
												@endfor
											</tbody>
										</table>
									</div>
									
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Save & Continue
											</button>
										</div>
										<div class="col m2 s10 mb-3">
											<button class="btn cyan waves-effect waves-light right" type="reset">Reset
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
	
<script>
	var form_edit_msg='';
	@if(!empty($application_master->toc) && $application_master->toc!='null') 
    var form_edit_msg = "Warning : If you save the changed data the upcoming fields will also be reflected according to the changes made.(यदि आप परिवर्तित डेटा सेव करते हैं तो आने वाले फ़ील्ड भी किए गए परिवर्तनों के अनुसार परिवर्तित दिखाई देंगे।)";
	@endif 
</script>

@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/toc_subject_details.js') !!}"></script> 
@endsection 
 
@php } @endphp


