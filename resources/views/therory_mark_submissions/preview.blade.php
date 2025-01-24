@extends('layouts.default')
@section('content')
<style>
	.alignclass{
		text-align: center;

	}
</style>
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
		
        {{-- <div class="col s12">
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
				</div> --}}
				
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<table>
									
										<tbody>
											<tr >
												<th >Examination Center</th>
												<td >{{@$examiner_list[@$data->examcenter_detail_id]}}</td>
												<th >Course</th>
												<td >{{@$course[@$data->course_id]}}</td>
												<th >Subject</th>
												<td >{{$subjects[@$data->subject_id]}}</td>
												<th >Examiner SSO ID</th>
												<td >{{@$userdetails->ssoid}}</td>
											</tr>
											<tr class='alignclass'>
												<th>Examiner Name</th>
												<td>{{@$userdetails->name}}</td>
												<th>Total Students Appearing</th>
												<td>{{@$data->total_students_appearing}}</td>	
												<th>Total Copies of the subject</th>
												<td >{{@$data->total_copies_of_subject}}</td>
												<th >Total Absent</th>
												<td >{{@$data->total_absent}}</td>
											</tr>
											
											<tr class='alignclass'>
												<th >Total NR</th>
												<td >{{@$data->total_nr}}</td>
												<th>Maximum Marks</th>
												<td >{{@$getMaxMarks->theory_max_marks}}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				@if(@$message)
					<div class="row">
						<div class="col s12">
							<div class="container">
								<div class="seaction">
									<div class="card">
										<div class="card-content">
											<span class="red-text">Note : {{ @$message }}</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>    
				@endif
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content"> 
									<div class="row"> 
										{{ Form::open(['url'=>url()->current(),'id'=>'marks_submissions_id']) }}
										{!! Form::token() !!}
										{{ method_field('POST') }}
											<table>
												<thead>
													<tr>
														<th >S.R</th>
														<th style='text-align:center'>Fictitious Code</th>
														<th>Marks Scored by the student</th>
													</tr>
												</thead>
												<tbody>
													<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
													<input type="hidden" name='Marking_absent_id' value={{Crypt::encrypt($id)}} id='ajaxRequest'>
													@php	
													$i=1;
														@endphp
														@foreach(@$result as $k=>$data)
															<tr>
																<td>{{ $i }}</td>
																<td style='text-align:center'>{{ @$data->fixcode }}</td>
																<td>
																	@if(@$data->theory_absent=='1')
																	<span class='final_theory_marks_span red-text' >Absent</span>
																	@elseif(@$data->theory_absent=='2')
																	<span class='final_theory_marks_span blue-text' >NR</span>
																
																			@else
																			<span style="color:black">{{$data->final_theory_marks}}</span>
																	@endif
										
																</td>
															</tr>
														@php  $i++; @endphp
														@endforeach
												
												</tbody>
											</table>
											
											@if(@$message)
												<div class="row">
													<div class="col s12">
														<div class="container">
															<div class="seaction">
																<div class="card">
																	<div class="card-content">
																		<span class="red-text">Note : {{ @$message }}</span>
																		&nbsp;&nbsp;&nbsp;<a href="{{ route('theory_add_marks',$eid) }}" class="btn gradient-45deg-indigo-purple">
																			<span>
																				Update Marks
																			</span>
																		</a> 
																	</div>
																	
																</div>
															</div>
														</div>
													</div>
												</div>  
												
												<div class="col m9 s12 mb-3" style="margin-top:1%"> 
													
												</div> 
											@else
												<div class="card">
													<div class="">  
													<div class="col m11 s mb-3" >
														<div class="col m12 s12 mb-12" style="margin-top:1%">
															@php
																$route = 'theory_add_marks';
																if(Route::current()->action['as'] == "theorymarkpreview"){
																	$paramName1 = Route::current()->parameters()['id']; 
																@endphp  
																	<a href="{{ route('theory_add_marks',$eid) }}" class="btn gradient-45deg-indigo-purple">
																		<span>Update &nbsp; Marks</span>
																	</a> 
																	@php 
																}else{ 
																}
															@endphp
															<button class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange right delete-confirm" type="submit">
																<i class="material-icons right">send</i>Final Lock And Submitted
															</button>
														</div> 
													</div> 
												</div>
												</div>
											@endif
											{{-- {{ $result->links('elements.paginater') }} --}}
											{{ Form::close() }}
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
    <script src="{!! asset('public/app-assets/js/bladejs/theory_preview.js') !!}"></script> 
@endsection 