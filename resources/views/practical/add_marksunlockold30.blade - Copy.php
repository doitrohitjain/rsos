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
									<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										<strong>
										@if ($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										</strong>
									</span>
										
									<h6>{{ $title }}<h6>
									<h6>
										<span class="z-depth-2" style="color:red;font-size:16px;line-height: 1.6;">
											{{ @$specialNoteRemarks[0] }}
										</span>
										@can('examiner_mapping_add')
										<a href="{{route('practicalexamineradd')}}" class="btn btn-xs btn-info right hide">Add Practical Examiner</a>
										@endcan
									</h6>
								</div>
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
									<tr>
										<th>Exam Center</th><td>{{ @$examcenter_list[@$examinerMapData['examcenter_detail_id']] }}</td>
										<th>Course</th><td>{{ @$course_list[@$examinerMapData['course']] }}</td>
										<th>Subject</th><td>{{ @$subject_list[@$examinerMapData['subject_id']] }}</td>
										<th>Min Marks</th><td>{{ @$subjectMinMarks }}</td>
										<th>Max Marks</th><td>{{ @$subjectMaxMarks }}</td>
									</tr>
								</table>
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
								<!--<form id="PracticalMarkSubmission" action="http://10.68.181.236/lrsos/practical/add_marks" />-->
								<!-- static url is temporary for testing -->
								{{ Form::open(['route' => [request()->route()->getAction()['as'], $e_user_examiner_map_id], 'id' => 'PracticalMarkSubmission','autocomplete'=>'off']) }}
									<table>
										<thead>
											<tr>
												<th>#</th>
												<th>Name</th>
												<th>Enrollment</th>
												<th>Is Absent</th>
												<th>Final Marks</th>
											</tr>
										</thead>
										<tbody>
											{!! Form::token() !!}
											{!! method_field('PUT') !!}
											@php Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation',
											'value'=>'1']); 
											@endphp
											<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
											@if($master) 
												@php 
												//echo "defaultPageLimit : ".$defaultPageLimit; 
												//echo "</br>currentPage : ".$master->currentPage(); die;
												if($master->currentPage()!=1){
													$i= $defaultPageLimit * ($master->currentPage()-1)+1; 
												} else {
													$i=1;
												}
												@endphp
												@foreach(@$master as $k=>$data)
													<tr>
														<td>{{ $i; }}</td>
														<td>{{ @$data->name }}</td>
														<td>{{ @$data->enrollment }}</td>
														<td>
															<label>
															<?php
															$is_check_class = '';
															if(@$data->practical_absent=='1'){
																$is_check_class= 'checked="checked"';
															}
															?>
															<input type='checkbox' id="practicalAbsent<?php echo $i; ?>" name='data[{{ $k }}][practical_absent]' class="practical_absent practical_absent_<?php echo $i; ?> check_absent_marks" <?php echo $is_check_class; ?> /><span></span>
															</label>
														</td>
														<!-- <td>
															<input type='text' id="finalPracticalMarks<?php echo $i; ?>" name='data[{{ $k }}][final_practical_marks]' class="final_practical_marks final_practical_marks_<?php echo $i; ?>  check_absent_marks" value="{{ ''; }}" <?php if(@$data->practical_absent=='1'){ echo "readonly"; } ?>>
															<input type='hidden' name='data[{{ $k }}][student_allotment_marks_id]' class='student_allotment_marks_id' value='{{ Crypt::encrypt($data->id) }}'>
														</td> -->
														
														<td>
															<?php 
															$final_practical_marks ='';
															if(@$data->practical_absent!='1'){
																$final_practical_marks = @$data->final_practical_marks;
															}
															?>
															<input type='text' id="finalPracticalMarks<?php echo $i; ?>" name='data[{{ $k }}][final_practical_marks]' class="num final_practical_marks final_practical_marks_<?php echo $i; ?>  check_absent_marks" value="{{ @$final_practical_marks }}"   <?php if(@$data->practical_absent=='1'){ echo "readonly"; } ?>  maxlength="2"  >
															
															<input type='hidden' name='data[{{ $k }}][student_allotment_marks_id]' class='student_allotment_marks_id' value='{{ Crypt::encrypt($data->id) }}'>
														</td>
													</tr>
												@php  $i++; @endphp
												@endforeach
											@endif
										</tbody>
									</table>
									
									<div class="card">
										<div class="row">
											<input type='hidden' name='last_page_id' class="last_page_id" value='{{ Crypt::encrypt($master->lastPage()) }}'></td>
											<input type='hidden' name='current_page_id' class="current_page_id" value='{{ Crypt::encrypt($master->currentPage()) }}'></td>
											<input type='hidden' name='min_marks' class="min_marks" value='{{ Crypt::encrypt($subjectMinMarks) }}'>
											<input type='hidden' name='max_marks' class="max_marks" value='{{ Crypt::encrypt($subjectMaxMarks) }}'>
										
											<div class="col m9 s12 mb-3" style="margin-top:1%">
												@php 
													//$action = route(Route::currentRouteName());   
													//$param = Route::current()->parameters['user_examiner_map_id'];
													//echo $action . $param;  
												@endphp
												<a href="#" class="btn cyan waves-effect waves-light right"><i class="material-icons right">clear</i> Reset </a>
											</div>
											<?php if($master->lastPage()==$master->currentPage()){ ?>
												<div class="col m3 s12 mb-3" style="margin-top:1%">
												<button class="btn cyan waves-effect waves-light right" type="submit" name="action">Save & Preview 
												<i class="material-icons right">send</i>
												</button>
											</div>
											<?php } else { ?>
												<div class="col m3 s12 mb-3" style="margin-top:1%">
												<button class="btn cyan waves-effect waves-light right submit_dsiabled" type="submit" name="action">Save & Next
												<i class="material-icons right">send</i>
												</button>
											</div>
											<?php } ?>
											
										</div>
									</div>
									{{ $master->links('elements.paginater') }}
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
@endsection

@section('customjs')
	<script>
		min_marks = '<?php echo $subjectMinMarks; ?>';
		max_marks = '<?php echo $subjectMaxMarks; ?>';
	</script>
	<script src="{!! asset('public/app-assets/js/bladejs/practical/add_marks.js') !!}"></script> 
@endsection 