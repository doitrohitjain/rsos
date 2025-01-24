@extends('layouts.default')
@section('content')
@include('elements.dashboard_ui_notifications')
<!-- BEGIN: Page Main-->
@can('Ai_center_student_dashboard')
<div id="main">
    <div class="row">
		<div id="card-stats" class="pt-0">
			<div class="row">
			
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft">
						<div class="padding-4">
							<div class="row">
								<div class="col s7 m7">
									<i class="material-icons background-round mt-5">perm_identity</i>
									<p>Total Enrollment Generated </p>
								</div>
								<div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">{{@$total_registered_student}}</h5>
									<p class="no-margin"><a href="{{ route('student_applications') }}" class="white-text" >Click Here</a></p>
									<p>{{@$total_registered_student}}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft">
					   <div class="padding-4">
						  <div class="row">
							 <div class="col s7 m7">
								<i class="material-icons background-round mt-5">perm_identity</i>
								<p>Total Applications Lock & Submitted </p>
							 </div>
							 <div class="col s5 m5 right-align">
								<h5 class="mb-0 white-text">{{@$total_lock_Submit_student}}</h5>
								<p class="no-margin"><a href="{{ route('student_locksumbited') }}" class="white-text" >Click Here</a></p>
								<p>{{@$total_lock_Submit_student}}</p>
							 </div>
						  </div>
					   </div>
					</div>
				</div>
				
				 <div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeRight">
					   <div class="padding-4">
						  <div class="row">
							 <div class="col s7 m7">
								<i class="material-icons background-round mt-5">timeline</i>
								<p>Not Payment pay </br>Users</p>
							 </div>
							 <div class="col s5 m5 right-align">
								<h5 class="mb-0 white-text">{{@$get_Student_payment_not_pay_Count}}</h5>
								<p class="no-margin"><a href="{{route('student_not_pay_details')}}" class="white-text" >Click Here</a></p>
								<p>{{@$get_Student_payment_not_pay_Count}}</p>
							 </div>
						  </div>
					   </div>
					</div>
				</div>
			
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft">
					   <div class="padding-4">
						  <div class="row">
							 <div class="col s7 m7">
								<i class="material-icons background-round mt-5">timeline</i>
								<p>payment Payed </br>Users</p>
							 </div>
							 <div class="col s5 m5 right-align">
								<h5 class="mb-0 white-text">{{@$get_Student_payment_Count}}</h5>
								<p class="no-margin"><a href="{{route('student_payment_details')}}" class="white-text" >Click Here</a></p>
								<p>{{@$get_Student_payment_Count}}</p>
							 </div>
						  </div>
					   </div>
					</div>
				</div>
				
				<!--<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft">
					   <div class="padding-4">
						  <div class="row">
							 <div class="col s7 m7">
								<i class="material-icons background-round mt-5">perm_identity</i>
								<p>Total Users<br><br></p>
							 </div>
							 <div class="col s5 m5 right-align">
								<h5 class="mb-0 white-text">1885</h5>
								<p class="no-margin">New</p>
								<p>1,12,900</p>
							 </div>
						  </div>
					   </div>
					</div>
				</div>
				
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeRight">
					   <div class="padding-4">
						  <div class="row">
							 <div class="col s7 m7">
								<i class="material-icons background-round mt-5">timeline</i>
								<p>Total DEO Users<br><br></p>
							 </div>
							 <div class="col s5 m5 right-align">
								<h5 class="mb-0 white-text">80%</h5>
								<p class="no-margin">Growth</p>
								<p>3,42,230</p>
							 </div>
						  </div>
					   </div>
					</div>
				</div>
				 -->
			</div>
		</div>
	</div>
</div>
@endcan
@can('Supplementary_ai_center_student_dashboard')
<div id="main">
    <div class="row">
		<div id="card-stats" class="pt-0">
			<div class="row">
			
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
						 
						<div class="padding-4">
							<div class="row">
								<div class="col s7 m7">
									<i class="material-icons background-round mt-5">perm_identity</i>
									<p>Supplementary Total Pending </br> Applications </p>
								</div>
								<div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">{{@$supplementary_total_registered_student}}</h5>
									<p class="no-margin"><a href="{{ route('supplementary_student_applications') }}" class="white-text" >Click Here</a></p>
									<!--<p>{{@$total_registered_student}}</p>-->
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
								<p>Supplementary Total Lock & submitted Applications </p>
								</div>
								<div class="col s5 m5 right-align">
								<h5 class="mb-0 white-text">{{@$supplementary_total_lock_Submit_student}}</h5>
								<p class="no-margin"><a href="{{ route('supplementary_student_locksumbited_applications')}}" class="white-text" >Click Here</a></p>
								<!--<p>{{@$total_lock_Submit_student}}</p>-->
								</div>
							</div>
						</div>
					</div>
				</div>
				
			<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
						<a href="{{ route('supplementary_aicenter_student_not_pay_payment_details') }}" >						
							<div class="padding-4">
							  <div class="row">
								 <div class="col s7 m7">
									<i class="material-icons background-round mt-5 mb-5">attach_money</i>
									<p>Supplementary Not Fee Paid </br>Applications</p>
								 </div>
								 <div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">{{@$supplementary_get_Student_payment_not_pay_Count}}</h5>
									<p class="no-margin">Click Here</p>
								 </div>
							  </div>
						   </div>
					   </a>
					</div>
				</div>
				
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-purple-deep-purple  gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
						<a href="{{ route('supplementary_aicenter_student_payment_details') }}" >
							<div class="padding-4">
							  <div class="row">
								<div class="col s7 m7">
									<i class="material-icons background-round mt-5 mb-5">attach_money</i>
									<p>Supplementary Fee Payment Paid Applications</p>
								</div>
								<div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">{{@$supplementary_get_Student_payment_Count}}</h5>
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
@endcan
@endsection 