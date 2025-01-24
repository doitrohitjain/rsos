@extends('layouts.default')
@section('content')
@include('elements.dashboard_ui_notifications')
<!-- BEGIN: Page Main-->
@can('theroyexaminer_dashboard')
<div id="main">
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="cardold">
					<div class="card-content">
					<ul class="collapsible">
					<li>
						<div class="collapsible-header">
							<h5>Thoery Examiner Dashboard</h5>
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
														<p>Total Theory Exam Mapping </p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$master}}</h5>
														<p class="no-margin"><a href="{{ route('therory_mark_submissions') }}" class="white-text" >Click Here</a></p>
														<!--<p>{{@$total_registered_student}}</p>-->
													</div>
												</div>
										   </div>
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
			</div>
		</div>
	</div>
</div>
@endcan
@endsection 