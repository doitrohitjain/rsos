@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									<h4 class="card-title">{{ $page_title; }} </h4>
								</div> 
								{{-- @dd($data); --}}
							
								<div class="card card-content">
									<div class="card-content"> 
										<div class="row">
											<div class="col s12 m4 l4">
												<div class="timeline-panel">
													<div class="card m-0 hoverable" id="profile-card">
													  <div class="card-image waves-effect waves-block waves-light">
														<img class="oldactivator responsive-img" src="../../public/app-assets/images/gallery/30.png" alt="user bg">
													  </div>
													  <div class="card-content">
														<img src="{{$student_path}}" alt="" class="circle responsive-img oldactivator card-profile-image orange padding-1">
														<h6 class="card-title oldactivator grey-text text-darken-4 mt-1">{{@$data['student']['name']}}</h6>
														
														@if(@$data['student']['enrollment'])
															<p>Enrollment : <span style="font-size:18px;font-weight:900;">{{@$data['student']['enrollment']}}</span></p>
													    @endif
														
														@if(@$data['student']['father_name'])
															<p>Father's Name : <span style="font-size:16px;font-weight:900;">{{@$data['student']['father_name']}}</span></p>
													    @endif
														
														@if(@$data['student']['mother_name'])
															<p>Mother's Name : <span style="font-size:16px;font-weight:900;">{{@$data['student']['mother_name']}}</span></p>
													    @endif
														
														@if(@$data['student']['dob'])
                                                            @php   $dob=strtotime(@$data['student']['dob']);@endphp
															<p>DOB :  <span style="font-size:16px;font-weight:900;">{{date('d-m-Y',$dob) }}</span></p>
                                                        @endif
														@if(@$data['student']['course'] )
														<p>Course : <span style="font-size:16px;font-weight:800;">{{@$course[@$data['student']['course']]}}</span></p> 
													    @endif	
														@if(@$data['student']['stream'])
														<p>Session : <span style="font-size:16px;font-weight:800;">{{@$stream[@$data['student']['stream']]}} {{@$admission_sessions[@$data['student']['exam_year']]}}</span></p> 
													    @endif
													
														@if(@$data['student']['mobile']) 
														<p>Mobile : <span style="font-size:16px;font-weight:800;">{{@$data['student']['mobile']}}</span></p>
													 
													    @endif
														@if(@$data['student']['sso'])
														<p>SSO : <span style="font-size:16px;font-weight:800;">{{@$data['student']['sso']}}</span></p>
														@endif
													  </div>
													  <div class="card-reveal">
														<span class="card-title grey-text text-darken-4">Roger Waters <i class="material-icons right">close</i>
														</span>
														<p>Here is some more information about this card.</p>
														<p><i class="material-icons">perm_identity</i> Project Manager</p>
														<p><i class="material-icons">perm_phone_msg</i> +1 (612) 222 8989</p>
														<p><i class="material-icons">email</i> yourmail@domain.com</p>
														<p><i class="material-icons">cake</i> 18th June 1990</p>
														<p></p>
														<p><i class="material-icons">airplanemode_active</i> BAR - AUS</p>
														<p></p>
													  </div>
													</div>
												  </div>
											</div>
											<div class="col s12 m8 l8">
												<ul class="collapsible">
												    
													@if(@$data['reval'] && count($data['reval']) > 0)
														<li>
															<div class="collapsible-header"><i class="material-icons">perm_identity</i><h6 style="font-size:18px;">पुनर्मूल्यांकन आवेदन पत्र(Reval  Application Form)</h6></div>
															<div class="collapsible-body">
															<ul>
															@foreach(@$data['reval'] as $rev)
																		<div class="card">
																			<div class="card-content"> 
																				<span> 
																				<span>
																		Student	{{@$stream[$rev['exam_month']]}} {{@$admission_sessions[@$rev['exam_year']]}} Reval application form.
																			</span>
																				<a class="" style ="font-size:18px;" href="{{ route('reval_generate_student_pdf',Crypt::encrypt(@$rev['id'])) }}">
																					<i class="material-icons dp48">file_download</i>
																					</a>
																					
																				</span>
																					
																			</div>
																		</div>


																</li>
															@endforeach	
															
															</ul>
															</div>
														</li>
													@endif
											
													@if(@$data['examResult'] && count($data['examResult']) > 0)
														<li class="">
															<div class="collapsible-header"><i class="material-icons">filter_drama</i><h6 style="font-size:18px;">परिणाम(Result)</h6></div>
															
															<div class="collapsible-body">
															
															<ul> 
															
															@foreach(@$data['examResult'] as $results)
																 @php $formId = 'frm'. @$results['enrollment'] .  
																	 @$results['exam_year'] . 
																	 @$results['exam_month'] ; @endphp
																@php
																	$supp=null;
																	if(@$results['supplementary'] &&$results['supplementary']=='1'){
																	$supp="Supplementary";	
																	}
																		
																@endphp
																<li>
																
																	{{ Form::open(['route' => ['oldresultdownloadpdf',[Crypt::encrypt(122),Crypt::encrypt(111)]],'id' => $formId,'method' =>'POST','autocomplete'=>'off']) }} 
																		<input type="hidden" name='enrollment' value="{{ encrypt(@$results['enrollment']) }}" id='enrollment'>
																		<input type="hidden" name='dob' value="{{ encrypt(@$data['student']['dob']) }}" id='dob'>
																		<input type="hidden" name='student_login' value="true" id='student_login'> 
																		<input type="hidden" name='exam_year' value="{{ encrypt(@$results['exam_year']) }}" id='exam_year'>
																		<input type="hidden" name='exam_month' value="{{ encrypt(@$results['exam_month']) }}" id='exam_month'>
																	{{ Form::close() }}
 
															<div class="card">
																<div class="card-content"> 
																	<span>Result {{$supp}} {{@$stream[@$results['exam_month']]}} {{@$admission_sessions[@$results['exam_year']]}} Exam Result.</span>
																	
																<a class="resultCls" data-form="{{ @$results['enrollment'] }}{{ @$results['exam_year'] }}{{ @$results['exam_month'] }}"  style ="font-size:18px;" href="javascript:void(none);"> 
																<i class="material-icons dp48">file_download</i>
															</a>
																</div>
															</div>

															</li>
															@endforeach
                                                           </ul>															
															</div>
														</li>
													@endif
														
													@if(@$data['supplementary'] && count($data['supplementary']) > 0)
													<li> 
														<div class="collapsible-header"><i class="material-icons">perm_identity</i>
															<h6 style="font-size:18px;">अनुपूरक आवेदन पत्र(Supplementary  Application Form)</h6>
														</div>

														
														<div class="collapsible-body"> 
															
														

															<ul>
																@foreach(@$data['supplementary'] as $supp)
																	@if(!empty(@$supp['locksumbitted']) && !empty(@$supp['locksumbitted']) )
																	@php $formId = 'supFrm'. @$supp['student_id'] .  
																				@$supp['id']; @endphp	 
																	{{ Form::open(['route' => ['supp_generate_student_pdf',Crypt::encrypt(@$supp['student_id'])], 'id' => $formId,'method' =>'post','autocomplete'=>'off']) }} 
																	<input type="hidden" name='student_login' value="true" id='student_login'>
																	<input type="hidden" name='supp_id' value="{{ encrypt(@$supp['id']) }}" id='supp_id'>
																	{{ Form::close() }}
																	<div class="card">
																		<div class="card-content"> 
																			<span> 
																			<span>Supplementary {{ @$supp['displayExamMonthYear'] }} Exam.</span>
																			<a class="suppFormCls" data-form="{{ @$supp['student_id'] }}{{ @$supp['id'] }}"  style ="font-size:18px;" href="javascript:void(none);">
																				<i class="material-icons dp48">file_download</i>
																				</a> 
																			</span> 
																		</div>
																	</div>
																	@endif
																@endforeach	 
															</ul> 
														</div>  
													</li>
													@endif
													@if(@$data['student'] && @$data['student']->id && !empty(@$data['application']['locksumbitted'] && !empty(@$student)))		
														@if(@$data['student_document'] && count($data['student_document']->toArray()) > 0)
															@php $data['student_document'] = @$data['student_document']->toArray(); @endphp
															<li>		
																<div class="collapsible-header"><i class="material-icons">perm_identity</i><h6 style="font-size:18px;"> आवेदन पत्र दस्तावेज़(Student Application Form Documents) </h6>
															</div>
																<div class="collapsible-body"> 
																	
																<ul>
																@foreach(@$student as  $fld => $lbl)
																<div class="card">
																
																	<div class="card-content"> 
																		<span> 
                                                                        @if(@$lbl['label'] == "Photo" ||@$lbl['label'] == "Signature") 
                                                                         {{@$lbl['label']}}
                                                                        @else
                                                                         {{@$lbl['label']}} Document.
                                                                        @endif
                                                                         </span>
																		<a style ="font-size:18px;" href="{{@$lbl['value']}}">
																			<i class="material-icons dp48">file_download</i>
																			</a>
																			
																		</span>
																		 
																	</div>
																</div>
																@endforeach
																</ul>
																</div> 
																	
															</li>
														@endif
													@endif
												
													
                                                   @if(@$allowips == true) 
													@if(@$isAllowToShowAdmitCardDownloadForStudent)
														<li class="active">
															<div class="collapsible-header"><i class="material-icons">perm_identity</i><h6 style="font-size:18px;">परीक्षा के लिए छात्र का हॉल टिकट(
																Student's Hall Ticket For Exam)</h6></div>
															<div class="collapsible-body">
															
																@if((@$data['student_allotment']) && !empty(@$data['student_allotment']))
																	<span style='color:red';>Note:प्रवेश पत्र(Admit Card) को संदर्भ केन्द्र से प्रमाणित करने की आवश्यकता नहीं है।</span>
																	<div class="card">
																	
																   
																		<div class="card-content"> 
																			<span> 
																			<span>
																			
																	Student Hall Ticket For Exam. 
																	{{ @$stream[@$data['student_allotment']['stream']].' '.@$admission_sessions[@$data['student_allotment']['exam_year']] }} 
																		</span>
																		
																		
																		
																		<a class="" style ="font-size:18px;" href="{{ route('downloadAdmitCard',encrypt($data['student_allotment']['enrollment'])) }}">
																				<i class="material-icons dp48">file_download</i>
																				</a>
																			</span>
																		</div>
																	</div>
															@endif
																
															</div>
														</li>
													@endif
                                                    @endif
													@if(@$data['student'] && @$data['student']->id && !empty(@$data['application']['locksumbitted']))
														<li class="active">
															<div class="collapsible-header"><i class="material-icons">perm_identity</i><h6 style="font-size:18px;">छात्र आवेदन पत्र(
																Student Application Form)</h6></div>
															<div class="collapsible-body">
															
																@if(!empty(@$data['application']['locksumbitted']) && !empty(@$data['application']['locksumbitted']))
                                                                
																	<div class="card">
																		<div class="card-content"> 
																			<span> 
																			<span>
																	Student Application Form.
																		</span>
																		<a class="" style ="font-size:18px;" href="{{ route('generate_student_pdf',Crypt::encrypt(@$data['student']['id'])) }}">
																				<i class="material-icons dp48">file_download</i>
																				</a>
																			</span>
																		</div>
																	</div>
															@endif
																
															</div>
														</li>
													@endif
												</ul>
											</div>
											{{-- @dd($data); --}}
										</div>
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
	<script src="{!! asset('public/app-assets/js/bladejs/student_history_details.js') !!}"></script>
	<script>
	 
	$('.resultCls').on("click",function(event){
		var form = $(this).attr('data-form');
		formId = "#frm" + form;
		
		$(formId).submit();
	});
	
	$('.suppFormCls').on("click",function(event){
		var form = $(this).attr('data-form');
		formId = "#supFrm" + form;
		$(formId).submit();
	}); 
	</script>
@endsection
<style>
	.card-profile-image{
		max-height: 65px !important;
    	max-width: 65px !important;
	}
</style>