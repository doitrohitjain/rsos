@extends('layouts.default')
@section('content')
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
		<div class="row">
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
										<table>
											<thead>	
												<tr>	
													<th>Sr. No</th>	
													<th>Exam Center	ode</th>
													<th>Course	</th>	
														<th>Subject</th>	
														<th>SSO</th>	
														<th>Examine	Name</th>
														<th>Mobile</th>	
													<th>Total Appearing</th>
													<th>Total Copies<br>of the subject</th>
													<th>Absent</th>
													<th>NR</th>
													<th>Date Of Allotment</th>
													<th>Is Lock &<br>Submitted</th>
													<th>Action</th>	
												</tr>
											</thead>
											<tbody>
												 @php $count=1; @endphp
												@if(!empty(@$master) && @$master->count(0))   
												@foreach(@$master as $data)

														<tr>
															<td>{{$count++}}</td>
															<td>{{@$examiner_list[@$data->examcenter_detail_id]}}</td>
															<td>{{@$course[@$data->course_id]}}</td>
															<td>{{@$data->subject_name}}</td>
															<td>{{@$data->name}}</td>
															<td>{{@$data->ssoid}}</td>
															<td>{{@$data->mobile}}</td>
															<td>{{@$data->total_students_appearing}}</td>
															<td>{{@$data->total_copies_of_subject}}</td>
															<td>{{@$data->total_absent}}</td>
															<td>{{@$data->total_nr}}</td>
															<td>{{@$data->allotment_date}}</td>
														    @if(!empty(@$data->theory_lastpage_submitted_date)&& @$data->marks_entry_completed==1)
															    <td>Yes</td>
															@else
															    <td>No</td>
															@endif 
															



															@if(!empty(@$data->theory_lastpage_submitted_date) && @$data->marks_entry_completed == 1)
																<td>
																	<a href="{{ route('theory_mark_pdf',Crypt::encrypt($data->id)) }}" class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style='margin:10px'>
																		Generate&nbsp;Pdf
																	</a>
																
																	
																</td>
															
																<!-- <td>
																	<a href="{{ route('theory_Edit_marks',Crypt::encrypt($data->id)) }}" class="btn btn-xs btn-info invoice-action-view">
																		Update
																	</a>
																</td> -->
																@else
																<td>
																	
																	@if(@$isAllowDate)
																		<a href="{{ route('theory_add_marks',Crypt::encrypt($data->id)) }}" class="btn gradient-45deg-indigo-purple">
																			<span>Add&nbsp;Marks</span>
																		</a>
																	@endif
																</td>
																@endif
															
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

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/theory.js') !!}"></script> 
@endsection 