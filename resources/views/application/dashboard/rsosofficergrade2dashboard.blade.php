@extends('layouts.default')
@section('content')
@include('elements.dashboard_ui_notifications')
<div id="main">
    <div class="row">
		<div id="card-stats" class="pt-0">
			<div class="row">
				<div class="col s12 m6 l5 xl4">
					<div class="card gradient-45deg-red-pink gradient-shadow min-height-100 white-text animate fadeLeft dashboard-link-box">
						<div class="padding-4">
							<div class="row">
								<div class="col s7 m7">
									<i class="material-icons background-round mt-5">perm_identity</i>
									<p>Ai Center List</p>
								</div>
								<div class="col s5 m5 right-align">
									<h5 class="mb-0 white-text">{{@$master}}</h5>
									<p class="no-margin"><a href="{{ route('aicenterusers.index') }}" class="white-text" >Click Here</a></p>
									<!--<p>{{@$total_registered_student}}</p>-->
								</div>
							</div>
					   </div>
					    
					</div>
				</div>
				
					
			</div>
		</div>
	</div>
</div>

@endsection 