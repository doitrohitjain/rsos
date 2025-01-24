<div id="main">
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="card">
					<div class="card-content">
						<h5>
							Applications ({{@$admission_sessions[@$current_session]}})
							@if(@$allowShow && @$counter)
								<span style="float: right;color: red;font-size:16px;">
									<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/> 
									<a href="{{ route('downloadstudentupdatedataExl') }}"  style="float: right;color: red;font-size:16px;" >{{ @$counter }} students form pending.</a>
								</span>
							@endif
						</h5>
						<div class="row">
							<div id="card-stats" class="pt-0">
								<div class="row">
									<div class="col s12 m6 l5 xl4">
										
										<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
											<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5">perm_identity</i>
														<p style="font-size: 16px;">Total Generated <br> Applications </p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$total_registered_student}}</h5>
														<p class="no-margin"><a href="{{ route('student_applications') }}" class="white-text" >Click Here</a></p>
													</div>
												</div>
										</div>
											
										</div>
									</div>
									<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e4ad12,#d3ce31)!important;">
											<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
													<p style="font-size: 14px;">Lock & Submitted Applications </p>
													</div>
													<div class="col s5 m5 right-align">
													<h5 class="mb-0 white-text">{{@$total_lock_Submit_student}}</h5>
													<p class="no-margin"><a href="{{ route('allstudent_locksumbited')}}" class="white-text" >Click Here</a></p>
													<!--<p>{{@$total_lock_Submit_student}}</p>-->
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
											<a href="{{ route('allstudent_not_pay_details') }}" >						
												<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
														<p><span style="font-size: 14px;">Fee Not Paid </span> <br> Applications</p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$get_Student_payment_not_pay_Count}}</h5>
														<p class="no-margin">Click Here</p>
													</div>
												</div>
											</div>
										</a>
										</div>
									</div>
									<div class="col s12 m6 l5 xl4">
										<div class="card white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
											<a href="{{ route('allstudent_zero_fees_pay_details') }}" >						
												<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
														<p><span style="font-size: 14px;">Zero(0) Fee Paid <span><br> Applications</p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$get_Student_zero_fees_payment_Count}}</h5>
														<p class="no-margin">Click Here</p>
													</div>
												</div>
											</div>
										</a>
										</div>
									</div>
									<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
											<a href="{{ route('allstudent_payment_details') }}" >
												<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
														<p>Fee Paid <br>Applications</p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$get_Student_payment_Count}}</h5>
														<p class="no-margin">Click Here</p>
													</div>
												</div>
											</div>
										</a>
										</div>
									</div>
									<!-- @php 
									 $deleteVal = false;
									$masterIP = '10.68.181.236';
									
									if( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
										$masterIP2 = '10.68.181.213';$masterIP3 = '10.68.181.229';$masterIP4 = '10.68.181.249';$masterIP5 = '10.68.181.51';
										if($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3 || $_SERVER['REMOTE_ADDR'] == $masterIP2){
											$deleteVal = true;
										}
									}else if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')){
										if($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP){
											$deleteVal = true;
										}
									}    
									@endphp -->
									@php 
									$deleteVal = false;
									$alllowips=config('global.whiteListMasterIps');
									if(in_array($_SERVER['REMOTE_ADDR'],$alllowips)){
										$deleteVal = true;
									}
									@endphp
									@if($deleteVal == true) 
									<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box" style="background: linear-gradient(45deg,#12e471,#219243)!important;">
												<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5">perm_identity</i>
														<p>Eligible<br>Students</p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$eligible_get_Student_payment_not_pay_Count}}</h5>
														<p class="no-margin"><a href="{{ route('student_applications',['is_eligible' => 1]) }}" class="white-text" >Click Here</a></p>
													</div>

												</div>
											</div>
										</div>
									</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>