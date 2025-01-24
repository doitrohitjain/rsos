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
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
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
									@include('elements.filters.search_filter')
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
													<th>Lock & Submitted Applications 10th</th>
													<th>Lock & Submitted Applications 12th</th>
													
												</tr>
											</thead>
									<tbody> 
									@foreach ($master as $ai_code => $item) 
										<tr>
											<td>{{ $item->ai_code }}</td>
											<td>{{ $item->college_name }}</td>
											
											
											<td>
												@php
												  $custom_helper_obj = new CustomHelper;
													$totalLocked = $custom_helper_obj->_getStudentLockSubmttedcourse10AiCodeWise($item->ai_code,$conditionstream);
													echo $totalLocked;
												@endphp	
											</td>
											
											<td>
												@php
												   $custom_helper_obj = new CustomHelper;
													$totalLocked = $custom_helper_obj->_getStudentLockSubmttedcourse12AiCodeWise($item->ai_code,$conditionstream);
													echo $totalLocked;
												@endphp	
											</td>
											
										</tr>
									@endforeach  
									</tfoot>
									</table>
									{{ $master->links('elements.paginater') }}
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


