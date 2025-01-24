@extends('layouts.default')
@section('content')
@php
 	$role_id = Session::get('role_id');
	$academicofficer_id = Config::get('global.academicofficer_id');
	$verifier_id = Config::get('global.verifier_id');
	$super_admin_id = Config::get('global.super_admin_id');
	$enrollment = $masterDetails->enrollment;
	
	if($role_id == $super_admin_id){
		$isAllowButtons = true;
	} 
@endphp
<div id="main">
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="col s12">
						<div class="card">
							<div class="card-content">
								@php $backUrl =  url()->previous(); @endphp 
								<h5>{{ $page_title; }} 
								@if(@$isAllowButtons) 
									<a class="waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow" style ="" href="{{ route('generate_student_pdf',Crypt::encrypt(@$student_id)) }}" title="Download Application" >Download Application </a>
									
									<a class="btn btn-buy-now2 gradient-45deg-indigo-purple gradient-shadow white-text tooltipped2" style ="" target="_blank" title="Preview Application" href="{{ route('view_details',Crypt::encrypt(@$student_id)) }}">Preview Application </a>
									<a href="{{ @$backUrl}}" class="btn btn-xs btn-info right">Back</a><h5> 
								@endif
							</div>
						</div>
					</div> 
					
					@if(@$studentdata->verifier_status && $studentdata->verifier_status == 5)
					@else
						<div id="mainform">
							{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
							  'enctype' => 'multipart/form-data', "id" => $model ]) }}
							{!! Form::token() !!}
							{!! method_field('PUT') !!}
							<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
							<input type="hidden" name='isAllRejected' value="null" id='isAllRejected'>
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="col s12">
									<div class="card">
										<div class="card-content">
											<h5>विद्यार्थी के प्रत्येक सत्यापन का विवरण   (Student's verification each details ) : 
												{{ @$masterDetails->name }} 
											</h5>

											<span class="" style="color:red;font-size:14px;">
												यदि आप सत्यापन चरण को हटाना चाहते हैं, तो कृपया बॉक्स को चेक करें; अन्यथा, इसे अनचेक छोड़ दें।(If you want to remove the verification step, please check the box; otherwise, leave it unchecked.)
											</span>

											<table>
												<tr>
													<th>Actions</th>
													<th>Level</th>
													<th>DateTime</th>
													<th>Details</th>
													<th class="hide">Dril Down Details</th>
													@if($role_id == $super_admin_id && $enrollment == null)
														<th class="">Action</th>
													@endif
												</tr>
												@if(@$documentverifications && count($documentverifications) > 0)
													@foreach($documentverifications as $k => $v)
													@php 
														$baseNameFirst = "verifier_";
														if($v->role_id == $verifier_id){ 
															$baseNameFirst = "verifier_";
														}elseif($v->role_id == $super_admin_id){ 
															$baseNameFirst = "department_";
														}else if($v->role_id == $academicofficer_id){
															$baseNameFirst = "ao_";
														}  
													@endphp
													<tr>
														<th>{{ $k+1 }}</th>
														<th>{{ @$roles[$v->role_id] }}</th>
														<th>
															@php
																echo ($v->updated_at); 
															@endphp
														</th>
														<th>
															@php
																$fieldName = $baseNameFirst . 'upper_documents_verification';
																$v->$fieldName = json_decode($v->$fieldName, true); 
																$html = "";
																foreach($v->$fieldName as $ik => $iv){
																	$color = "red";
																	if($iv == 1){
																		$color = "green";
																	}
																	$html .= "<li>".  (@$labels[$ik]['hindi_name']);
																	if($v->role_id == $verifier_id){  
																		$html .= ' : <span class="chip lighten-5 ' . $color ." " . $color . '-text">';
																			$html .=   @$verifier_status_label[$iv];
																		$html .= '</span';
																	}else{ 
																		$html .= ' : <span class="chip lighten-5 ' . $color ." " .  $color . '-text">';
																			$html .=   @$ao_dept_status_label[$iv];
																		$html .= '</span'; 
																	}
																	$html .= "</li>";
																}  
																echo $html;
															@endphp
														</th>
														<th class="hide">
															@php  
																$baseNameFirst = "verifier_";
																if($v->role_id == $verifier_id){ 
																	$baseNameFirst = "verifier_";
																}elseif($v->role_id == $super_admin_id){ 
																	$baseNameFirst = "department_";
																}else if($v->role_id == $academicofficer_id){
																	$baseNameFirst = "ao_";
																}  
																$fieldName = $baseNameFirst . 'documents_verification'; 
																$html = $v->$fieldName; 
																echo $html;
															@endphp
														</th> 

														@if($role_id == $super_admin_id  && $enrollment == null)
															<th>
																<label>
																	<?php  
																		$check_attribute = ''; 
																		$value = null; 
																		$fld = "main";
																		$fld = ($fld . "[". encrypt($v->id) ."]"); 
																	?> 
																	<input {{ $check_attribute; }}  name="{{ $fld; }}" type="checkbox" >
																	<span>{{ @$subject_list[@$value] }}</span>
																</label>	
															</th> 
														@endif 
													</tr>
														 
													@endforeach
												@else	
													<tr>
														<td colspan="10"><center>No record found</center></td>
													</tr>
												@endif
											</table>
										</div>
									</div>
								</div>
							</div>
							@if($role_id == $super_admin_id  && $enrollment == null)
								<div class="">
									<div class="">
										<div class="row">
											<div class="col m9 s12 mb-3">
												<button class="btn cyan waves-effect waves-light right btn_disabled" type="submit" name="action"> Remove
												</button>
											</div>
											<div class="col m2 s10 mb-3">
												<button class="btn cyan waves-effect waves-light right" type="reset">Reset
												</button>
											</div>
										</div>
									</div>
								</div>
							@endif 
							{{ Form::close() }}							
						</div> 
					@endif
				</div> 
			</div> 
		</div> 
	</div> 
</div> 
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/verification_trail.js') !!}"></script> 
@endsection 

<style> 
	.odd{
		background:white !important;
	}
	.even{
		background:rgba(242,242,242,.7) !important;
	}  
	.header{
		background:rgba(242,242,242,.7) !important;
	}
	.lower_even{
	} 

</style>
