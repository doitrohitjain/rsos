@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.student_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
			
							<div id="responsive-table" class="card card card-default scrollspy">
								<div class="card-content">
								@if(@$studentdata->application->category_a != 7)
								@if($studentdata->gender_id == 2 && $studentdata->are_you_from_rajasthan == 1)
								<span style="color: red; font-size:20px;display:none;">Notes:- Online Service Fees of Rs. 30/- to be paid offline to the AI center </span></br></br>
								@else
								<span style="color: red; font-size:20px;display:none;">Notes:- Forwarding Fees of Rs. 50/- and Online Service Fees of Rs. 30/- to be paid offline to the AI center</span></br></br>
								@endif
								
								<span>Types of learning e-content/books?(शिक्षण ई-सामग्री/किताबें?) :
									<span style="color: blue; font-size:20px;">
									@if(@$studentdata->book_learning_type_id)
									{{ @$book_learning_type[$studentdata->book_learning_type_id] }}
									@endif
									</span>
								</span></br></br>
								
								@endif
									<h4 class="card-title">{{ $page_title; }} </h4>
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									<div class="row">
							<div class="col s1">
							</div>
							<div class="col s10">
							<table class="responsive-table">
							<thead>
							</thead>
								<tbody>
									<tr>
										<td>@php $fld="registration_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr>
									<tr>
										<td>@php $fld="forward_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr>
									<!-- <tr>
										<td>@php $fld="online_services_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr> -->
									@if($studentdata->adm_type != 5 )
									@if($studentdata->adm_type != 3)
									<tr>
										<td>@php $fld="add_sub_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr>
									<tr>
										<td>@php $fld="toc_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr>
									@endif
									<tr>
										<td>@php $fld="practical_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr>
									@endif
									@if($studentdata->adm_type != 4)
									<tr>
										<td>@php $fld="readm_exam_fees"; echo "Exam Fees"; @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr>
									@endif
									@if(@$studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true)
									@else
									<tr>
										<td>@php $fld="late_fees"; echo substr(ucwords(str_replace("_", " ", $fld)), 0, 8); @endphp</td>
										<td>{{  @$master[$fld]}}</td>
									</tr>	
									@endif
                             </tbody>
                              </table><br>
							  @if(@$studentdata->student_change_requests == 2 && $checkchangerequestsAllowOrNotAllow == true)
							  <span style ='margin-left:75%;font-size:20px !important;' class="chip lighten-10 green  black-text"> TOLAL : &nbsp;{{@$master['final_fees'] - @$master['late_fees']}}</span>
						      @else
								 <span style ='margin-left:75%;font-size:20px !important;' class="chip lighten-10 green  black-text"> TOLAL : &nbsp;{{@$master['final_fees']}}</span>  
						      @endif
							  </div>
							<div class="row">
										<div class="col m11 s12 mb-3"><br><br>
											<button class="btn cyan waves-effect waves-light right btn_disabled" type="submit" name="action"> Save & Continue
											</button>
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
	<script src="{!! asset('public/app-assets/js/bladejs/fees_details.js') !!}"></script> 
@endsection




