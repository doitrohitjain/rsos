<div id="main">
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="card">
					<div class="card-content">
						<h5>Supplementary March May</h5>
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
														<h5 class="mb-0 white-text">{{@$supplementary_total_registered_student_march_may}}</h5>
														<p class="no-margin"><a href="{{ route('supplementary_student_applications',[1]) }}" class="white-text" >Click Here</a></p>
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
													<h5 class="mb-0 white-text">{{@$supplementary_total_lock_Submit_student_march_may}}</h5>
													<p class="no-margin"><a href="{{ route('supplementary_student_locksumbited_applications',[1])}}" class="white-text" >Click Here</a></p>
													<!--<p>{{@$total_lock_Submit_student}}</p>-->
													</div>
												</div>
											</div>
										</div>
									</div>
									
								<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
											<a href="{{ route('allsupplementary_student_not_pay_payment_details',[1])}}" >						
												<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
														<p style="font-size: 16px;">Not Fee Paid Supplementary </p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$supplementary_get_Student_payment_not_pay_Count_march_may}}</h5>
														<p class="no-margin">Click Here</p>
													</div>
												</div>
											</div>
										</a>
										</div>
									</div>
									
									<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
											<a href="{{ route('allsupplementary_student_payment_details',[1]) }}" >
												<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
														<p style="font-size: 16px;">Fee Payment Paid Supplementary</p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$supplementary_get_Student_payment_Count_march_may}}</h5>
														<p class="no-margin">Click Here</p>
													</div>
												</div>
											</div>
										</a>
										</div>
									</div>

									<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
											<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5 mb-5">attach_money</i>
														<p style="font-size: 16px;">Eligible Students Supplementary</p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">
												      {{@$supplementary_get_Eligiable_Students_march_may}}
														</h5>
														<p class="no-margin"></p>
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
		</div>
	</div>
</div>