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
				<div class="row">
					<div class="col s12">
						<div class="container">
							<div class="seaction">
								<div class="card">
									<div class="card-content">
										<div class="blue-text"> 
											Note :  If the theory examiner already exists and you want to "Allot copies to the examiner" <a href="{{route('alloting_copies_examiners.add')}}" class=""> Please Click here </a>.<br>(यदि सैद्धांतिक परीक्षक पहले से मौजूद है और आप "परीक्षक को प्रतियां आवंटित करना चाहते हैं" तो कृपया यहां क्लिक करें।)
											<a href="{{route('mapping_examiners.add')}}" class="btn btn-xs btn-info right gradient-45deg-deep-purple-blue">Add Theory Examiner</a>
									</div>
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