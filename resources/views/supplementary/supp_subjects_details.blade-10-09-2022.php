@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.supplementary_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div class="col s12 m12 l12" style="background-color: #4f505245;'">
								<div id="Form-advance" class="card card card-default scrollspy">
									<div class="card-content">
										
										@php 
											$attentionMsg = null;
											if(@$master->adm_type == 4){
												$attentionMsg = "पूरक फार्म भरने के लिए सुधार प्रवेश की अनुमति नहीं है। (Improvement admission is not allowed to fill the supplementary form.)";
											}else if(@$master->adm_type == 3){
												$attentionMsg = "भाग प्रवेश के लिए विषय परिवर्तन की अनुमति नहीं है। (Subject change not allowed for Part Admission.)";
											}else if(@$master->adm_type == 5){
												$attentionMsg = "आईटीआई प्रवेश के लिए विषय परिवर्तन की अनुमति नहीं है। (Subject change not allowed for ITI Admission. )";
											} 
										@endphp
										@if(@$attentionMsg)
											<marquee><span style="font-size:20px;color:red;">
												<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
												 {{ $attentionMsg }} </span></marquee>
										@endif
										
										<h4 class="card-title">{{ $page_title; }}</h4>
										<div class="col x25 m12 s12"> 
										<div class="input-field col s12">
											<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
												<strong> 
													@if($errors->any()) 
														@foreach ($errors->all() as $error)
															<div>{{$error}}</div>
														@endforeach
													@endif
												</strong>
											</span>
										</div> 
														
										<table class="table"> 
											<tr>
												<td style="font-size:16px;font-weight: bold;">नामांकन संख्या (Enrollment Number) </td>
												<td style="color: green;font-size:20px;font-size:16px;font-weight: bold;">{{ @$master->enrollment  }}</td>
											</tr>
											<tr>
												<td width="25%" style="font-size:16px;font-weight: bold;"> पूरा नाम (Full Name) </td>
												<td width="25%">{{ @$master->name  }}</td> 
												<td width="25%" style="font-size:16px;font-weight: bold;">पिता का नाम (Father's Name) </td>
												<td width="25%">{{ @$master->father_name  }}</td>
											</tr>
											<tr>
												<td style="font-size:16px;font-weight: bold;">मोबाइल नंबर (Mobile No.) </td>
												<td>{{ @$master->mobile  }}</td>
											
												<td style="font-size:16px;font-weight: bold;">पिन कोड (Pincode) </td>
												<td>{{ @$master->Address->pincode  }}</td>
											</tr>
											<tr>
												<td style="font-size:16px;font-weight: bold;">प्रवेश प्रकार (Admission Type) </td>
												<td>
													@if(@$master->adm_type && @$adm_types[$master->adm_type])
														{{ @$adm_types[$master->adm_type]  }}
													@endif
												</td>
												<td></td>
												<td></td>
												
											</tr>
											
											<tr>
												<td width="25%" style="font-size:16px;font-weight: bold;">पता (Address) </td>
												<td colspan="3">{{ @$master->Address->address1.",".@$master->Address->address2.",
												".@$master->Address->address3.",".@$master->Address->block_name.",
												".@$master->Address->tehsil_name.",".@$master->Address->city_name.",
												".@$master->Address->district_name.",".@$master->Address->state_name  }}</td>
											</tr>
										</table>
										</div>
									</div>
									
									<div class="card-content"> 
										<div class="col s12 m12 l12">
											<div id="Form-advance" class="card card card-default scrollspy">
												<div class="card-content">@include('elements.ajax_validation_block')
												{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id],'enctype' => 'multipart/form-data', 'id' => $model]) }}
													<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
													{!! Form::hidden('student_id',$estudent_id,['id'=>'student_id']); !!}
														
													<div class="row">
														@php $i = 0;
														$subject_count = 0;
														$remaining_subject_count = 0; 
														if(!empty(@$supp_subject_arr)){
														$subject_count = count($supp_subject_arr);
														$remaining_subject_count = 7-$subject_count;
														@endphp
														<legend class="scheduler-border fieldsetLable-newll">
															<label style="font-size:18px;font-weight: bold;">Subject Details (विषय का विवरण)</label>
														</legend>
														
														<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
														
														<table>
														@php
															$notAllowEdit = false;
															if(@$master->adm_type == 5 && @$master->course == 12){
																$notAllowEdit = true;
																$remaining_subject_count = 0;
															}
															if(@$master->adm_type == 3){
																$notAllowEdit = true;
																$remaining_subject_count = 0;
															} 
														@endphp
														
														@for($k=0; $subject_count > $k; $k++)
														@php if(@$supp_subject_arr[$k]['final_result'] != 'p' && @$supp_subject_arr[$k]['final_result'] != 'P' && @$supp_subject_arr[$k]['final_result'] != 'PASS'){  @endphp 
															<tr>
																<td> 
																<?php
																/* if(!empty($supp_subject_label_arr[$k]['subject_id']) && $supp_subject_label_arr[$k]['subject_id']!= null ){
																	echo $subject_list[$supp_subject_label_arr[$k]['subject_id']];
																	echo "<br>";
																	echo $supp_subject_label_arr[$k]['subject_id'];
																} else {
																	echo "SELECT SUBJECT";
																} */
																
																
																 if(!empty($subject_list[@$supp_subject_arr[$k]['subject_id_origional']]) && $subject_list[@$supp_subject_arr[$k]['subject_id_origional']]!=null ){
																	echo $subject_list[@$supp_subject_arr[$k]['subject_id_origional']];
																} else {
																	echo "SELECT SUBJECT";
																} 
																
																
																if(@$master->adm_type == 5 && @$master->course == 12){
																	$subject_list = array();
																	$subject_list[19] = "English";
																}else if(@$master->adm_type == 3){
																	$subject_list2 = array(); 
																	$subject_list2[@$supp_subject_arr[$k]['subject_id']] = $subject_list[@$supp_subject_arr[$k]['subject_id']];
																	// $subject_list = $subject_list2;
																}
																?>
																</td>
																
																<td>
																	@php $lbl='Select Subject:';
																	$edit_class = 'subject_'.@$supp_subject_arr[$k]['subject_id'];
																	$placeholder = "Select Subject"; 
																	$fld='subject_id[]'; @endphp
																	<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
																	{!! Form::select($fld,$subject_list, @$supp_subject_arr[$k]['subject_id'], ['class' => $edit_class.' subjectitem form-control subject_list select2 select2a browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','disabled'=>'disabled']) !!}
																	@include('elements.field_error')
																</td>
																
																<td>
																	@if(@$notAllowEdit)
																		<span style="color:red;font-size:16px;cursor:pointer;">
																			Non Editable
																		</span>
																	@else
																		<span style="color:#fff;font-size:16px;cursor: pointer;" id="@php echo @$edit_class; @endphp" class="edit_supp_btn btn cyan waves-effect waves-light">
																			Edit
																		</span>
																	@endif 
																</td>
															</tr>
														@php } else { 
															$subject_list = $subject_list_two;
														@endphp
															<tr>
																<!--<td> {{  $subject_list[@$supp_subject_arr[$k]['subject_id']] }}</td>-->
																<td> 
																<?php 
																
																
																
																if(!empty($subject_list[@$supp_subject_arr[$k]['subject_id_origional']]) && $subject_list[@$supp_subject_arr[$k]['subject_id_origional']]!=null ){
																	echo '<span style="font-size:18px;font-weight: bold;">'.$subject_list[@$supp_subject_arr[$k]['subject_id_origional']].'</span>';
																}else {
																	echo '<span style="font-size:18px;font-weight: bold;">SELECT SUBJECT</span>';
																}
																?>
																</td>
																
																<td><span style="color:green;font-size:24px"><b>PASS</b></span>
																{!! Form::hidden('subject_id[]',null,['type'=>'text']); !!}
																</td>
																<td><span style="color:red;font-size:16px;cursor:pointer;">
																	Non Editable
																</span></td>
															</tr>
														@php } 
														if($k == 4){
															echo "<tr><td> <b>Addtional Subjects :</b> </td></tr>";
														}
														$i++;
														@endphp
														@endfor
														
														@for($k=0; $remaining_subject_count > $k; $k++)
															<tr>
																<td style="font-size:18px;font-weight: bold;"> SELECT SUBJECT</td>
																<td>
																	@php
																	$edit_class = 'subject_'.@$supp_subject_arr[$k]['subject_id'];
																	$lbl_placeholder='Select Subject:';
																	$lbl='<span style="font-size:18px;font-weight: bold;">Select Subject:</span>';
																	$placeholder = "Select ". $lbl_placeholder; 
																	$fld='subject_id[]'; 
																	@endphp
																	<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
																	{!! Form::select($fld,$subject_list,null, ['class' =>'form-control subject_list select2 select2a browser-default center-align','placeholder' =>$lbl_placeholder,'autocomplete'=>'off']) !!}
																	@include('elements.field_error')
																</td>
																<!--<td><span style="color:green;font-size:16px;cursor: pointer;" id="@php echo @$edit_class; @endphp" class="edit_supp_btn">Edit</span></td>-->
															</tr>
														@php   
														if($k == 5){
															echo "<tr><td> <b>Addtional Subjects :</b> </td></tr>";
														}
														$i++;
														@endphp
														@endfor
														</table>
														
														<h4 class="card-title">Marksheet Documents</h4>
														<div class="col x25 m12 s12"> 
															<table>
																<tr>
																	<td>@include('elements.supplementary_document_input') </td>
																</tr>
															</table>
														</div>
														<div class="row"></div><br>
														
														<div class="col m10 s12 mb-3">
															<button class="btn cyan waves-effect waves-light right supp_form_submit" type="button" name="action"> Save & Continue</button>
														</div>
														<div class="col m2 s10 mb-3">
															<button class="btn waves-effect waves-light form_reset" id="form_reset" type="button">Reset
															</button>
														</div>
														@php } else { @endphp
														<table><tr><td><span style="color:red;font-size:23px;font-weight:bold">Subject Not Found</span></td></tr></table>
														@php }  @endphp
													</div>
													{{ Form::close() }}
											</div>
										</div>
									</div>
									<div class="row"></div>
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
	<script src="{!! asset('public/app-assets/js/bladejs/supplementary/supp_subject_details.js') !!}"></script>
@endsection 
 


