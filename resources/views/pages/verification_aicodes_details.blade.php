@extends('layouts.default')
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
		<!--<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="card">
							<div class="card-content">
								<div class="blue-text">
									Note: To enter the theory marks of the student please use the respective combination.
									<br>(छात्र के सिद्धांत अंक दर्ज करने के लिए कृपया संबंधित संयोजन का उपयोग करें।)
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->
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
										<table>
											<thead>	
												<tr>	
													<th>Sr. No</th>	
													<th>SSO</th>
													<th>Ai Codes</th>
													<th>Action</th>	
												</tr>
											</thead>
											<tbody>
												@php $count=1; @endphp
												@if(!empty(@$master) && @$master->count(0))   
													@foreach(@$master as $data)
														<tr>
															<td>{{@$count++}}</td>
															@php
																$fld="aicodes";
																$id=Crypt::encrypt($data->id);	$table_type=Crypt::encrypt(@$user_type)
															@endphp
															{{ Form::open(['url'=>route('verficationDataUpdate'),'method'=>'POST','id'=>'dataform','class'=>'dataform']) }}
																{!! Form::token() !!}
															<td>
															@php $fld='user_id'; @endphp
															<div class="input-field">
														    	{!!Form::select($fld,@$user_data,$data->$fld,['class'=>'select2 browser-default form-control  exam_details_id form-control students_appearing_fields','autocomplete'=>'off','placeholder'=>'Select SSO ','required'=>'required']); !!}
																
															</div>
															</td>	
															<td>{!!Form::textarea("aicodes",(@$data->aicodes),['type'=>'text','class'=>'form-control ','id'=>$fld,'placeholder'=>'Enter ','autocomplete'=>'off']); !!}
															 <input type ="hidden" name="id" value="{{$id}}">
															 <input type ="hidden" name="table_type" value="{{$table_type}}">
															</td>
															<td>
																<button type="submit" class="btn gradient-45deg-indigo-yellow btn-submit">
																<span>Update</span>
																</button>
															</td>
															{{Form::close() }}
														</tr>	
													@endforeach 
													@php  $count++; @endphp
												@else
													<tr>
														<td colspan="10" class="text-center text-primary">No data found</td>
													</tr>
												@endif 	
											</tbody>
										</table>
										{{$master->links('elements.paginater')}}
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