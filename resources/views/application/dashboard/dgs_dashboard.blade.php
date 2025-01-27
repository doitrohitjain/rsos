@extends('layouts.default')
@section('content')
@include('elements.dashboard_ui_notifications')
<div id="main">
@php 
$isShow = true; 

if(@$applicationCount){
    foreach(@$applicationCount as $type => $data){
		if($data['status'] == 'true'){	
@endphp
@php $role_id = Session::get('role_id'); @endphp
	
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="cardold">
					<div class="card-content">
					<ul class="collapsible">
					<li>
						<div class="collapsible-header">
						<h5>
							Applications ({{@$admission_sessions[@$current_session]}} {{ @$exam_monthall[$type] }})
						</h5>
						<span class="material-icons tooltipped icon" style="float: right;margin-left: auto;" data-position="bottom" data-tooltip="Click here for details">
											<i class="material-icons">add</i>
										</span>
                                    </div>
						 <div class="collapsible-body"><span>
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
															<h5 class="mb-0 white-text">{{@$data["total_registered_student"]}}</h5>
															@if($type != "total")
																<p class="no-margin"><a href="{{ route('dgs_listing',['exam_month'=>$type]) }}" class="white-text" >Click Here</a></p>
															@else
																<p class="no-margin"><a href="{{ route('dgs_listing')}}" class="white-text" >Click Here</a></p>
															@endif
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
@php
		}
	}
}
@endphp


</div>
@endsection 

