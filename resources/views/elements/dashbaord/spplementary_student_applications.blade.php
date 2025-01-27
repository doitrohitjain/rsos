@php 
	$role_id = @Session::get('role_id');
	// echo Config::get("global.examination_department");
	//dd($supp);
@endphp
@if($role_id == Config::get("global.aicenter_id"))
	<div id="main">
@elseif($role_id == Config::get("global.examination_department"))
	@if(Route::current()->getName() == 'examination_department')
		<div id="main">
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
				@if(!empty(@$supp))
					@foreach($supp as $k => $detail)
						<div class="cardold">
							<div class="card-content">
								<ul class="collapsible">
									<li>
										<div class="collapsible-header">
											<h5>
												Supplementary 
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
																	<div class="padding-4">
																		<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5">perm_identity</i>
																				<p style="font-size: 17px;">Total Generated <br> Supplementary </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_total_registered_student'])}}</h5>
																				<p class="no-margin"><a href="{{ route('supplementary_student_applications',['exam_month' => $k]) }}" class="white-text" >Click Here</a></p>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col s12 m6 l5 xl4">
																<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
																	<div class="padding-4">
																		<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																			<p style="font-size: 16px;">Lock & Submitted Supplementary </p>
																			</div>
																			<div class="col s5 m5 right-align">
																			<h5 class="mb-0 white-text">{{ (@$detail['supplementary_total_lock_Submit_student'])}}</h5> 
																			<p class="no-margin"><a href="{{ route('supplementary_student_locksumbited_applications',['exam_month' => $k])}}" class="white-text" >Click Here</a></p>
																			<!--<p>{{@$total_lock_Submit_student}}</p>-->
																			</div>
																		</div>
																	</div>
																</div>
															</div>
									
															<div class="col s12 m6 l5 xl4">
																<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
																	<a href="{{ route('allsupplementary_student_not_pay_payment_details',['exam_month' => $k])}}">		
																	<div class="padding-4">
																		<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 16px;">Not Fee Paid Supplementary </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_get_Student_payment_not_pay_Count'])}}</h5>
																				<p class="no-margin">Click Here</p>
																			</div>
																		</div>
																	</div>
																	</a>
																</div>
															</div>
															<div class="col s12 m6 l5 xl4">
																<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
																	<a href="{{ route('allsupplementary_student_payment_details',['exam_month' => $k]) }}" >
																		<div class="padding-4">
																		<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 16px;">Fee Payment Paid Supplementary</p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_get_Student_payment_Count'])}} </h5>
																				<p class="no-margin">Click Here</p>
																			</div>
																		</div>
																	</div>
																	</a>
																</div>
															</div>
															@php 
																$deleteVal = false;	$alllowips=config('global.whiteListMasterIps');
																$CURRENT_IP=config('global.CURRENT_IP');
																if(in_array($CURRENT_IP,$alllowips)){
																	$deleteVal = true;
																}
																@endphp
																@if($deleteVal == true)
															@endif
														</div>
													</div>
												</div>
											</span>
										</div>
									</li>
								</ul>
								<div class ="card-content">
									<ul class="collapsible">
										<li>
											<div class="collapsible-header">
												<h5>Verification Supplementary
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
													@if($role_id != Config::get("global.aicenter_id"))
													<div class="row">
														<div id="card-stats" class="pt-0">
															<div class="row"> 
																<div class="col s12 m6 l5 xl4">
																	<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
																		<a href="{{ route('supplementary_student_applications',['exam_month' => $k,'is_aicenter_verify' => 2  ,'is_department_verify' => 1]) }}" >
																			<div class="padding-4">
																			<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 16px;">Verify Pending At Department </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_get_department_not_verfied'])}} </h5>
																				<p class="no-margin">Click Here</p>
																			</div>
																			</div>
																			</div>
																		</a>
																	</div>
																</div>
																<div class="col s12 m6 l5 xl4">
																	<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
																		<a href="{{ route('supplementary_student_applications',['exam_month' => $k,'is_aicenter_verify' => 2  ,'is_department_verify' => 4]) }}" >
																			<div class="padding-4">
																			<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 15px;">Received Clarification At Department </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_get_department_clarification'])}} </h5>
																				<p class="no-margin">Click Here</p>
																			</div>
																			</div>
																			</div>
																		</a>
																	</div>
																</div>
															{{--<div class="col s12 m6 l5 xl4">
																	<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
																		<a href="{{ route('supplementary_student_applications',['exam_month' => $k,'is_per_rejected' => 1 ]) }}" >
																			<div class="padding-4">
																			<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 16px;">Verify Per. Rejected At Department </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_get_department_per_rej'])}} </h5>
																				<p class="no-margin">Click Here</p>
																			</div>
																			</div>
																			</div>
																		</a>
																	</div>
																</div> --}}
																<div class="col s12 m6 l5 xl4">
																	<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
																		<a href="{{ route('supplementary_student_applications',['exam_month' => $k,'is_aicenter_verify' => 2  ,'is_department_verify' => 3]) }}" >
																		<div class="padding-4">
																			<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 16px;">Verify Rejected At Department </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_get_department_rejected'])}} </h5>
																				<p class="no-margin">Click Here</p>
																			</div>
																			</div>
																			</div>
																		</a>
																	</div>
																</div>
																<div class="col s12 m6 l5 xl4">
																	<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
																		<a href="{{ route('supplementary_student_applications',['exam_month' => $k,'is_aicenter_verify' => 2  ,'is_department_verify' => 2]) }}" >
																			<div class="padding-4">
																			<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 16px;">Verify Verified At Department </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ (@$detail['supplementary_get_department_verified'])}} </h5>
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
													@endif	
												</span>
											</div>
										</li>
									</ul>
								</div>	
							</div>
						</div>
					@endforeach
				@endif		
			</div>
		</div>
	</div>
</div>
