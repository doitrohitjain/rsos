@extends('layouts.default')
@section('content')
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
									<div class="col s3 right">
										<a href="{{'create'}}" class="btn btn-xs btn-info right waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content">Add Report Master</a>
									</div>
									<div class="col s3 right">
										<a target="_blank" href="{{'front_view'}}" class="btn btn-xs btn-info right btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange">Front View</a>
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
										@include('elements.filters.report_master_table_data')
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
	<script src="{!! asset('public/app-assets/js/bladejs/report_master_table_data.js') !!}"></script>
@endsection 


