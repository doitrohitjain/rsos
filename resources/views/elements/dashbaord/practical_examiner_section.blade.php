@can('show_practical_dashboard')
<div id="main">
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="cardold">
					<div class="card-content">
						<ul class="collapsible">
						<li>
						<div class="collapsible-header">
						<h5>Examiner Dashboard</h5>
						<span class="material-icons tooltipped icon" style="float: right;margin-left: auto;" data-position="bottom" data-tooltip="Click here for details">
											<i class="material-icons">add</i>
										</span>
                                    </div>
						<div class="collapsible-body"><span>
						<div class="row">
							<div id="card-stats" class="pt-0">
								<div class="row">
									<div class="col s12 m6 l5 xl4"> 
										<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
											<div class="padding-4">
												<div class="row">
													<div class="col s7 m7">
														<i class="material-icons background-round mt-5">perm_identity</i>
														<p style="font-size: 16px;">Total Practical <br>Exam Mappping </p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$total_examiner_mapping_count}}</h5>
														<p class="no-margin">
															<a href="{{ route('practicals') }}" class="white-text">
																Click Here
															</a>
														</p>
													</div>
												</div>
											</div> 
										</div>
									</div> 
									@php $deleteVal = false;
									$masterIP = '10.68.181.236';
									if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
										$masterIP2 = '10.68.181.213';$masterIP3 = '10.68.181.229';$masterIP4 = '10.68.181.249';$masterIP5 = '10.68.181.51';
										if($_SERVER['REMOTE_ADDR'] == $masterIP || $_SERVER['REMOTE_ADDR'] == $masterIP3 || $_SERVER['REMOTE_ADDR'] == $masterIP2){
											$deleteVal = true;
										}
									} else if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')){
										if(@$_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP){
											$deleteVal = true;
										}
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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endcan