@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="col s12 m12 l12">
						<div class="card"> 
							
						</div>
					</div>
					<div class="col s12 m12 l12"> 
						<div id="Form-advance" class="card card card-default scrollspy">
							<div class="card-content">
								<h4 class="card-title">{{ $page_title; }} </h4> 
								
								<div class="col m12 s12 mb-1">
									<span class="badge cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style="float:left !important">
										Note: The photograph and signature should of minimum 10 kb and maximum 50 kb size and file format( jpeg / png / gif). <span class="starmark" style="color:red;"> *</span>
									</span>
								</div>
								
								@include('elements.image_input')
								<div class="col m12 s12 mb-1">
									<span class="badge cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style="float:left !important">
										Note: The document of minimum 50 kb and maximum 500 kb size should be uploaded in pdf/jpeg/png/gif file type. <span class="starmark" style="color:red;"> *</span>
									</span>
								</div>
								@include('elements.document_input') 
							</div>
							<div class="row">
								<div class="col m11 s12 mb-1">
								<a href="{{ route('admission_subject_details',$estudent_id) }}" class="btn cyan waves-effect waves-light right show_confirm ">Save & Next</a>
								</div> 
							</div>
							<br>
						</div>
					</div>
				</div> 
			</div> 
		</div> 
	</div> 
</div> 
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/document_details.js') !!}"></script> 
@endsection 