@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="col s12">
						<div class="card">
							<div class="card-content">
								@php $backUrl =  url()->previous(); @endphp
								<h6><a href="{{ @$backUrl}}" class="btn btn-xs btn-info right">Back</a></h6>
								<h5>{{ $page_title; }}<h5>
							</div>
						</div>
					</div> 
					
					<div class="col s12">
						<div class="card">
							<div class="card-content">
								@include('elements.student_details_for_verify_document') 
							</div>
						</div>
					</div>  
					<div class="col s12 m12 l12"> 
						<div id="Form-advance" class="card card card-default scrollspy">
							<div class="card-content"> 
								<div class="col m12 s12 mb-1">
									<span class="" style="float:left !important;color:blue;font-size:18px;">
									Note: Please verify each document separately.If any document mark is rejected then please enter the reason of rejection.
									 
									(नोट: कृपया प्रत्येक दस्तावेज़ को अलग से सत्यापित करें। यदि कोई दस्तावेज़ चिह्न अस्वीकार कर दिया गया है तो कृपया अस्वीकृति का कारण दर्ज करें।)
									</span>
								</div>
								@include('elements.verify_document_input') 
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
	<script src="{!! asset('public/app-assets/js/bladejs/verify_document_details.js') !!}"></script> 
@endsection 