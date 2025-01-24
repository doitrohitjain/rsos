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
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="card">
						<div class="card-content">
							<div calss=""> 
								<div class="blue-text">
									Note: If you want to change the existing examiner please check.(यदि आप मौजूदा सिद्धांत परीक्षक को बदलना चाहते हैं तो कृपया जांचें।)
								</div>
								<div class="blue-text"> 
									Note : If the theory examiner is not found in the list please <a href="{{route('mapping_examiners.add')}}" class=""> Click here to add the theory examiner </a>.(यदि सिद्धांत परीक्षक सूची में नहीं मिला है तो कृपया सिद्धांत परीक्षक जोड़ने के लिए यहां क्लिक करें।)
								</div>
								<a href="{{ route('mapping_examiners.add')}}" title="Add theory examiner" class="btn btn-xs btn-info gradient-45deg-amber-amber">
									Add Examiner
								</a> &nbsp;
								<a href="{{ route('alloting_copies_examiners')}}" title="Alloting Copies Examiner List" class="btn btn-xs btn-info" style="background: linear-gradient(45deg,#8e24aa,#ff6e40)!important;">Alloting Copies List</a>
							</div>
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
							{{-- <h4 class="card-title">Allotting Examination Copies To Examiner</h4> --}}
							@include('elements.ajax_validation_block')
								{{ Form::open(['url'=>url()->current(),'id'=>'AllottingExaminationCopies']) }}
								{!! Form::token() !!}
								{{ method_field('POST') }}
								<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
								<div class="row">
									<div class="col m4 s12">
										@php $lbl='Exam Center Fixcode'; $fld='examcenter_detail_id'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
									    <div class='input-field'>
										{!!Form::select($fld,$examiner_list,old($fld),['class'=>' select2 browser-default exam_details_id form-control students_appearing_fields','id'=>'MarkingAbsentStudentExamcenterDetailId','autocomplete'=>'off','placeholder'=>'Select Exam Center Fixcode','required']); !!}
										@include('elements.field_error')
										</div>
									</div>
									<div class="col m4 s12">
										@php $lbl='Course'; $fld='course_id'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										<div class='input-field'>
										{!!Form::select($fld,$course,old($fld),['class'=>'select2 browser-default course_id form-control students_appearing_fields','id'=>'MarkingAbsentStudentCourseId','autocomplete'=>'off','id'=>'ExaminerName','placeholder'=>'Select Course','required']); !!}
										@include('elements.field_error')
										</div>
									</div>
									<div class="col m4 s12">
										@php $lbl='Subject'; $fld='subject_id'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										<div class='input-field'>
										{!!Form::select($fld,$subjects,old($fld),['class'=>'select2 browser-default subjectsid form-control students_appearing_fields','id'=>'MarkingAbsentStudentSubjectId','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','placeholder'=>'Select subjects','required']); !!}
										@include('elements.field_error')
										</div>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col m3 s12">
										{!!Form::hidden('marking_absent_student_id',old('marking_absent_student_id'),['type'=>'hidden','class'=>'form-control num  marking_absent_student_id','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@php $lbl='Total Students Appearing'; $fld='Totalstudentsappearing'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										<div class='input-field'>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control num totalapperingstudent','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
									     </div> 
									</div>
									<div class=" col m3 s12">
										@php $lbl='Total Copies of the subject'; $fld='totalcopiesofthesubject'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										<div class='input-field'>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control num  totalcopiesofsubjects','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
										</div>
									</div>

									<div class=" col m3 s12">
										@php $lbl='Total Absent'; $fld='total_absent'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										<div class='input-field'>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'total_absent form-control num ','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@include('elements.field_error')
										</div>
									</div>
									<div class=" col m3 s12">
										@php $lbl='Total NR'; $fld='Total_nr'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										<div class='input-field'>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'total_nr form-control num','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','readonly'=>'readonly']); !!}
										@include('elements.field_error')
										</div> 
									</div>
									
								</div>

								<div class="row">

								<div class="col m4 s12">
										@php $lbl='SSO  ' ; $fld='ssoid'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
									    <div class='input-field'>
										{!!Form::select($fld,$theoryExaminerList,old($fld),['class'=>' select2 browser-default exam_details_id form-control sso_id','id'=>'examiner_ssoid','autocomplete'=>'off','placeholder'=>'Select SSO','required']); !!}
										 
										@include('elements.field_error')
										</div>
									</div>
									<div class=" col m3 s12">
										@php $lbl='Name of the Examiner'; $fld='name'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										<div class='input-field'>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'examiner_name form-control frees','autocomplete'=>'off','id'=>'ExaminerName','readonly'=>'readonly']); !!}
										@include('elements.field_error')
										</div>
									</div>
									<div class=" col m3 s12">
										{!!Form::hidden('user_id',old('mapping_examiner_id'),['type'=>'hidden','class'=>'form-control num  mapping_examiner_id','autocomplete'=>'off','readonly'=>'readonly']); !!}
										@php $lbl='Mobile Number '; $fld='mobile'; @endphp
										<h8>{!!Form::label($fld, $lbl) !!}</h8>
										<div class='input-field'>
										{!!Form::text($fld,old($fld),['type'=>'text','class'=>'mobile form-control frees','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','readonly'=>'readonly']); !!}
										@include('elements.field_error')
										</div>
									</div>
								</div>
								<div class="row">
									{{-- <div class="col m3 s12">
										@php $lbl='Date of Allotment'; $placeholder = "Select ". $lbl; $fld='allotment_date'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field"> 
											{!!Form::text($fld,date('Y-m-d H:i:s'),['class'=>'form-control datepicker','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,'required']); !!}
											@include('elements.field_error')
										</div>	
									</div> --}}

									<div class="isChangedCls col m3 s12 hide">
										@php $lbl='Examiner changed'; $placeholder = "Select ". $lbl; $fld='is_changed';
										$secondvalue=0; 
                                        $fieldVal=(!empty(old($fld)))?old($fld):"";
                                        $status=(!empty($fieldVal)&&$fieldVal==1)?true:false;
                                        $fieldVal=(!$status)?1:0; 
										@endphp
										<span class="small_lable">@php //echo $lbl ; @endphp </span>
										<div class="input-field"> 
										<label>
									        {{ Form::hidden($fld,$secondvalue,array('type'=>'checkbox','class'=>' name filled-in' )) }} 
								        </label>
										</div>	
									</div>
								</div>  
                                <div class="row">
									<div class="col m10 s12 mb-3">
                                        @php $route = Route::current()->getActionName(); @endphp
										<button href="{{ action($route) }}" class="btn cyan waves-effect waves-light right" type="reset">
											<i class="material-icons right">clear</i>Reset
										</button>
									</div>
									<div class="col m2 s12 mb-3">
										<button class="btn cyan waves-effect waves-light right green btnSubmit" type="submit" name="action">submit 
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
</div>
@endsection

@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/alloting_copies_examiners.js') !!}"></script> 
@endsection 
