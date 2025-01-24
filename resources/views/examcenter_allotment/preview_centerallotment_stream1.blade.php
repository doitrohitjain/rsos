@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row"> 
        <div id="breadcrumbs-wrapper" data-image="../../public/app-assets/images/gallery/breadcrumb-bg.jpg">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
						<strong>
						@if ($errors->any())
						 @foreach ($errors->all() as $error)
						 <div>{{$error}}</div>
						 @endforeach
						 @endif
						</strong>
					</span>
					
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ @$title }}</span></h5>
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
									<div class="row right"> 
								  <a href="{{route('examcenter_aicenter_mapping_stream1',Crypt::encrypt($examcenter_detail_id))}}" class="btn btn-xs btn-info pull-right">Allot More on Same Centre</a>
								  <a href="{{url('examcenter_details/listing')}}" class="btn btn-xs btn-info pull-right">Examcenter Listing</a>
								  </div><br><br>
									<div class="row"> 
										@include('elements.examcenter_allomtnet_listing_stream1')
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
		
		<div class="col s12 allottedenrollmentdiv" style="display:none">
			<div class="container">  
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row center"> 
                                        <div class="row" style="padding-left:2%;word-wrap: break-word;text-align: left;" id="allotmetndatadiv"></div>
										<div class="row" style="padding:2%;word-wrap: break-word;text-align: left;" id="allottedenrollmentdiv"></div>
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

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/preview_centerallotment_stream1.js') !!}"></script> 
@endsection 


