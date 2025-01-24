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
								<div class="card-content" style="">
										<?php 
											$showStatus = false;
											$masterIP = '10.68.181.236';
											if( isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == $masterIP) {
												$masterIP2 = '10.68.181.213';$masterIP3 = '10.68.181.229';$masterIP4 = '10.68.181.249';$masterIP5 = '10.68.181.51';
												if($_SERVER['REMOTE_ADDR'] == $masterIP){
													$showStatus = true;
												}
											}else if(isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' || $_SERVER['HTTP_HOST'] == 'www.rsosadmission.rajasthan.gov.in')){
												if($_SERVER['HTTP_X_FORWARDED_FOR'] == $masterIP){
													$showStatus = true;
												}
											} 
											if($showStatus){ ?>
											<div class="row">
												<div class="col-md-12">
													<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" 
													height="30" alt="materialize logo"/> 
													<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
													target="_blank"
													style ="background: rgb(180,58,85);
													background: linear-gradient(142deg, rgba(180,58,85,1) 0%, rgba(253,29,29,1) 50%, rgba(133,69,252,0.5634628851540616) 100%);" 
													href="{{ route('bulk_verify_payment_issues',1) }}">
														Request Payment Issues
													</a>
													&nbsp;
													
													<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
													target="_blank"
													style ="background: rgb(66, 58, 180);
													background: linear-gradient(45deg,#aa2424,#ff6e40)!important;" 
													href="{{ route('bulk_verify_payment_issues',0) }}">
														All Payment Issues
													</a>
													<br><br>
													&nbsp;
													<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
													target="_blank"
													style ="background: rgb(66, 58, 180);
													background: linear-gradient(45deg,#24aa3f,#ff6e40)!important;" 
													href="{{ route('bulk_find_duplicate_payment_issues') }}">
														From E-mitra Duplicate Payments
													</a>  

													&nbsp;
													<a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" 
													target="_blank"
													style ="background: rgb(180,58,85);
													background: linear-gradient(142deg, rgba(180,58,85,1) 0%, rgba(253,29,29,1) 50%, rgba(133,69,252,0.5634628851540616) 100%);" 
													href="{{ route('sendSMSMessageForFeePaid') }}">
														Send SMS To Student Who yet Not fee paid but lock & Submitted
													</a>
													&nbsp;
												</div>
											</div>
										<?php } ?>
								</div>
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
	<script src="{!! asset('public/app-assets/js/bladejs/reporting_listing_payment_issues.js') !!}"></script> 
@endsection 


