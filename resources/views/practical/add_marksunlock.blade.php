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
								
								{{ Form::open(['route' => [request()->route()->getAction()['as'], $e_user_examiner_map_id],'method'=>'POST','id' => 'PracticalMarkSubmission','autocomplete'=>'off']) }}
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
										@php $i=1;@endphp
										@foreach($final_data as $data)
									
										<tbody>
											
											@php Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation',
											'value'=>'1']);
											
											@endphp
											<input type="hidden" name='page_type' value='2' id=''>
											<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
											<tr width=100%>
												<td colspan=2 width=50%><b style="color: black;font-size: large;font-weight: bold;">Date Time Start:-</b>{{@date('d-m-Y h:i A',strtotime(@$data['date_time_start']))}}</td>
												<td colspan=3 width=50% style="text-align: right;"><b style="color: black;font-size: large;font-weight: bold;">Date Time End:-</b>{{@date('d-m-Y h:i A',strtotime(@$data['date_time_end']))}}
												</td>
											</tr>
													
													@foreach($data['getpracticalstudentList'] as $k => $value)
									
													<tr>
													@php
													$disbale="";
													$click = "return true";
													if($value['final_practical_marks'] && $value['practical_absent'] != 1){
														$disbale='disabled';
														$click="return false";
													}
													@endphp
													    <td>{{ @$i; }}</td>
														<td>{{ @$value['name'] }}</td>
														<td>{{ @$value['enrollment'] }}</td>
														<td disabled>
															<label>
															<?php
															$is_check_class = '';
															if(@$value['practical_absent']=='1'){
																$is_check_class= 'checked="checked"';
															}
															?>
															<input type='checkbox' id="practicalAbsent<?php echo $i; ?>" name='data[{{ $i }}][practical_absent]' 
															class="practical_absent practical_absent_<?php echo $i; ?> check_absent_marks" <?php 
															echo $is_check_class; ?> value="{{@$value['id']}}" {{$disbale}}/><span>
															
															</span>
															
															</label>
														</td>
														<td >
															<?php 
															$final_practical_marks ='';
															if(@$value['practical_absent']!='1'){
															$final_practical_marks = @$value['final_practical_marks'];
															}
															?>
															<input type='text' id="finalPracticalMarks<?php echo @$value['id']; ?>" 
															name='data[{{ $i }}][final_practical_marks]' 
															class="num final_practical_marks final_practical_marks_<?php echo $i; ?>  
															check_absent_marks" value="{{ @$final_practical_marks }}"   
															<?php if(@$value['practical_absent']=='1'){  } ?>  
															maxlength="2" <?php echo $disbale; ?>>
															@if($disbale == 'disabled')
																<input type='hidden' id="finalPracticalMarks<?php echo @$value['id']; ?>" 
															name='data[{{ $i }}][final_practical_marks]' 
															class="num final_practical_marks final_practical_marks_<?php echo $i; ?>  
															check_absent_marks" value="{{ @$final_practical_marks }}"   
															<?php if(@$value['practical_absent']=='1'){  } ?>  
															maxlength="2" >
																@endif
															<input type='hidden' name='data[{{ $i }}][student_allotment_marks_id]' 
															class='student_allotment_marks_id' value="{{ $value['id'] }}"  >
														</td>
														
													</tr>
											@php $i++; @endphp
												@endforeach
											
										</tbody>
										 
											@endforeach
									</table>
									<div class="card">
										<div class="row">
											
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
											
												<div class="col m3 s12 mb-3" style="margin-top:1%">
												<button class="btn cyan waves-effect waves-light right" type="submit" name="action">Save & Preview 
												<i class="material-icons right">send</i>
												</button>
											</div>
										</div>
									</div>
									
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