@extends('layouts.default')
@php 
	use App\Http\Controllers\Controller;
	$Controller = new Controller;
@endphp
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
									
										@if(!empty(@$finalArr))  
									
											@php $isPresent = false; @endphp
											@foreach(@$finalArr as $masters)
										        @foreach(@$masters as $master)
													@if(!empty($master['global_variables']) || !empty($master['table_details']))
														@php $isPresent = true; @endphp
														<ul class="collapsible">
															<li>
																<div class="collapsible-header">
																	<b>{{@$master['module_name']}} ( {{@$master['sub_module_name']}} )</b>
																</div>
																<div class="collapsible-body"> 
																	@if(@$master['global_variables'])
																		<fieldset>
																			<legend><b><span style="font-weight:900;color:black">Global Variables</span></b></legend>
																			<table>
																				@php $count=1; @endphp
																				<tr>
																					<th>S.R</th>
																					<th>Label</th>
																					<th>Global Variable</th>
																					<th>Global Value</th>
																					<th>Action</th>
																				</tr>		
																				@foreach(@$master['global_variables'] as 	$global_variable)
																					<tr>
																						<td>{{$count}}</td>
																						<td>{{ str_replace('', '', ucwords(str_replace('_', ' ', $global_variable))) }}</td>
																						<td>{{$global_variable}}</td>
																						<td>
																							{{ Form::open(['method'=>'POST','id'=>'dataform','class'=>'dataform']) }}
																							{!! Form::token() !!}
																							@php
																								$fld=@$global_variable;
																								$indexItem = Crypt::encrypt($global_variable);
																								$response = $Controller->getFromGlobalVariable($indexItem);
																							@endphp
																							{!!Form::text("variable_value",(@$response['data']),['type'=>'text','class'=>'form-control ','id'=>$fld,'placeholder'=>'Enter ','autocomplete'=>'off']); !!}
																							<input type ="hidden" name="variable_name" value="{{$indexItem}}">
																						</td>
																						<td>
																							<button type="submit" class="btn gradient-45deg-indigo-yellow btn-submit">
																							<span>Update</span>
																							</button>
																						</td>
																						{{ Form::close() }}
																					
																					</tr>
																					@php  $count++; @endphp
																				@endforeach
																			</table>
																		</fieldset>
																	@endif
																	@if(@$master['table_details'])
																		<fieldset>
																			<legend><b><span style="font-weight:900;color:black">Other Then Global Variables</span></b></legend>
																			@if(@$master['table_name'] == 'masters')
																				<table>
																					@php $count=1; @endphp
																					<tr>
																						<th>S.R</th>
																						<th>Label</th>
																						<th>Master Variable</th>
																						<th>Master Value</th>
																						<th>Action</th>
																					</tr>
																					@foreach(@$master['table_details'] as $table_variable)
																						<tr>
																							<td>{{$count++}}</td>
																							<td>{{ str_replace('', '', ucwords(str_replace('_', ' ', $table_variable))) }}</td>
																							<td>{{$table_variable}}</td>
																							<td>
																								{{ Form::open(['method'=>'POST','id'=>'dataform','class'=>'dataform']) }}
																								{!! Form::token() !!}
																								@php
																								$fld=@$table_variable;
																								$indexItem = Crypt::encrypt($table_variable);
																								$table_name = Crypt::encrypt(@$master['table_name']); 

																								if($master['table_name'] == 'masters'){
																									$response = $Controller->getFromMasterDetils($indexItem);
																								}

																								@endphp
																								{!!Form::text("variable_value",(@$response['data']),['type'=>'text','class'=>'form-control ','id'=>$fld,'placeholder'=>'Enter ','autocomplete'=>'off']); !!}
																								 <input type ="hidden" name="variable_name" value="{{$indexItem}}">
																								 <input type ="hidden" name="table_name" value="{{$table_name}}">
																							</td>
																							<td>
																								<button type="submit" class="btn gradient-45deg-indigo-yellow btn-submit">
																									<span>Update</span>
																								</button>
																							</td>
																							{{ Form::close() }}
																						</tr>
																					@endforeach
																				</table>
																			@else
																				@php 
																					$datas = $Controller->getExamLateFeeDetails(@$master['table_details'][0]); 
																					
																				@endphp	
																				<table>
																					@php $count=1; @endphp
																					<tr>
																						<th>S.R</th>
																						<th>Stream</th>
																						<th>IS Supplementary</th>
																						<th>Gender</th>
																						<th>Late Fees</th>	
																						<th>From Date</th>	
																						<th>Action</th>
																						<th>To Date</th>	
																						<th>Action</th>
																					</tr>
																					@foreach(@$datas as $key=>$data)
																						<tr>
																							<td>{{$count++}}</td>	
																							<td>{{@$stream_id[@$data->stream]}}</td>
																							<td>{{$yesno[@$data->is_supplementary]}}</td>
																							<td>{{@$gender_id[@$data->gender_id]}}</td>
																							<td>{{@$data->late_fee}}</td>
																							<td>
																								{{ Form::open(['method'=>'POST','id'=>'dataform','class'=>'dataform']) }}
																								{!! Form::token() !!}
																								@php
																								$fld=@$data->from_date;
																								$indexItem = Crypt::encrypt(@$data->id);
																								$table_name = Crypt::encrypt(@$master['table_name']); 
																								$record_id =	Crypt::encrypt(@$data->id);
																								$field_name =Crypt::encrypt('from_date');
																								@endphp
																								{!!Form::text("variable_value",(@$fld),['type'=>'text','class'=>'form-control ','id'=>$fld,'placeholder'=>'Enter ','autocomplete'=>'off']); !!}
																								<input type ="hidden" name="variable_name" value="{{$indexItem}}">
																								<input type ="hidden" name="table_name" value="{{$table_name}}">
																								<input type ="hidden" name="record_id" value="{{$record_id}}">
																								<input type ="hidden" name="field_name" value="{{$field_name}}">	
																							</td>
																							<td>
																								<button type="submit" class="btn gradient-45deg-indigo-yellow btn-submit">
																									<span>Update</span>
																								</button>
																							</td>
																							{{ Form::close() }}
																							<td>
																								{{ Form::open(['method'=>'POST','id'=>'dataform','class'=>'dataform']) }}
																								{!! Form::token() !!}
																								@php
																								$fld=@$data->to_date;
																								$indexItem = Crypt::encrypt(@$data->id);
																								$table_name = Crypt::encrypt(@$master['table_name']); 
																								$record_id =	Crypt::encrypt(@$data->id);
																								$field_name =Crypt::encrypt('to_date');
																								@endphp
																								{!!Form::text("variable_value",(@$fld),['type'=>'text','class'=>'form-control ','id'=>$fld,'placeholder'=>'Enter ','autocomplete'=>'off']); !!}
																								<input type ="hidden" name="variable_name" value="{{$indexItem}}">
																								<input type ="hidden" name="table_name" value="{{$table_name}}">
																								<input type ="hidden" name="record_id" value="{{$record_id}}">	
																								<input type ="hidden" name="field_name" value="{{$field_name}}">	
																							</td>
																							<td>
																								<button type="submit" class="btn gradient-45deg-indigo-yellow btn-submit">
																									<span>Update</span>
																								</button>
																							</td>
																							{{ Form::close() }}
																						</tr>
																					@endforeach				
																				</table>	
																			@endif
																		</fieldset>
																	@endif
																</div>
															</li>											  
														</ul>
													@else
														@if(!$isPresent)
															<span class="center" style="font-weight:900;color:black;font:20px;"><center>Data Not	 Found</center></span>
														@endif
													@endif
												@endforeach	
										    @endforeach
										@else
										<span class="center" style="font-weight:900;color:black;font:20px;"><center>Data Not Found</center></span>
										@endif
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
</div> 
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/SingleScreenDate.js') !!}"></script> 
@endsection 