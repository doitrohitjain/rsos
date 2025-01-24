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
				<div class="row">
					<div class="col s12">
						<div class="container">
							<div class="seaction">
								<div class="card">
									<div class="card-content">
										<h6>Mark Scoring List</h6>
									</div>
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
													<th>S.R.NO</th>
													<th>Exam Center</th>
													<th>Course	</th>
													<th>Subject</th>
													<th>SSO ID</th>
													<th>Examiner<br>Name</th>
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
															<td>{{$count}}</td>
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
															    <td>yes</td>
															@else
															    <td>NO</td>
															@endif
															@if(!empty(@$data->theory_lastpage_submitted_date)&& @$data->marks_entry_completed==1)
																<td>
																	<a href="{{ route('theory_Edit_marks',Crypt::encrypt($data->id)) }}" class="btn btn-xs btn-info invoice-action-view">
																	Edit_Marks_Entry
																	</a>
																	<br><br>
																	<a href="{{ route('theory_mark_pdf',Crypt::encrypt($data->id)) }}" class="btn btn-xs btn-info invoice-action-view">
																		Generate Pdf
																		</a>
																</td>
															@else
															<td>
																<a href="{{ route('theory_add_marks',Crypt::encrypt($data->id)) }}" class="btn btn-xs btn-info invoice-action-view">
																ADD_Marks
																</a>
															</td>
															@endif
														</tr>
                                @endforeach 
                                    @php  $count++; @endphp
                            @else
                                <tr>
                                    <td colspan="10" class="text-center text-primary">There are no data.</td>
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