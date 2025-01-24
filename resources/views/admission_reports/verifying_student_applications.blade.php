@extends('layouts.default')
@section('content')
@php 
	use App\Helper\CustomHelper;
@endphp

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
								<div class="card-content">
									<div class="row"> 
									@if(@$role_id == config("global.verifier_id") || @$role_id == config("global.academicofficer_id"))
										<tr>
											<th colspan="8"></th>
											<th> 
												@php
													$modelContent = '<center>';
													// @dd($aiCenters);
													$coutner = 0;
													$modelContent .= "<table class='table'><tr>
															<th><b>SR.No.</b></th>
															<th><b>AiCentre</b></th>
														</tr>";
													foreach(@$aiCenters as $code => $name)
													{
														$coutner++;
														$modelContent .= "<tr><td>" . @$coutner ."</td><td> " .   @$name ."</td></tr>";
													} 
													$modelContent .= '</center>';
													$modelContent .= '</table>';
												@endphp 
												<span class="waves-effect waves-light  modal-trigger modalCls" style="color:blue;" data-content="{{ $modelContent }}">Allowed AiCentre  ( {{ count($aiCenters)}})</span> 
											</th>
										</tr>
									@endif 
									 
										 @if(@$role_id == config("global.super_admin_id"))
										<table class="responsive-table">
											<tr>
												<td>
													<a target="_blank" class="btn" href="{{ route('downloadRejectedStudentExl',['126','1']) }}" title="Click here to check complete students list">
														Rejected Students List Stream-1 with Document Rejection Reasons
													<span class="material-icons ">open_in_new</span> </a>
												</td>
												<td>
													<a target="_blank" class="btn" href="{{ route('downloadRejectedStudentExl',['126','2']) }}" title="Click here to check complete students list">
														Rejected Students List Stream-2 with Document Rejection Reasons
													<span class="material-icons ">open_in_new</span> </a>			
												</td>
											</tr>
										</table>
									@endif
										
									<table class="responsive-table">
									<thead> 
									<tr>
										<th>Sr.No.</th>
										<th>Enrollment</th> 
										<th>SSO</th>
										<th>AI Code</th>
										<th>Mobile Number</th>
										<th>Name</th>
										<th>Gender</th>
										<th>Course</th>
										<th>Stream</th>
										<th>Admission</th>
										@if(@$role_id == config("global.verifier_admin_id")) 
											<th>Verifier SSO</th>
										@endif
										<th>Is Verifier Verify</th>
										@if(@$role_id == config("global.super_admin_id") || @$role_id == config("global.academicofficer_id"))
											<th>Is AO Verify</th>
											<th>Is Dept. Verify</th>
										@endif
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
									@php $i = 1;@endphp
									@foreach ($master as $key => $user)
									<tr>
									<td>
									    {{ @$i }}
									</td>
									<td >{{ @$user->enrollment }}</td>
									@if(@$role_id == config("global.super_admin_id") || @$role_id == config("global.academicofficer_id"))
										<td>  
											<a target="_blank" href="{{ route('verification_trail',encrypt($user->id)) }}" title="Click here to check complete trailhead">
											{{ @$user->ssoid }}  </a>  
											<span class="material-icons ">open_in_new</span>
										</td>
									@else
										<td>{{ @$user->ssoid }}</td>
									@endif 
									<td >{{ @$user->ai_code }}</td>
									<td >{{ @$user->mobile }}</td>
									<td >{{ @$user->name }}</td>
									<td >{{ @$gender_id[@$user->gender_id] }}</td>
									<td >{{ @$course[@$user->course] }}</td>
									<td >{{ @$stream_id[@$user->stream] }}</td>
									<td >{{ @$adm_types[@$user->adm_type] }}</td>
									
									@if(@$role_id == config("global.verifier_admin_id"))
										<td>
										@php $verifierDetails = CustomHelper::getUserIdOfVerifierAiCode(@$user->ai_code); @endphp
											{{ @$verifiers[$verifierDetails] }}
										</td>
									@endif
									
									<td >{{ @$fresh_student_verfication_status[@$user->verifier_status] }}</td>
									
									@if(@$role_id == config("global.super_admin_id") || @$role_id == config("global.academicofficer_id"))
										<td>{{ @$fresh_student_verfication_status[@$user->ao_status] }}</td>
										<td>{{ @$fresh_student_verfication_status[@$user->department_status] }}</td>
									@endif 

									<td>
										<div class="invoice-action">
											@if(@$role_id == config("global.verifier_id"))
												@php 
													$isAllowVerify = CustomHelper::helpercheckIsStudentVerificationAllowAtVerifier(@$user->locksubmitted_date);
												@endphp
												@if(@$user->verifier_status == 7)
													<span class="chip lighten-5 green green-text">Accepted</span>
												@elseif(@$user->verifier_status == 8)
													<span class="chip lighten-5 red red-text">Objection</span>
												@elseif(@$user->verifier_status == 9)
													<a href="{{ route('rejected_verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Re-verifying the documents">
														ReVerify
													</a> 
												@elseif(@$user->verifier_status == 1)
													@if(@$isAllowVerify) 
														@php 
															$color = "";
														@endphp
														<a href="{{ route('verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Verify the documents">
															Verify
														</a>
													@endif
												@endif
											@endif
											
											
											@if(@$role_id == config("global.academicofficer_id"))
												@php 
													$isTocChangeAllow = false;
													
													$isAllowVerify = CustomHelper::helpercheckIsPaymentRecivedOrNot(@$user->id);
												@endphp
												
												
												
												@if(@$user->ao_status == 4 && @$isAllowVerify)
													@php $isTocChangeAllow = true; @endphp
													<a href="{{ route('ao_verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Verify the documents">
														Verify
													</a>
												@elseif(@$user->ao_status == 2)
													<span class="chip lighten-5 green green-text">Approved</span>
												@elseif(@$user->ao_status == 3)
													<span class="chip lighten-5 red red-text">Rejected</span>
												@elseif(@$user->ao_status == 5)
													<a href="{{ route('printupdatestudentdetalis',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Update basic details">
														Update
													</a>
												@elseif(@$user->ao_status == 6)
													<span class="chip lighten-5 orange orange-text">
														Updated
													</span>
												@elseif(@$user->ao_status == 9)
													@php $isTocChangeAllow = true; @endphp
													<a href="{{ route('ao_rejected_verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Re-verifying the documents">
														ReVerify
													</a> 
												@elseif(@$user->ao_status == 1 && @$isAllowVerify)
													@php $isTocChangeAllow = true; @endphp
													<a href="{{ route('ao_verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Verify the documents">
														Verify
													</a>
												@endif
												
												@if(@$isTocChangeAllow) 
													<a href="{{ route('dev_toc_subject_details',encrypt($user->id)) }}" class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content" title="Click here to Update TOC." >
														TOC
													</a> 
												@endif 
												
											@endif 
											@if(@$role_id == config("global.super_admin_id"))
												@php 
													$isAllowVerify = CustomHelper::helpercheckIsPaymentRecivedOrNot(@$user->id); 
												@endphp
												@if(@$user->department_status == 4 && @$isAllowVerify)
													<a href="{{ route('verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Verify the documents">
														Verify
													</a>
												@elseif(@$user->department_status == 2 || @$user->verifier_status == 2)
													<span class="chip lighten-5 green green-text">Approved</span>
												@elseif(@$user->department_status == 3)
													<span class="chip lighten-5 red red-text">Rejected</span>
												@elseif(@$user->department_status == 5)
													<a href="{{ route('printupdatestudentdetalis',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Update basic details">
														Update
													</a>
												@elseif(@$user->verifier_status == 6)
													<span class="chip lighten-5 orange orange-text">
														Updated
													</span>
												@elseif(@$user->verifier_status == 1)
													<span class="chip lighten-5 orange orange-text">
														Verifier_Pending
													</span>
												@elseif(@$user->department_status == 10)
													<a href="{{ route('dept_rejected_verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Re-verifying the documents">
														ReVerify
													</a> 
												
												@elseif(@$isAllowVerify)
													<a href="{{ route('verify_documents',encrypt($user->id)) }}" class="mb-6 btn waves-effect waves-light gradient-45deg-light-blue-cyan" title="Verify the documents">
														Verify
													</a>
												@endif
											@endif
										</div>
									</td>
									</tr>
									 @php $i++; @endphp	
									@endforeach 								
									</tfoot>
									</table>
									{{ $master->withQueryString()->links('elements.paginater') }}
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
	<script src="{!! asset('public/app-assets/js/bladejs/reporting_student_application.js') !!}"></script> 
@endsection 


