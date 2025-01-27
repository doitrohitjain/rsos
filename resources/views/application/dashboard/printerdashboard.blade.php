@extends('layouts.default')
@section('content')
	@include('elements.dashboard_ui_notifications')
    @php
        $isShow = true;
        if(@$applicationCount){
            foreach(@$applicationCount as $type => $data){
                if($data['status'] == 'true'){
    @endphp
    <div id="main">
        <div class="col s12">
            <div class="container">
                <div class="seaction">
                    <div class="cardold">
                        <div class="card-content">
							<ul class="collapsible">
								<li>
									<div class="collapsible-header">
										<h5>  Revised/Duplicate Marksheet/Migration </h5>
										<span class="material-icons tooltipped icon" style="float: right;margin-left: auto;" data-position="bottom" data-tooltip="Click here for details">
											<i class="material-icons">add</i>
										</span>
                                    </div>
									<div class="collapsible-body"><span>
										<!--total genrated-->
										<div class="row">
											<div id="card-stats" class="pt-0">
												<div class="row">
													<div class="col s12 m6 l5 xl4">
														<div class="card gradient-45deg-light-blue-cyan 		gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5">perm_identity</i>
																		<p style="font-size: 16px;">Total <br>Applications </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{@$applicationCount['total']["total_genrated_application"]}}</h5>
																		<p class="no-margin"><a
																					href="{{ route('revised_duplicate_report')}}"
																					class="white-text">Click Here</a>
																		</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!-- total lock & submitted -->
													<div class="col s12 m6 l5 xl4">
														<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box"
															 style="background: linear-gradient(45deg,#e4ad12,#d3ce31)!important;">
															<div class="padding-4">
																<div class="row">
																	<div class="col s7 m7">
																		<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																		<p style="font-size: 14px;">Lock & Submitted
																			Applications </p>
																	</div>
																	<div class="col s5 m5 right-align">
																		<h5 class="mb-0 white-text">{{ @$applicationCount['total']["total_lock_and_sunmitted_application"]}}</h5>

																		<p class="no-margin"><a
																					href="{{ route('revised_duplicate_report',['locksumbitted'=>1])}}"
																					class="white-text">Click Here</a></p>
																		<!--<p>{{@$total_lock_Submit_student}}</p>-->
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!-- total fee paid-->
													<div class="col s12 m6 l5 xl4">
														<div class="card white-text animate fadeLeft dashboard-link-box"
															 style="background: linear-gradient(45deg,#12e471,#219243)!important;">
															<a href="{{ route('revised_duplicate_report',['locksumbitted'=>1,'fee_status'=>1]) }}">
																<div class="padding-4">
																	<div class="row">
																		<div class="col s7 m7">
																			<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																			<p><span style="font-size: 14px;">Fee Paid </span> <br>
																				Applications</p>
																		</div>
																		<div class="col s5 m5 right-align">
																			<h5 class="mb-0 white-text">{{ @$applicationCount['total']["total_fee_paid_application"]}}</h5>
																			<p class="no-margin">Click Here</p>
																		</div>
																	</div>
																</div>
															</a>
														</div>
													</div>
												</div>
												<div class="raw">
													<div id="card-stats" class="pt-0">
														<div class="row">
															<div class="col s12 m6 l5 xl4">
																<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
																	<div class="padding-4">
																		<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5">perm_identity</i>
																				<p style="font-size: 16px;">Total Revised
																					Applications </p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{@$applicationCount['total']["total_genrated_revised_application"]}}</h5>
																				<p class="no-margin">
																					<a
																					href="{{ route('revised_duplicate_report',['marksheet_type'=>1]) }}"
																					class="white-text">Click Here</a>
																				</p>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col s12 m6 l5 xl4">
																<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box"
																style="background: linear-gradient(45deg,#e4ad12,#d3ce31)!important;">
																	<div class="padding-4">
																		<div class="row">
																			<div class="col s7 m7">
																				<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																				<p style="font-size: 14px;">Revised Lock & Submitted Applications 
																				</p>
																			</div>
																			<div class="col s5 m5 right-align">
																				<h5 class="mb-0 white-text">{{ @$applicationCount['total']["total_lock_and_sunmitted_revised_application"]}}</h5>
																				<p class="no-margin">
																					<a href="{{ route('revised_duplicate_report',['marksheet_type'=>1,'locksumbitted'=>1]) }}"class=	"white-text">Click Here</a>
																				</p>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col s12 m6 l5 xl4">
																<div class="card white-text animate fadeLeft dashboard-link-box" style="background:linear-gradient(45deg,#12e471,#219243)!important;">
																	<a href="{{ route('revised_duplicate_report',['marksheet_type'=>1,'locksumbitted'=>1,'fee_status'=>1]) }}">
																		<div class="padding-4">
																			<div class="row">
																				<div class="col s7 m7">
																					<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																					<p><span style="font-size: 14px;">Revised Fee Paid </span>
																						<br> Applications</p>
																				</div>
																				<div class="col s5 m5 right-align">
																					<h5 class="mb-0 white-text">{{ @$applicationCount['total']["total_fee_paid_revised_application"]}}</h5>
																					<p class="no-margin">Click Here</p>
																				</div>
																			</div>
																		</div>
																	</a>
																</div>
															</div>
														</div>
														<div class="raw">
															<div id="card-stats" class="pt-0">
																<div class="row">
																	<div class="col s12 m6 l5 xl4">
																		<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
																			<div class="padding-4">
																				<div class="row">
																					<div class="col s7 m7">
																						<i class="material-icons background-round mt-5">perm_identity</i>
																						<p style="font-size: 16px;">Total Duplicate Applications </p>
																					</div>
																					<div class="col s5 m5 right-align">
																						<h5 class="mb-0 white-text">{{@$applicationCount['total']["total_genrated_duplicate_application"]}}</h5>
																						<p class="no-margin">
																						<a	href="{{ route('revised_duplicate_report',['marksheet_type'=>2]) }}"class="white-text">Click	Here</a>
																						</p>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="col s12 m6 l5 xl4">
																		<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background:linear-gradient(45deg,#e4ad12,#d3ce31)!important;">
																			<div class="padding-4">
																				<div class="row">
																					<div class="col s7 m7">
																						<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																						<p style="font-size: 14px;">Duplicate Lock &	Submitted Applications </p>
																					</div>
																					<div class="col s5 m5 right-align">
																						<h5 class="mb-0 white-text">{{ @$applicationCount['total']["total_lock_and_sunmitted_duplicate_application"]}}</h5>
																						<p class="no-margin">
																						<a href="{{ route('revised_duplicate_report',['marksheet_type'=>2,'locksumbitted'=>1]) }}"		class="white-text">Click Here
																						</a>
																						</p>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="col s12 m6 l5 xl4">
																		<div class="card white-text animate fadeLeft dashboard-link-box" style="background:linear-gradient(45deg,#12e471,#219243)!important;">
																			<a href="{{ route('revised_duplicate_report',['marksheet_type'=>2,'locksumbitted'=>1,'fee_status'=>1]) }}">
																				<div class="padding-4">
																					<div class="row">
																						<div class="col s7 m7">
																						<i class="material-icons background-round mt-5 mb-5">attach_money</i>
																						<p><span style="font-size: 14px;">Duplicate Fee Paid </span><br> Applications</p>
																						</div>
																						<div class="col s5 m5 right-align">
																						<h5 class="mb-0 white-text">{{ @$applicationCount['total']["total_fee_paid_duplicate_application"]}}</h5>
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
								</li>
							</ul>
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
@endsection 