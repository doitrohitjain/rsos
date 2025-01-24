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
					<div class="col s12">
						<div class="card">
							<div class="card-content">
								@if(@$role_id == config("global.verifier_id"))
									@if(@$studentdata->verifier_status && $studentdata->verifier_status == 5)
										 <span style='color:green;font-size:16px;'><img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="RSOS"/>
											<span class="chip lighten-5 green green-text">Request sent</span>
											</span> 
									@elseif(@$studentdata->verifier_status && $studentdata->verifier_status == 6)
										<span style='color:blue;font-size:16px;'>
											<span class="chip lighten-5 blue blue-text">
												Clarification received from department
											</span>
										</span>
									@else
										<span style="color:red;font-size:16px;">यदि आपको किसी छात्र के मूल विवरण में कोई समस्या मिलती है, तो कृपया अपडेट के लिए विभाग को सूचित करने के लिए "अनुरोध" बटन पर क्लिक करें।(If you find any issues in a student's basic details, please click the "request" button to notify the department for updates.)  </span>
										<a class=" btn cyan waves-effect waves-light show_confirm request_btn" style ="" title="To notify the department for updates of basic details" href="{{ route('request_to_dept',Crypt::encrypt(@$student_id)) }}">Request</a>
									@endif
								@endif
								@include('elements.student_details_for_verify_document')
							</div>
						</div>
					</div>
					
					@if(@$studentdata->verifier_status && $studentdata->verifier_status == 5)
					@else
						<div id="mainform">
							{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
							  'enctype' => 'multipart/form-data', "id" => $model ]) }}
							{!! Form::token() !!}
							{!! method_field('PUT') !!}
							<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
							<input type="hidden" name='isAllRejected' value=null id='isAllRejected'>
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="col s12">
									<div class="card">
										<div class="card-content">
											<h5>विद्यार्थी के प्रत्येक विवरण का सत्यापन करें(Student's each details verification)</h5> 
											@include('elements.verify_other_then_docuemnt_input') 
											
											<div class="row">
												<div class="right">
													<div class="col m6 s12 mb-1">
														<button class="green btn submitBtnCls submitconfirms btn_disabled" type="submit" name="action">
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
								</div>
							</div>  
							
							{{ Form::close() }}
							
						</div> 
					@endif
				</div> 
			</div> 
		</div> 
	</div> 
</div> 
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/verify_document_details.js') !!}"></script> 


@endsection 