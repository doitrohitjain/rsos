<?php use App\Component\PracticalCustomComponent; ?>
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
						<p class="caption mb-0">
							<h6>
								<span style="margin-left: 85%;"><a href="{{ route('practicalexaminer') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
							<h6>
						</p> 
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
													<th>#</th>
													<th>Academic Year</th>
													<th>Exam Center Name</th>
													<th>Examiner Name</th>
													<th>Examiner SSO</th>
													<th>Course</th>
													<th>Stream</th>
													<th>Subject</th>
													<th>No of Student</th>
													<th>Is Lock & Submitted</th>
													<th>Is Signed PDF Upload</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												@php  
												$i= 1; @endphp
												@foreach(@$master as $data)
												<tr>
													<td>{{ $i; }}</td>
													<td><?php if(isset($exam_year_arr[@$current_exam_year]) && isset($exam_month_arr[@$current_exam_month])){ echo $exam_year_arr[@$current_exam_year]."/".$exam_month_arr[@$current_exam_month]; 
													}else { echo "-"; } ?></td>
													<td><?php if(isset($examcenter_list[@$data->examcenter_detail_id])){ echo $examcenter_list[@$data->examcenter_detail_id]; }else { echo "-"; } ?></td>
													<td>{{ strtoupper(@$data->name) }}</td>
													<td>{{ strtoupper(@$data->ssoid) }}</td>
													<td>{{ @$data->course }}th</td>
													<td>{{ @$data->stream }}</td>
													<td>
														<?php if(isset($subject_list[$data->subject_id])){ echo $subject_list[$data->subject_id]; }else { echo "-"; } ?>
													</td>
													<td>
														<?php 
														$practical_component_obj = new PracticalCustomComponent;
														$StudentCountArr = $practical_component_obj->getPracticalStudentList(@$data->examcenter_detail_id,$data->subject_id,false);
														if(@$StudentCountArr){
															echo count($StudentCountArr);
														} else {
															echo "-";
														}
														?>
													</td>
													<td>
														<?php 
															$fld="is_lock_submit";
															if(@$data->$fld){ 
																echo "Yes";
															}else{
																echo "No";
															}
														?>
													</td>
													<td>
														<?php 
															$fld="document";
															if(@$data->$fld){
																echo "Yes";
															}else{
																echo "No";
															}
														?>
													</td>
													<td> 
														@php if($data->is_lock_submit==1){ @endphp
														@can('practical_unlock')
														<a href="{{ route('practicalMarksUnlock',Crypt::encrypt($data->id)) }}"	 
														class="delete-confirm btn btn-xs btn-info" 
														data-id="{{ Crypt::encrypt(@$data->id) }}">
															Unlock
														</a>
														@endcan
														
														<br><br>
														<a href="{{ route('practicalMarksUnlock',[Crypt::encrypt($data->id),'1'])}}"	 
														class="delete-confirm btn btn-xs btn-info" 
														data-id="{{ Crypt::encrypt(@$data->id) }}">
															Allow_Marks_Entrys
														</a>
														@php } @endphp
														
														</br></br>
														@can('practical_examiner_delete')
														<a href="{{ route('practicalexaminerdestory',Crypt::encrypt($data->id)) }}" class="delete-confirm btn gradient-45deg-purple-deep-orange
															" 
															data-id="{{ Crypt::encrypt(@$data->id) }}">
															{{-- {{ @$data->id }} --}}
															 <i class="material-icons">delete</i>
														</a>
														@endcan
													</td>
												</tr>
												@php  $i++; @endphp
												@endforeach
											</tbody>
										</table>
										{{ $master->links('elements.paginater') }}
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
	<script src="{!! asset('public/app-assets/js/bladejs/practical/examiner_mapping_lists.js') !!}"></script> 
@endsection 


