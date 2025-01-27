@extends('layouts.default')
@section('content')
@include('elements.dashboard_ui_notifications')
<!-- BEGIN: Page Main-->
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
									<p>Total Pending </br>Users </p>
								</div>
								<div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">{{@$total_registered_student}}</h5>
									<p class="no-margin"><a href="{{ route('student_application_ai_center_wise_count') }}" class="white-text" >Click Here</a></p>
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
								<i class="material-icons background-round mt-5">perm_identity</i>
								<p>Total Lock & submitted Applications </p>
								</div>
								<div class="col s5 m5 right-align">
								<h5 class="mb-0 white-text">{{@$total_lock_Submit_student}}</h5>
								<p class="no-margin"><a href="{{ route('student_application_ai_center_wise_count')}}" class="white-text" >Click Here</a></p>
								<!--<p>{{@$total_lock_Submit_student}}</p>-->
								</div>
							</div>
						</div>
					    
					</div>
				</div>
				
				<!--<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
						<a href="{{ route('applications') }}" >
							<div class="padding-4">
							  <div class="row">
								 <div class="col s7 m7">
									<i class="material-icons background-round mt-5">timeline</i>
									<p>Supplementary Submitted</p>
								 </div>
								 <div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">80%</h5>
									<p class="no-margin">Click Here</p>
									<p>3,42,230</p>
								 </div>
							  </div>
						   </div>
					   </a>
					</div>
				</div>
			
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
						<a href="{{ route('applications') }}" >
							<div class="padding-4">
							  <div class="row">
								 <div class="col s7 m7">
									<i class="material-icons background-round mt-5">add_shopping_cart</i>
									<p>Total Revaluation {{ now()->year-2 }}-{{ now()->year-1 }}</p>
								 </div>
								 <div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">691</h5>
									<p class="no-margin">Click Here</p>
									<p>6,00,00</p>
								 </div>
							  </div>
						   </div>
					   </a>
					</div>
				</div>-->
				
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
						<a href="{{ route('applications') }}" >						
							<div class="padding-4">
							  <div class="row">
								 <div class="col s7 m7">
									<i class="material-icons background-round mt-5">perm_identity</i>
									<p>Not Payment pay </br>Users</p>
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
					<div class="card gradient-45deg-amber-amber gradient-shadow min-height-100 white-text animate fadeRight dashboard-link-box">
						<a href="{{ route('applications') }}" >
							<div class="padding-4">
							  <div class="row">
								 <div class="col s7 m7">
									<i class="material-icons background-round mt-5">timeline</i>
									<p>payment Payed </br>Users</p>
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
				
			</div>
		</div>
	</div>
</div>
@endsection 