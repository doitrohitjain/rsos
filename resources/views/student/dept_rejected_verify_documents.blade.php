@extends('layouts.default')
@section('content')
@php
$role_id = Session::get('role_id');
	$academicofficer_id = Config::get('global.academicofficer_id');
	$verifier_id = Config::get('global.verifier_id');
	$super_admin_id = Config::get('global.super_admin_id');
	$enrollment = $masterDetails->enrollment;
	
	if($role_id == $super_admin_id){
		$isAllowButtons = true;
	} 
 $role_id = Session::get('role_id'); @endphp
 if($role_id == $super_admin_id || $role_id == $academicofficer_id){
	$basicDetailsUpdate = true;
}
@endphp
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
								@if(@$isAllowButtons) 
								<a class="waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow" style ="" href="{{ route('generate_student_pdf',Crypt::encrypt(@$student_id)) }}" title="Download Application" >Download Application </a>
								<a class="btn btn-buy-now2 gradient-45deg-indigo-purple gradient-shadow white-text tooltipped2" style ="" target="_blank" title="Preview Application" href="{{ route('view_details',Crypt::encrypt(@$student_id)) }}">Preview Application </a>
								@endif
								<a href="{{ @$backUrl}}" class="btn btn-xs btn-info right">Back</a>
								<h5> 
							</div>
						</div>
					</div> 
					@if(@$basicDetailsUpdate)
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<span style="color:red;font-size:16px;">यदि आपको किसी छात्र के मूल विवरण में कोई समस्या मिलती है, तो कृपया अपडेट  बटन पर क्लिक करें।(If you find any issues in a student's basic details, please click the "update" button for updates.)  </span>  
									<a class=" btn cyan waves-effect waves-light"   style ="" title="Update the basic details of student" href="{{ route('printupdatestudentdetalis',Crypt::encrypt(@$student_id)) }}">Update</a>
 
								</div>
							</div>
						</div>  
					@endif
					
					@if(@$studentdata->verifier_status && $studentdata->verifier_status == 5)
					@else
						<div id="mainform">
							{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 
							  'enctype' => 'multipart/form-data', "id" => $model ]) }}
							{!! Form::token() !!}
							{!! method_field('PUT') !!}
							<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
							<input type="hidden" name='isAllRejected' value="null" id='isAllRejected'>
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="col s12">
									<div class="card">
										<div class="card-content">
											<h5>विद्यार्थी के प्रत्येक विवरण का सत्यापन करें(Student's each details verification)</h5> 
											@include('elements.student_basic_details')  
											@if(@$role_id == config("global.verifier_id"))
												@include('elements.verification.rolebased.verifier_verify_docuemnt_inputs')
											@elseif(@$role_id == config("global.academicofficer_id"))
												@include('elements.verification.rolebased.ao_verify_docuemnt_inputs')
											@elseif(@$role_id == config("global.super_admin_id"))
												@include('elements.verification.rolebased.dept_verify_docuemnt_inputs')
											@endif
											<br>
											<div class="row">
												<div class="right">
													<div class="col m6 s12 mb-1">
														<button class="green btn submitBtnCls btn_disabled submitconfirms waves-effect waves-light right btn_disable " type="submit" name="action"> Submit Verification
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
	<script src="{!! asset('public/app-assets/js/bladejs/ao_verify_document_details.js') !!}"></script> 
@endsection 

<style> 
	.odd{
		background:white !important;
	}
	.even{
		background:rgba(242,242,242,.7) !important;
	}  
	.header{
		background:rgba(242,242,242,.7) !important;
	}
	.lower_even{
	}  
</style>
