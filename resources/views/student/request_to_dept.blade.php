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
								@php  
								$backUrl =  route('verify_documents',Crypt::encrypt(@$student_id)); @endphp 
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
								<span style="color:red;font-size:16px;">
									कृपया पाए गए मूल विवरण में समस्या  का कारण बताएं। (Please provide the reason for the discrepancy in the basic details found.)
								</span>
								@include('elements.student_details_for_verify_document')
							</div>
						</div>
					</div> 
					<div id="mainform2"> 
						<div class="col s12">
							<div class="card">
								<div class="card-content"> 
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id],'enctype' => 'multipart/form-data', "id" => $model ]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name='isAllRejected' value=null id='isAllRejected'>
									<div id="Form-advance" class="">
										<div class="col s12">
											<div class="card2">
												<div class="card-content2">
													<span style="color:blue;">
													कृपया छात्र के मूल विवरण गलत पाए जाने पर समस्या स्पष्ट करें।(Please explain the reason for the basic details mismatch found.)
													</span> 
													<br>
													 @php $fld = "request_to_dept_remarks"; @endphp
													 @php $lbl='Explain Reason';$fld='request_to_dept_remarks'; @endphp
													<span class="extraCss">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
													 <div class="input-field2 col m12 s12 mb-3 cls_{{ $fld }}_div {{ @$showStatus }}"> 
														{!! Form::textarea($fld,null,array('id' => $fld,'class'=>'form-control cls_' . $fld . " ", 
														'rows' => 5, 'cols' => 2,'maxlength' => "3000",'style' => 'height:5rem;','placeholder'=>'Explain Reason')) !!}
													</div> 
												</div>
											</div>
										</div>
									</div>  
									<div class="row">
										<div class="col m8 s12 mb-1">
										</div>
										<div class="col m2 s12 mb-1">
											<button class="green btn submitBtnCls btn_disabled submitconfirms show_confirm" type="submit" name="action">
												Submit
											</button>
										</div>
										<div class="col m2 s12 mb-3">
										 <a href="{{ url()->current() }}" class="waves-effect waves dark btn btn-primary next-step">Reset</a> 
										</div> 
									</div>
									{{ Form::close() }} 
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
	<script src="{!! asset('public/app-assets/js/bladejs/request_to_dept.js') !!}"></script>  
@endsection 