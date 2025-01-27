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
							{{--<span class="z-depth-2" style="color:red;font-size:16px;line-height: 1.6;">
									{{ @$specialNoteRemarks[0] }}
							</span>--}}
								<a href="{{route('practicalexamineradd')}}" class="btn btn-xs btn-info right hide">Add Practical Examiner</a>
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
										<th>Exam Cntrer</th><td>{{ @$examcenter_list[@$examinerMapData['examcenter_detail_id']] }}</td>
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
								{{ Form::open(['route' => [request()->route()->getAction()['as'], $e_user_examiner_map_id], 'id' => 'PracticalMarkSubmission','autocomplete'=>'off','enctype' => 'multipart/form-data']) }}
									<table>
										<tr>
											<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
											<td><a href="{{route('practicalMarksSubmissionPdf',$e_user_examiner_map_id)}}" 
												class="btn btn-xs btn-info left">Download Practical Marks Pdf</a></td>
										</tr>
										<span style="color:red; font-size:15px;"><b>Note:-कृपया "Download Practical Marks Pdf" पर क्लिक करके पीडीएफ डाउनलोड करें, इस पर हस्ताक्षर करें। "Choose File" बटन पर क्लिक करें। हस्ताक्षरित फ़ाइल का चयन करें और "Finally Submit" बटन पर क्लिक करें</b></span>
									</table>	
									<div class="card">
										<div class="row">
											<div class="col m12 s12 mb-3" style="margin-top:1%">
												<td><label><b>Upload your signed marksheet PDF   :    </b></label></td>
												@php $fld = 'practical_marks_pdf'; @endphp
												<td>{!!Form::file($fld,['type'=>'file', "id" => $fld, 'class'=>'practical_marks_pdf form-control','autocomplete'=>'off','required'=>'required']);  !!}
												</td>
												<button class="btn cyan waves-effect waves-light right submit_dsiabled" type="submit" name="action">Finally Submit
												<i class="material-icons right">send</i>
												</button>
											</div>
										</div>
									</div>
									
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
											@if($master) 
												@php $i=1;  @endphp
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
															
																$is_check_class= "checked";
																
															}
															
															?>
															<input type='checkbox' id="practicalAbsent<?php echo $i; ?>" name='data[{{ $k }}][practical_absent]' class="practical_absent practical_absent_<?php echo $i; ?> check_absent_marks" <?php echo $is_check_class; ?>   disabled /><span></span>
															</label>
														</td>
														<td>
															<input type='text' id="finalPracticalMarks<?php echo $i; ?>" name='data[{{ $k }}][final_practical_marks]' class="final_practical_marks final_practical_marks_<?php echo $i; ?>  check_absent_marks" value="{{ @$data->final_practical_marks }}" disabled>
															<input type='hidden' name='data[{{ $k }}][student_allotment_marks_id]' class='student_allotment_marks_id' value='{{ Crypt::encrypt($data->id) }}'>
														</td>
													</tr>
												@php  $i++; @endphp
												@endforeach
											@endif
										</tbody>
									</table>
									
									{!! Form::token() !!}
											{!! method_field('PUT') !!}
											@php Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation',
											'value'=>'1']); 
											@endphp
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
	<script src="{!! asset('public/app-assets/js/bladejs/practical/examiner_marks_docupload.js') !!}"></script> 
@endsection 