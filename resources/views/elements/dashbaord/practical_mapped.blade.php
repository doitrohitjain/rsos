<div id="main">
@php 
$isShow = true; 
if(@$practicalmapped){
    foreach(@$practicalmapped as $type => $data){
		
@endphp
@php $role_id = Session::get('role_id'); @endphp
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="card">
					<div class="card-content">
						<h5>
							Practical mapped ({{@$admission_sessions[@$current_session]}} {{ @$exam_monthall[$type] }})
							
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
														<p style="font-size: 16px;">Total Practical mapped </p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{@$data["Practical_mapped_registered_count"]}}</h5>
														<p class="no-margin"><a href="javascript:void(0)" class="white-text" >Click Here</a></p>
													 
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
													    <p style="font-size: 14px;">Total Practical mapped yes</p>
													</div>
													<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{ @$data["Practical_mapped_count"]}}</h5>
														<p class="no-margin"><a href="javascript:void(0)" class="white-text" >Click Here</a></p>
														
													<!--<p>{{@$total_lock_Submit_student}}</p>-->
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col s12 m6 l5 xl4">
										<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">
											@if($type != "total")	
												<a href="{{ route('allstudent_not_pay_details',['exam_month'=>$type]) }}" >
											@else
												<a href="{{ route('allstudent_not_pay_details') }}" >
											@endif								
												<div class="padding-4">
													<div class="row">
														<div class="col s7 m7">
															<i class="material-icons background-round mt-5 mb-5">attach_money</i>
															<p><span style="font-size: 14px;">Not practical mapped NO</span></p>
														</div>
														<div class="col s5 m5 right-align">
														<h5 class="mb-0 white-text">{{ @$data["Practical_not_mapped_count"]}}</h5>
														<p class="no-margin"><a href="javascript:void(0)" class="white-text" >Click Here</a></p>
														
													<!--<p>{{@$total_lock_Submit_student}}</p>-->
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
@php
		
	}
}
@endphp

@include('elements.dashbaord.verification_student_aicenter_applications')
</div>