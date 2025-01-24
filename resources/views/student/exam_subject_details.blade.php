<?php use App\Helper\CustomHelper; ?> 
@extends('layouts.default')
@section('content')
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
										@if ($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										</strong>
									</span>
									
									<h4 class="card-title">{{ $page_title; }}</h4>
									<span class="card" style="color:red"> 
										<b>नोट  :  कृपया उन विषयों को अचयनित करें जिनमें आप विषयों में शामिल नहीं होना चाहते हैं। (Please unselect the subjects in which you don't want to appear for in the exam.)</b>
									</span></br></br>
									
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									<input type="hidden" name='student_id' value='{{ @$estudent_id }}' id='student_id'>
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									  	
										@if(@$master['comExamSubjects'])
										<div class="row">
											<h4 class="card-title">Compulsary Subjects :</h4>
											<p>
												@foreach($master['comExamSubjects'] as $i => $value)
													<div class="col m3 s3 mb-3">
														@php
															$lbl='विषय '. ($i+1) .' (Subject '. ($i+1) .')'; 
															$placeholder ='Select Any';  
															$fld='subject_id[' . $value . ']'; 
														@endphp 
														
														<label>
														<?php 
														$subject_check_status = CustomHelper::examSubjectIsCheckedStatus($student_id,$value);
														$check_attribute = '';
														
														if($subject_check_status){
															$check_attribute = 'checked="checked"';
														}
														if(@$master['examSubjectsCount'] && $master['examSubjectsCount'] > 0 ){}else{
															$check_attribute = 'checked="checked"';
														}
														?>
														
														<input {{ $check_attribute; }}  name="{{ $fld; }}" type="checkbox" value="{{ $value; }}">
														<span>{{ @$subject_list[@$value] }}</span>
														</label>
														@include('elements.field_error')
													</div>
												@endforeach
											</p>  
										</div>
										@endif
										@if(@$master['addiExamSubjects'])
											<div class="row">
												<h4 class="card-title">Additional Subjects :</h4>
												<p>
													@foreach($master['addiExamSubjects'] as $i => $value)
													<div class="col m3 s3 mb-3"> 
														@php
															$lbl='विषय '. ($i+1) .' (Subject '. ($i+1) .')'; 
															$placeholder ='Select Any';  
															$fld='subject_id[' . $value . ']'; 
														@endphp 
														
														<label>
															<?php
															$subject_check_status = CustomHelper::examSubjectIsCheckedStatus($student_id,$value);
															
															$check_attribute = '';
															if($subject_check_status){
																$check_attribute = 'checked="checked"';
															}
															if(@$master['examSubjectsCount'] && $master['examSubjectsCount'] > 0 ){}else{
																$check_attribute = 'checked="checked"';
															}
															?>
															<input {{ $check_attribute; }}  name="{{ $fld; }}" type="checkbox" value="{{ $value; }}">
															<span>{{ @$subject_list[@$value] }}</span>
														</label>
														@include('elements.field_error')
													</div>  
													@endforeach
												</p>
											</div>
										@endif
									</div>
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right btn_disabled" type="submit" name="action"> Save & Continue
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
	@if(COUNT($ExamSubject1) != 0) 
    var form_edit_msg = "Warning : If you save the changed data the upcoming fields will also be reflected according to the changes made.(यदि आप परिवर्तित डेटा सेव करते हैं तो आने वाले फ़ील्ड भी किए गए परिवर्तनों के अनुसार परिवर्तित दिखाई देंगे।)";
	@endif
</script>
	
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/exam_subject_details.js') !!}"></script> 
@endsection 
 


