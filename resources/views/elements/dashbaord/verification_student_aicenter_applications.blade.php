@php 
	// use App\Helper\CustomHelper;
	// $permissions = CustomHelper::roleandpermission();
	// dd($permissions);
@endphp

@php 
$isShow = true;
$fieldName = "verifier_status";
$levelName = "Verifier";

if(@$applicationVerifyCount){
    foreach(@$applicationVerifyCount as $type => $data){
		if(@$data['status'] == 'true'){	
		
@endphp
@php $mainId = "main2"; @endphp
@php $permissionAllowed = false;  $isClarificatonAllowed = false; @endphp  

@if(@$role_id == Config::get("global.super_admin_id"))
	@php 
		$fieldName = "department_status";
		$levelName = "Department";
	@endphp
	@can('department_fresh_document_verification')
		@php $permissionAllowed = true; @endphp
		@php $isClarificatonAllowed = true; @endphp
	@endcan
@elseif(@$role_id == Config::get("global.academicofficer_id"))
	@php 
		$fieldName = "academicofficer_status";
		$levelName = "Academic Officer";
	@endphp
	@can('academicofficer_fresh_document_verification')
		@php $permissionAllowed = true; @endphp
		@php $isClarificatonAllowed = true; @endphp
		@php $mainId = "main"; @endphp
	@endcan
@elseif(@$role_id == Config::get("global.verifier_id"))
	
	@php $mainId = "main"; @endphp
	@can('verifier_fresh_document_verification')
		
		@php $permissionAllowed = true; @endphp
	@endcan
@endif  

@if($permissionAllowed) 
	<div id="{{ @$mainId }}">
		<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="card">
					<div class="card-content">
						<h5>
							Verification Applications ({{@$admission_sessions[@$current_session]}} {{ @$exam_month[$type] }})
							@if(@$allowShow && @$counter)
								<span style="float: right;color: red;font-size:16px;">
									@if(@$isShow)
									<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
									<a href="{{ route('downloadstudentupdatedataExl') }}"  style="float: right;color: red;font-size:16px;" >{{ @$counter }} students form pending.</a>
									@php
                                      $isShow = null; 
									@endphp
									@endif
								</span>
							@endif
						</h5>
						   <div class="row">
										<div id="card-stats" class="pt-0">
											<div class="row"> 
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														@if(@$role_id == Config::get("global.super_admin_id"))
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,'verifier_status' => '3',$fieldName => 1]) }}" >
														@else
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldName => 1]) }}" >
														@endif
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> Pending At {{ @$levelName }} </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_pending'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												@if(@$isClarificatonAllowed)
													<div class="col s12 m6 l5 xl4">
														<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldName => 4]) }}" >
																<div class="padding-4">
																	<div class="row">
																		<div class="col s7 m7">
																			<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																			<p style="font-size: 10px;">Received Clarification At {{ @$levelName }} </p>
																		</div>
																		<div class="col s5 m5 right-align">
																			<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_clarification'])}} </h5>
																			<p class="no-margin">Click Here</p>
																		</div>
																	</div>
																</div>
															</a>
														</div>
													</div> 
												@endif
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
														<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldName => 3]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> Rejected At {{ @$levelName }} </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_rejected'])}}  </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>

												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldName => 2]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> Verified At {{ @$levelName }} </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_verfied'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												
												<div class="col s12 m6 l5 xl4"> 
													<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
														<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldName => 5]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 12px;"> Verifier Request To Department </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_request_verifier_to_dept'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div> 
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft" style="color:white;">
														<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldName => 6]) }}" >
															<div class="padding-4">
																<div class="row white-text">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 11px;">Department Sent Clarification To Verifier  </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_dept_clarification_to_verifier'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
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
	</div> 
@else	 
@endif

@php
}
}
				}
@endphp