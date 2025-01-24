@php 
$isShow = true;
if(@$applicationVerifyCount){
    foreach(@$applicationVerifyCount as $type => $data){
		if(@$data['status'] == 'true'){	
@endphp
 
	<div class="main"><div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="card">
					<div class="card-content">
						<h5>
							Verification Applications ({{@$admission_sessions[@$current_session]}} {{ @$exam_monthall[$type] }})
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
														<a href="{{ route('verifying_student_applications',['exam_month' => $type,'aicenter_status' => 1]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Verify Pending At AiCenter </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_aicenter_not_verfied'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>

												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
														<a href="{{ route('verifying_student_applications',['exam_month' => $type,'aicenter_status' => 4]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 15px;">Received Clarification At AiCenter </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_aicenter_verfied'])}} </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>



												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
														<a href="{{ route('verifying_student_applications',['exam_month' => $type,'aicenter_status' => 3]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Verify Rejected At AiCenter </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_aicenter_rejected'])}}  </h5>
																		<p class="no-margin">Click Here</p>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>

												<div class="col s12 m6 l5 xl4">
													<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
														<a href="{{ route('supplementary_student_applications',['exam_month' => $type,'aicenter_status' => 2]) }}" >
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 16px;">Verify Verified At AiCenter </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ (@$data['fresh_get_aicenter_clarification'])}} </h5>
																		<p class="no-margin">Click Her</p>
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
@php
}
}
				}
@endphp