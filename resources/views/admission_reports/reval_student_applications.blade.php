@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
									<div class="col-md-3 right hideold">
										&nbsp;
										<!-- <a href="https://rsosadmission.rajasthan.gov.in/rsos/exportr/eyJpdiI6Imc4TW4yT0t4amJ5VnhyRGtVSUxMTEE9PSIsInZhbHVlIjoiQUxOM01ycmxIQzhoVUsrMG9tOGVUQT09IiwibWFjIjoiYzQ2OGE4Yzk3MjgwOWVkN2VhY2ExYjRiODg2NThiYmI3N2Y3NDk5ZmNjNDVmZjFhZGI4NTZkNTY5YzExNzdmZCIsInRhZyI6IiJ9 " class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange">
										   Export RTI Student Address
										</a> --> 
										<?php
										//Local   //http://10.68.181.236/rsos/exportr/eyJpdiI6Iml0b2hTK0NsOVZkdHZic2djRk5ScEE9PSIsInZhbHVlIjoibmlwRk1QSjUzb3pDTWJFUnBxNmU0dz09IiwibWFjIjoiYzY4NWQ1YjE3NDEyYzZlNjJkZjQ1N2U2N2RmMzE4ZjQ2ZTc4ODI2YzRjYmE2OGNlMTE0ODc1ODRiYWVmMGE1MiIsInRhZyI6IiJ9
										// Live									
										//https://rsosadmission.rajasthan.gov.in/rsos/exportr/eyJpdiI6Imc4TW4yT0t4amJ5VnhyRGtVSUxMTEE9PSIsInZhbHVlIjoiQUxOM01ycmxIQzhoVUsrMG9tOGVUQT09IiwibWFjIjoiYzQ2OGE4Yzk3MjgwOWVkN2VhY2ExYjRiODg2NThiYmI3N2Y3NDk5ZmNjNDVmZjFhZGI4NTZkNTY5YzExNzdmZCIsInRhZyI6IiJ9 ?>
									</div>  
									@include('elements.filters.search_filter')
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row"> 
										@include('elements.filters.table_data')
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


