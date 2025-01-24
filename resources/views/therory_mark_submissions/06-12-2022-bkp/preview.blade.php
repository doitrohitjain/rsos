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
				{{-- <div class="row">
					<div class="col s12">
						<div class="container">
							<div class="seaction">
								<div class="card">
									<div class="card-content">
										<h6>Examiner List</h6>
									</div>
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
													<td >{{@$subjects[@$data->subject_id]}}</td>
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
																	<span style="color:black">Absent</span>
																	@elseif(@$data->theory_absent=='2')
																	<span style="color:black">NR</span>
																
																			@else
																			<span style="color:black">{{$data->final_theory_marks}}</span>
																	@endif
										
																</td>
															</tr>
														@php  $i++; @endphp
														@endforeach
												
												</tbody>
											</table>
											
											<div class="card">
												<div class="row">
												
												
													<div class="col m11 s mb-3" >
														<button class="btn cyan waves-effect waves-light right delete-confirm" type="submit">
														<i class="material-icons right">send</i>Final Lock And Submitted
														</button>
													</div>
													
											 </div>
											</div>
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