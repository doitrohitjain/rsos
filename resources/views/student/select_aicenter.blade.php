@extends('layouts.default')
@section('content')
@if(!empty(Auth::user()->id) || !empty(Auth::guard('student')->user()->id) )
	<div id="main">
		<div class="row">
@endif  
		<div class="">
			<div class="container"> 
				<div class="section"> 
				<div class="row">
				<div class="col s12">
					<div class="card">
					<div class="card-content">
					
					 <h6><a href="{{route('landing')}}" class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content right">Cancel</a></h6>
					  
					<h6><b>{{ @$title }}</b><h6> 
						<br>
						<span class="" style="color:red;font-size:18px;">
						<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
						Please select the AI Center as per the below information.(कृपया नीचे दी गई जानकारी के अनुसार एआई केंद्र का चयन करें।)
						<marquee></marquee>
					</span>
					</div>
				</div>
				</div> 
			
				<div class="col s12">
					<div class="card">
						<div class="card-content">
							@include('elements.filters.search_filter')
						</div>
					</div>
				</div>
				
				
	
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row"> 
									<table class="responsive-table">
										<thead>
											<tr>
												<th style="width:5%;">SR.NO</th>
												<th style="width:5%;">Ai Code</th> 
												<th style="width:35%;">AI Centres Name</th>
												<th style="width:5%;">District</th>
												<th style="width:5%;">Block</th>
												<th style="width:10%;">Nodal Officer Name</th>
												<th style="width:13%;">Nodal  Officer Mobile Number</th>
												<th style="width:13%;">Action</th> 
											</tr>
										</thead>
									<tbody>
										@php $i = 1;@endphp
										@foreach ($data as $key => $user)
											<tr>
												<td style="width:5%;">{{ @$i++ }}</td>
												<td style="width:10%;"> {{ @$user->ai_code }}</td>
												<td style="width:35%;"> {{ @$user->college_name }}</td>
												<td style="width:10%;">{{ @$district_list[$user->temp_district_id] }}</td>
												<td style="width:10%;">{{ @$master_block_list[$user->temp_block_id] }}</td>
												<td style="width:10%;" >{{ @$user->nodal_officer_name }}</td>
												<td style="width:10%;" >{{ @$user->nodal_officer_mobile_number }}</td> 
												<td style="width:10%;" >
												@php 
													$formId= 'self_re'.@$user->ai_code;
												@endphp
												@if(@$ssoid)
												<form method="post" action="{{ route('self_registration') }}" id="{{ $formId }}">
													
													<input type="hidden" name="ssoid" value="{{ @$ssoid}}"/>
													<input type="hidden" name="action_type" value="{{ @$action_type}}"/>
													<input type="hidden" class="cls_ai_code_{{ $formId }}" name="ai_code" value="{{@$user->ai_code }}"/>
													
													<a href="javascript:void('none');" data-ai_code = "{{@$user->ai_code }}" data-aidetails = "{{ @$user->ai_code }}-{{ @$user->college_name }}" class="btn btn-xs btn-success right mb-2 mr-1 selectaicenter" tooltipeed="Click here to select the Ai Center">Select</a>
												</form>

												@endif
									           </tb>
											</tr>
										@endforeach  
									</tbody> 
									</table>
									{{ $data->withQueryString()->links('elements.paginater') }}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> 
        </div>
		</div>
		@if(!empty(@$id = Auth::user()->id))
    </div>
</div> 
		@endif
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/select_aicenter.js') !!}"></script> 
@endsection  