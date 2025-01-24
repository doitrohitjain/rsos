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
								<h5>{{ $page_title; }} 
								<a class="waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow" style ="" href="{{ route('generate_student_pdf',Crypt::encrypt(@$student_id)) }}" title="Download Application" >Download Application </a>
								<a class="btn btn-buy-now2 gradient-45deg-indigo-purple gradient-shadow white-text tooltipped2" style ="" target="_blank" title="Preview Application" href="{{ route('view_details',Crypt::encrypt(@$student_id)) }}">Preview Application </a>
								<a href="{{ @$backUrl}}" class="btn btn-xs btn-info right">Back</a><h5> 
							</div>
						</div>
					</div>  
					{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
							  'enctype' => 'multipart/form-data', "id" => $model ]) }}
					{!! Form::token() !!}
					{!! method_field('PUT') !!}
					<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
					<input type="hidden" name='isAllRejected' value=null id='isAllRejected'>
					
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
					<div class="col s12">
						<div class="col card  s12 m12 l12"> 
							<div class="right">
								<div class="card-content">
									<div class="col m6 s12 mb-1">
										<button class="green btn submitBtnCls btn_disabled submitconfirms" type="submit" name="action">
											Submit
										</button>
									</div>
									<div class="col m4 s12 mb-3">
									 <a href="{{ url()->current() }}" class="waves-effect waves dark btn btn-primary next-step">Reset</a> 
									</div> 
								</div>
							</div>
						</div>
					</div> 
					{{ Form::close() }}
				</div> 
			</div> 
		</div> 
	</div> 
</div> 
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/verify_document_details.js') !!}"></script> 
@endsection 