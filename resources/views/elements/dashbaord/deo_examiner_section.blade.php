@can('show_deo_dashboard')

<div id="main">
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="cardold">
					<div class="card-content">
					<ul class="collapsible">
						<li>
						<div class="collapsible-header">
							<h5>Practical Section</h5>
						<span class="material-icons tooltipped icon" style="float: right;margin-left: auto;" data-position="bottom" data-tooltip="Click here for details">
											<i class="material-icons">add</i>
										</span>
                                    </div>
						<div class="collapsible-body"><span>
						<div class="row">
							<div id="card-stats" class="pt-0">
								<div class="row">
									
								 <div class="col s12 m6 l5 xl4">
										<a href="{{ route('practicalexamineradd')}}" class="white-text" >
											<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e4ad12,#d3ce31)!important;">
												<div class="padding-4">
													<div class="row">
														<div class="col s7 m7">
															<i class="material-icons background-round mt-5 mb-5">perm_identity</i>
														<p style="font-size: 14px;">Add New Practical Examiner</p>
														</div>
														<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text"></h5>
														<p class="no-margin">Click Here</p>
														</div>
													</div>
												</div>
											</div>
										</a>
									</div> 
									
									<div class="col s12 m6 l5 xl4">
										<a href="{{ route('practicalexaminer') }}" class="white-text" >
											<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
												<div class="padding-4">
													<div class="row">
														<div class="col s7 m7">
															<i class="material-icons background-round mt-5">perm_identity</i>
															<p style="font-size: 16px;">Total Practical<br> Examiners </p>
														</div>
														<div class="col s5 m5 right-align">
															<h5 class="mb-0 white-text">
																{{@$total_registered_examiner}}
															</h5>
															<p class="no-margin">Click Here</a></p>
														</div>
													</div>
												</div> 
											</div>
										</a>
									</div>
									
									<div class="col s12 m6 l5 xl4">
										<a href="{{ route('examiner_mapping_list') }}" >
											<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
												<div class="padding-4">
													<div class="row">
														<div class="col s7 m7">
															<i class="material-icons background-round mt-5 mb-5">perm_identity</i>
															<p><span style="font-size: 12px;">Examiner Map For Subject  & </span> <br>Centerwise</p>
														</div>
														<div class="col s5 m5 right-align">
															<h5 class="mb-0 white-text">
																{{@$total_registered_examiner_maps}}
															</h5>
															<p class="no-margin">Click Here</p>
														</div>
													</div>
												</div>
											</div>
										</a>
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
			</div>
		</div>
	</div>
</div>
@endcan