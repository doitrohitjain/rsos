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
									{{ @$title }}
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
										<div class="col s12"> 
											<div class="col-md-3 left">
												<h4 class="card-title">
													<span style="color: green;font-size:20px;">
													Note: Please click on the Download Button to download the materials.(नोट: सामग्री डाउनलोड करने के लिए कृपया डाउनलोड बटन पर क्लिक करें।)
													</span>
												</h4> 
											</div> 
											<table class="table">
												<thead>
													<tr style="text-align: center;font-size:20px;">
														<th>Sr. No.</th>
														<th>Particular</th>
														<th>Zip Contain Files</th>
														<th>Action</th>
													</tr> 
												</thead>
												<tbody>
													@foreach(@$linksBtn as $k => $v)
														@if($v['status'] == true)
															<tr>
																<th>{{ @$k+1 }}</th>
																<th>{{ @$v['title_label'] }}</th>
																<th>
																	<ol> 
																		@if(@$v['items'])
																			@php $tempData = explode(",",@$v['items']); @endphp
																			@foreach(@$tempData as $k => $vItem)
																				{{ $vItem }}<br>
																			@endforeach
																		@endif
																	</ol>
																</th>
																<th>
																	<a title="{{ $v['title_label'] }}" href="{{ route('examcenter_material_zip_downlaod', $v['course']) }}" class="btn waves-effect waves-light border-round #4dd0e1 cyan lighten-2 gradient-45deg-deep-orange-orange">
																		{{ $v['label'] }}
																	</a>   
																</th> 
															</tr>
														@endif
													@endforeach
												</tbody>
											</table>
										</div>
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
	 
@endsection 


