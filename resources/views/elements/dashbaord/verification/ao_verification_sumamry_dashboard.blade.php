@php 
	// use App\Helper\CustomHelper;
	// $permissions = CustomHelper::roleandpermission();
	// dd($permissions);
@endphp

@php 
$isShow = true;
$fieldName = "verifier_status";
$fieldNameAO = "ao_status";
$levelName = "Verifier";
$levelNameAO = "AO";
$verifier_id = Config::get("global.verifier_id");
$verifier_admin_id = Config::get("global.verifier_admin_id");
//@dd($applicationVerifyCount);
if(@$applicationVerifyCount){
    foreach(@$applicationVerifyCount as $type => $data){
		if(@$data['status'] == 'true'){
@endphp
@php $mainId = "main"; @endphp
@php $permissionAllowed = true; @endphp
@if($permissionAllowed) 
	<div id="{{ @$mainId }}">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="cardold">
						<div class="card-content">
							<ul class="collapsible">
								<li>
								 <div class="collapsible-header">
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
									<span class="material-icons tooltipped icon" style="float: right;margin-left: auto;" data-position="bottom" data-tooltip="Click here for details">
											<i class="material-icons">add</i>
										</span>
                                    </div>
							<div class="collapsible-body"><span>
							<div class="row">
								<div id="card-stats" class="pt-0"> 
										@if(@$role_id != $verifier_admin_id)
										<div class="ao_details"> 
											<div class="row"> 
											
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														<a href="{{ route('verifying_student_applications',['exam_month' => $type]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> All Application At {{ @$levelNameAO }} </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_ao_all'])}}  </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div> 
												
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														@if(@$role_id == Config::get("global.super_admin_id"))
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,'verifier_status' => '3']) }}" >
														@else
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldNameAO => 1]) }}" >
														@endif
														<div class="padding-4">
															<div class="row">
																<div class="col s7 m7">
																	<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																	<p style="font-size: 16px;"> Pending At {{ @$levelNameAO }} </p>
																</div>
																<div class="col s5 m5 right-align">
																	<h5 class="mb-0 white-text">{{ (@$data['fresh_get_ao_pending'])}} </h5>
																	<p class="no-margin">Click Here</p>
																</div>
															</div>
														</div>
														</a>
													</div>
												</div> 
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														@if(@$role_id == Config::get("global.super_admin_id"))
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,'verifier_status' => '3']) }}" >
														@else
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldNameAO => 2]) }}" >
														@endif
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> Approved At {{ @$levelNameAO }} </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_ao_verfied'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div> 
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
														@if(@$role_id == Config::get("global.super_admin_id"))
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,'verifier_status' => '3']) }}" >
														@else
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldNameAO => 3]) }}" >
														@endif
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> Rejected At {{ @$levelNameAO }} </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_ao_rejected'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>  
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														@if(@$role_id == Config::get("global.super_admin_id"))
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,'verifier_status' => '3']) }}" >
														@else
															<a href="{{ route('verifying_student_applications',['exam_month' => $type,$fieldNameAO => 9]) }}" >
														@endif
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 11px;"> First Appeal At {{ @$levelNameAO }} </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_ao_clarification_first_appeal'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
											</div>
											
											
											<div class="row">
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background:linear-gradient(45deg, #52ffb4, #b9f56f) !important">
														<a href="javascript:void(none);">
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> Agree with Verifier actions </p>
																	</div>
																	<div class="col s5 m5 right-align ">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_ao_agree_with_verifier'])}}  </h5>
																		<p class="no-margin hide">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>  
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background:linear-gradient(45deg, #ff527a, #d94b2b) !important;">
														<a href="javascript:void(none);" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;"> Not Agree with Verifier actions </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_ao_not_agree_with_verifier'])}}  </h5>
																		<p class="no-margin hide">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>  
											</div>
										</div>
										<hr>
										@endif
									
									<div class="row"> 
									
										<div class="col s12 m6 l5 xl4">
											<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" >
												<a href="{{ route('verifying_student_applications',['exam_month' => $type,'extra' => $verifier_id]) }}" >
													<div class="padding-4">
														<div class="row">
															<div class="col s7 m7">
																<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																<p style="font-size:13.8px;"> All Application At {{ @$levelName }} </p>
															</div>
															<div class="col s5 m5 right-align">
																<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_all'])}}  </h5>
																<p class="no-margin">Click Here</p>
															</div>
														</div>
													</div>
												</a>
											</div>
										</div>
										
										<div class="col s12 m6 l5 xl4">
											<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
												@if(@$role_id == Config::get("global.super_admin_id"))
													<a href="{{ route('verifying_student_applications',['exam_month' => $type,'extra' => $verifier_id,'verifier_status' => '3',$fieldName => 1]) }}" >
												@else
													<a href="{{ route('verifying_student_applications',['exam_month' => $type,'extra' => $verifier_id,$fieldName => 1]) }}" >
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


										<div class="col s12 m6 l5 xl4">
											<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
												<a href="{{ route('verifying_student_applications',['exam_month' => $type,'extra' => $verifier_id,$fieldName => 7]) }}" >
													<div class="padding-4">
														<div class="row">
															<div class="col s7 m7">
																<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																<p style="font-size: 16px;"> Accepted At {{ @$levelName }} </p>
															</div>
															<div class="col s5 m5 right-align">
																<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_accepted'])}} </h5>
																<p class="no-margin">Click Here</p>
															</div>
														</div>
													</div>
												</a>
											</div>
										</div> 
                                         <!-- lokendar -->
										
										<div class="col s12 m6 l5 xl4">
											<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
												<a href="{{ route('verifying_student_applications',['exam_month' => $type,'extra' => $verifier_id,$fieldName => 8]) }}" >
													<div class="padding-4">
														<div class="row">
															<div class="col s7 m7">
																<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																<p style="font-size: 14px;"> Objections At {{ @$levelName }} </p>
															</div>
															<div class="col s5 m5 right-align">
																<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_objected'])}}  </h5>
																<p class="no-margin">Click Here</p>
															</div>
														</div>
													</div>
												</a>
											</div>
										</div>
										
										<div class="col s12 m6 l5 xl4">
											<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
											@if(@$role_id == Config::get("global.super_admin_id"))
												<a href="{{ route('verifying_student_applications',['exam_month' => $type,'extra' => $verifier_id,'verifier_status' => '3']) }}" >
											@else
												<a href="{{ route('verifying_student_applications',['exam_month' => $type,'extra' => $verifier_id,$fieldName => 9]) }}" >
											@endif
											<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
														<p style="font-size: 14px;"> First Appeal At {{ @$levelName }} </p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{ (@$data['fresh_get_verifier_clarification_first_appeal'])}} </h5>
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
							</span></div>
						</li>
						</ul>	
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