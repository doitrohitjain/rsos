@extends('layouts.default')
@section('content')
<style>
	.hidefortemp{
		/* display: none; */
	}
</style>
	<div id="main">
		@php
			$modelContent = @$adminsion_subject_info[1];
		@endphp
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="section">
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
										@if (@$errors->any())
										 @foreach (@$errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										 </strong>
									</span> 
									<h5 style="font-size:26px;font-weight:bold">{{ $page_title;  }} 
									 
								</h5></br>

									
									
									@include('elements.ajax_validation_block') 
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name='student_id' value="{{ $estudent_id }}" id='ajaxRequest'> 
										@if(@$studentdata->are_you_from_rajasthan == 1 && @$studentdata->stream == 1 && @$studentdata->gender_id == 2)
											
										@else
											@if(@$studentdata->stream == 1)
												<div class="hidefortemp">
													<h6 style="font-size:22px;font-weight:bold">What types of learning e-content/books do you enjoy reading?(आप किस प्रकार की शिक्षण ई-सामग्री/किताबें पढ़ना पसंद करते हैं?)
												) :  
													@php $modelContent = "<h3>Types of learning e-content/books</h3>"; @endphp
													<span class="waves-effect waves-light  modal-trigger modalCls blue-text" data-content="{{ @$modelContent }}"><i class="material-icons mr-2"> info_outline </i>
													
													
													</span>
													@php echo Config::get('global.starMark'); @endphp
													 
													</h6>
													<div class="row"> 
														<div class=""> 
															
															<!--<div class="input-field col s8 l12">
																@include('elements.student_subject_faculty_master')
															</div>-->
															
															<div class="input-field col s4 l12">
																<div class="input-field2">
																	<h8>@php  
																		$placeholder = $lbl = 'What types of learning e-content/books do you enjoy reading?(आप किस प्रकार की शिक्षण ई-सामग्री/किताबें पढ़ना पसंद करते हैं?)'; 
																		$fld="book_learning_type_id"; 
																		//echo $lbl.Config::get('global.starMark');
																		// echo Config::get('global.starMark');
																		@endphp </h8>
																	{!! Form::select($fld,@$book_learning_type,@$studentdata->$fld,
																		[
																			'class' => 'subject_id select2 browser-default form-control center-align',
																			'id' => 'book_learning_type_id',
																			'placeholder' => 'Select '.$placeholder
																		]
																	) !!}
																	@include('elements.field_error')	
																</div>
															</div>
														</div>
													</div>
												</div>
											@endif
										@endif
									@if(@$studentdata->course == 12 )
										@php $modelContent = "<h3>Preferred Faculty(मुख्य संकाय) </h3>"; @endphp
										<div class="hidefortemp">
											<h6 style="font-size:22px;font-weight:bold">Preferred Faculty(मुख्य संकाय) :
											@if(@$studentdata->course == 12 )
												<span class="waves-effect waves-light  modal-trigger modalCls blue-text" data-content="{{	 @$modelContent }}"><i class="material-icons mr-2"> info_outline </i>
											</span>
											@php //echo Config::get('global.starMark'); @endphp
											
											@endif
											</h6>
											<div class="row"> 
												<div class=""> 
													
													<!--<div class="input-field col s8 l12">
														@include('elements.student_subject_faculty_master')
													</div>-->
													
													<div class="input-field col s4 l12">
														<div class="input-field2"> 
															<input type="hidden" name='is_multiple_faculty' value="0" id='is_multiple_faculty'>
															<h8>@php 
																$placeholder = $lbl = 'Preferred Faculty(मुख्य संकाय)'; 
																$fld="faculty_type_id"; 
																//echo $lbl.Config::get('global.starMark'); 
																//echo $lbl; 
																@endphp </h8>
															{!! Form::select($fld,@$faculties,@$application->$fld,
																[
																	'class' => 'subject_id select2 browser-default form-control center-align',
																	'id' => 'faculty_type_id',
																	'placeholder' => 'Select '.$placeholder
																]
															) !!}
															@include('elements.field_error')	
														</div>
													</div>
												</div>
											</div>
										</div>
									@endif
									<h6 style="font-size:22px;font-weight:bold">Compulsary Subjects(अनिवार्य विषय) : 

									@if(@$studentdata->course == 12 )
										@php $modelContent = "<h3>Compulsary Subjects(अनिवार्य विषय) </h3>"; @endphp
										<span class="waves-effect waves-light  modal-trigger modalCls blue-text" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>
									@endif
									
									</h6>
									<div class="row">
										@for($i = 0; $i < $cmax_input; $i++)
											<div class="input-field col s4">
												<h6> @php
														$lbl='विषय '. ($i+1) .' (Subject '. ($i+1) .')'; 
														$placeholder ='Select Any';  
														$fld='subject_id[' . $i . ']'; 
													@endphp
												</h6>
												
												<div class="input-field"> 
													<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
													{!! Form::select($fld,@$subject_list,@$master[$i]['subject_id'],
														[
															'class' => 'subject_id subjectselect select2 browser-default form-control center-align',
															'placeholder' => $placeholder
														]
													) !!}
													@include('elements.field_error')	
												</div>
											</div>  							
										@endfor
									</div>
									
									@if($amax_input > 0)
										<div class="row">
											<h6 style="font-size:22px;font-weight:bold">Addtional Subjects (अतिरिक्त विषय):
											@if(@$studentdata->course == 12 )
												@php $modelContent = "<h3>Addtional Subjects (अतिरिक्त विषय):</h3>"; @endphp
												<span class="waves-effect waves-light  modal-trigger modalCls blue-text" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>
											@endif
											</h6>
											@for ($i = 5; $i < $aSubArrCount; $i++)
												<div class="input-field col s4">
													<h6> @php $lbl='विषय '. ($i+1) .' (Subject '. ($i+1) .')'; $placeholder = 'Select Any';  $fld='subject_id[' . $i . ']';  @endphp </h6>
													<div class="input-field">
														{!! Form::select($fld,@$subject_list,@$master[$i]['subject_id'],
														[
															'class' => 'subject_id subjectselect select2 browser-default form-control center-align',
															'placeholder' => $placeholder]
														) !!}
														@include('elements.field_error')	
													</div>
												</div>  
											@endfor
										</div>
									@endif
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right show_confirm" type="submit" name="action"> Save & Continue
											</button>
										</div>
										<div class="col m2 s10 mb-3">
											<button class="btn cyan waves-effect waves-light right reset" type="reset">Reset
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
@section('customjs')
	<script>
		var form_edit_msg='';
		@if(!empty($master[0]['subject_id'])) 
		var form_edit_msg = "Warning : If you save the changed data the upcoming fields will also be reflected according to the changes made.(यदि आप परिवर्तित डेटा सेव करते हैं तो आने वाले फ़ील्ड भी किए गए परिवर्तनों के अनुसार परिवर्तित दिखाई देंगे।)";
		@endif
	</script>
	<script src="{!! asset('public/app-assets/js/bladejs/admission_subject_details.js') !!}"></script> 
@endsection 
 
