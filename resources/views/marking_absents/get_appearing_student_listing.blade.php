@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
		<div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
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
	<div class="col s12">
		<div class="container">
			<div class="seaction">
				<div class="col s12 m12 l12">
					<div id="Form-advance" class="card card card-default scrollspy">
						<div class="card-content">
							<h4 class="card-title">Add Marking Absent Student</h4>
								{{ Form::open(['url'=>url()->current(),'id'=>'marking_absent']) }}
								{!! Form::token() !!}
								{{ method_field('POST') }}
								<div class="row">
									<div class="input-field col m6 s12">
										@php $lbl='Exam Year'; $fld='exam_year'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$exam_year_session,NULL,['class'=>'select2 browser-default form-control','autocomplete'=>'off','placeholder'=>'Select Exam_Year']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m6 s12">
										@php $lbl='Exam Session'; $fld='exam_session'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$exam_month_session,old($fld),['class'=>'select2 browser-default form-control','autocomplete'=>'off','placeholder'=>'Select Exam_Sessions']); !!}
										@include('elements.field_error')
									</div>
								</div>
								<div class="row">
									<div class="input-field col m4 s12">
										@php $lbl='Examination Center'; $fld='Examination Center'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$examiner_list,old($fld),['class'=>' select2 browser-default exam_details_id form-control students_appearing_fields','id'=>'MarkingAbsentStudentExamcenterDetailId','autocomplete'=>'off','required'=>'true','placeholder'=>'Select Examiner Center']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m4 s12">
										@php $lbl='Course'; $fld='course'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$course,old($fld),['class'=>'select2 browser-default course_id form-control students_appearing_fields','id'=>'MarkingAbsentStudentCourseId','autocomplete'=>'off','id'=>'ExaminerName','required'=>'true','placeholder'=>'Select Course']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m4 s12">
										@php $lbl='Subject'; $fld='subjects'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,$subjects,old($fld),['class'=>'select2 browser-default subjectsid form-control students_appearing_fields','id'=>'MarkingAbsentStudentSubjectId','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','required'=>'true','placeholder'=>'Select subjects']); !!}
										@include('elements.field_error')
									</div>
								</div>
								<div class="row">
									<div class="input-field col m4 s12">
										@php $lbl='Total Students Appearing'; $fld='Total Students Appearing'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control num MarkingAbsentStudentTotalCopiesOfSubject','autocomplete'=>'off','required'=>'true','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m4 s12">
										@php $lbl='Total Copies of the subject'; $fld='Total Copies of the subject'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control num  MarkingAbsentStudentTotalAbsent','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>

									<div class="input-field col m4 s12">
										@php $lbl='Total Absent'; $fld='Total Absent'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'total_absent form-control num ','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>
									<div class="input-field col m3 s12">
										@php $lbl='Total NR'; $fld='Total NR'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'total_nr form-control num','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									</div>
									
								</div>
                                
								<div class="row">
									<div class="col  s12 mb-2">
										<button class="btn cyan waves-effect waves-light get_student_list_btn" type="button" id="get_student_list_btn">Get List Of Students</button>
									</div>
								</div>

								<div class="row">
									<div class="col m10 s12 mb-3">
										{{-- <button class="btn cyan waves-effect waves-light right" type="reset">
											<i class="material-icons right">clear</i>Reset
										</button> --}}
									</div>
									<div class="col m2 s12 mb-3">
										<button class="btn cyan waves-effect waves-light right" type="submit" name="action">submit 
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


