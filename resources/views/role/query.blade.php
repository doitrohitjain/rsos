@extends('layouts.default')
@section('content')
@php 
use App\Helper\CustomHelper;

@endphp
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							
						</ol>
					</div>
				</div>
			</div>
        </div>
		
        <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									
								</div>
							</div>
						</div>
					</div>
				</div>
				@php 
					$total = 0;
					$totalLocked = 0;
					$totalNotLocked = 0;
					$totalFeePaid = 0;
				@endphp
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row"> 
										<table class="responsive-table">
											<thead>
												<tr>
													<th>Ai Code </th>
													<th>Center Name</th>
													<th>rajasthan feamle 10 </th>
													<th>rajasthan feamle 12 </th>
													<th>rajasthan feamle total </th>
													
												</tr>
											</thead>
									<tbody> 
									@foreach ($master as $ai_code => $college_name)
										<tr>
											<td>{{ $ai_code }}</td>
											<td>{{ $college_name }}</td>
											<td>
												@php 
													$custom_helper_obj = new CustomHelper;
													$total1 = $custom_helper_obj->_getStudentAiCodeWisedata($ai_code);
													echo $total1;
												@endphp	
											</td>
											
												<td>
												@php 
													$custom_helper_obj = new CustomHelper;
													$total2 = $custom_helper_obj->_getStudentAiCodeWisedata2($ai_code);
													echo $total2;
												@endphp	
											</td>
											<td>
											     @php 
													$rajfemaletotal = ($total1 - $total2);
													echo $rajfemaletotal;
												@endphp	
											</td>
											
									@endforeach  
									</tfoot>
									</table>
									{{ $master->links('elements.paginater')}}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> 
          <div class="content-overlay"></div>
        </div>
		</div>
    </div>
</div> 
@endsection

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/reporting_student_application.js') !!}"></script> 
@endsection 


