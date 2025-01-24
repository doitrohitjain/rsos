@php 
	$role_id = @Session::get('role_id');
	// echo Config::get("global.examination_department");
	// dd($role_id);
@endphp
@if($role_id == Config::get("global.aicenter_id"))
	<div id="main">
@elseif($role_id == Config::get("global.examination_department"))
 @if(Route::current()->getName() == 'examination_department')
	<div id="main2">
 @else
	 <div id="main">
 @endif
@elseif($role_id == Config::get("global.developer_admin"))
<div id="main">
@else
	<div id="main">
@endif 
	<div class="col s12">
		<div class="container">
			<div class="seaction">   
				@php
					if(!empty($changerequest)){
						foreach($changerequest as $k => $detail){
							@endphp
							<div class="cardold">
								<div class="card-content">
									<ul class="collapsible">
										<li>
										 <div class="collapsible-header">
											<h5>
												Change Request 
												@if($k == 1 )
													({{@$admission_sessions[@$current_session]}} {{ @$exam_monthall[$k] }})
													
												@else
													({{@$admission_sessions[@$current_session]}} {{ @$exam_monthall[$k] }})
												@endif
											</h5>
									<span class="material-icons tooltipped icon" style="float: right;margin-left: auto;" data-position="bottom" data-tooltip="Click here for details">
											<i class="material-icons">add</i>
										</span>
                                    </div>
									<div class="collapsible-body">
										<span>
									<div class="row">
										<div id="card-stats" class="pt-0">
											<div class="row"> 
											<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														<a href="{{ route('change_request_student_total_Generated',['exam_month' => $k]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Change Request Total Generated</p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_total_generated'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														<a href="{{ route('change_request_student_applications',['exam_month' => $k,'student_change_requests' => 1]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Change Request Pending </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_total_registered_student'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('change_request_student_applications',['exam_month' => $k,'student_change_requests' => 2]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Change Request Approved</p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_total_approved_registered_student'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												
												
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('change_request_student_total_Generated',['exam_month' => $k,'student_change_requests' => 2]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 15px;">Change Request Department Approved</p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_department_approval'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														<a href="{{ route('change_request_student_student_not_update_applications',['exam_month' => $k]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Pending student updation From </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_student_approval_not_click_update_applications'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('change_request_student_total_Generated',['exam_month' => $k,'student_change_requests' => 2,'student_update_application' => 1]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Student Updating.. Form </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_student_update_applications'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('change_request_student_not_locksumbitted',['exam_month' => $k,'locksumbitted' => 1]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Change Request lock & submitted</p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_student_locksumbitted'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('change_request_student_not_locksumbitted',['exam_month' => $k]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Change Request not lock & submitted</p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_student_not_locksumbitted'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												
														<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
														<a href="{{ route('change_request_student_not_fees_pay',['exam_month' => $k]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Change Request Fees Not payed</p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_student_locksumbitted_fees_not_pay'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>
												
									
												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('change_request_student_completed',['exam_month' => $k]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 15px;">Change Request completed</p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$detail['change_request_student_completed'])}} </h5>
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
								</span>
							</div>
						</li>
					</ul>
				</div>
			</div>
					@php }
				}
			@endphp 
		</div>
		</div>
	</div>
</div>