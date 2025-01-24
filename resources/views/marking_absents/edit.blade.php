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
    </div>
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="card">
						<div class="card-content">
							<h6><a href="{{ route('marking_absents')}}" class="btn btn-xs btn-info right">Marking Absent student List</a></h6> 
						    <h6>{{ $title }}</h6>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="col s12 m12 l12">
					<div id="Form-advance" class="card card card-default scrollspy">
						<div class="card-content">
							<h4 class="card-title"></h4>
								{{ Form::open(['url'=>url()->current(),'id'=>'marking_absent']) }}
								{!! Form::token() !!}
								{{ method_field('PUT') }}
								<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
								<!-- <div class="row">
									<div class="input-field col m6 s12">
										@php $lbl='Exam Year'; $fld='exam_year'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$exam_year_session,$data1->$fld,['class'=>'select2 browser-default form-control','autocomplete'=>'off','placeholder'=>'Select Exam_Year','disabled']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m6 s12">
										@php $lbl='Exam Session'; $fld='exam_session'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$exam_month_session,$data1->$fld,['class'=>'select2 browser-default form-control','autocomplete'=>'off','placeholder'=>'Select Exam_Sessions','disabled']); !!}
										@include('elements.field_error')
									</div>
								</div> -->
								<div class="row">
									<div class="input-field col m4 s12">
										@php $lbl='Examination Center'; $fld='examcenter_detail_id'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$examiner_list,$data1->$fld,['class'=>' select2 browser-default exam_details_id form-control students_appearing_fields','id'=>'MarkingAbsentStudentExamcenterDetailId','autocomplete'=>'off','placeholder'=>'Select Examiner Center','disabled']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m4 s12">
										@php $lbl='Course'; $fld='course_id'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$course,$data1->$fld,['class'=>'select2 browser-default course_id form-control students_appearing_fields','id'=>'MarkingAbsentStudentCourseId','autocomplete'=>'off','id'=>'ExaminerName','placeholder'=>'Select Course','disabled']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m4 s12">
										@php $lbl='Subject'; $fld='subject_id'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$subjects,$data1->$fld,['class'=>'select2 browser-default subjectsid form-control students_appearing_fields','id'=>'MarkingAbsentStudentSubjectId','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','placeholder'=>'Select subjects','disabled']); !!}
										@include('elements.field_error')
									</div>
								</div>
								<div class="row">
									<div class="input-field col m4 s12">
										@php $lbl='Total Students Appearing'; $fld='total_students_appearing'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::text($fld,count($data),['type'=>'text','class'=>'total_students_appearing form-control num MarkingAbsentStudentTotalCopiesOfSubject','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m4 s12">
										@php 
										    $lbl='Total Copies of the subject'; $fld='total_copies_of_subject'; 
										    $totalcopies=count($data)-count($markingAbsentlist);
										@endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										{!!Form::text($fld,@$totalcopies,['type'=>'text','class'=>'total_copies_of_subject form-control num  MarkingAbsentStudentTotalAbsent','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>

									<div class="input-field col m4 s12">
										@php $lbl='Total Absent'; $fld='total_absent'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										{!!Form::text($fld,$data1->$fld,['type'=>'text','class'=>'total_absent form-control num ','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m3 s12">
										@php $lbl='Total NR'; $fld='total_nr'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										{!!Form::text($fld,$data1->$fld,['type'=>'text','class'=>'total_nr form-control num','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>
								</div>
								<div class="box-body table-responsive student_list_div">
									@include('marking_absents.get_appearing_student')
								</div>
								<div class="row">
									<div class="col m10 s12 mb-3">
										@php
											$route = Route::current()->getActionName();
											if(Route::current()->action['as'] == "marking_absents.edit"){
												$paramName1 = Route::current()->parameters()['id'];
												@endphp 
												<a href="{{ action($route, $paramName1) }}" class="btn btn-primary right">Reset</a>
												@php 
											}else{
												@endphp
													<a href="{{ action($route) }}" class="btn btn-primary right">Reset</a> 
												@php 
											}
										@endphp
									</div>
									<div class="col m2 s12 mb-3">
										<button class="btn cyan waves-effect waves-light right" style="background:linear-gradient(45deg,#12e471,#219243)!important;" type="submit" name="action">submit 
											<i class="material-icons right"></i>
										</button>
									</div>
								</div>
							{{ Form::close() }}
						
						</div>
					</div>
				</div>
			</div> 
		</div>
	</div>
</div> 

@endsection

@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/marking_student_absent.js') !!}"></script> 
@endsection 
