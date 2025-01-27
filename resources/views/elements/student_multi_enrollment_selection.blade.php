@php 
	use App\Helper\CustomHelper;
	$selected_enrollment = CustomHelper::_getCurrentLoginStudentEnrollment();

@endphp
 {{ Form::open(['id' => "studentMultiEnrollmentForm"]) }}
 {!! Form::token() !!}
 {!! method_field('PUT') !!}
	<div class="center">
		@php $lbl='नामांकन(Enrollment)'; $placeholder = "Select ". $lbl; $fld='student_multi_enrollment'; @endphp
		{!! Form::select($fld,@$student_multi_enrollments, @$selected_enrollment, ['class' => 'form-control student_multi_enrollment select2 select2a browser-default center-align','placeholder' =>$lbl]) !!}
		@include('elements.field_error')
	</div>
{{ Form::close() }} 
<script src="{!! asset('public/app-assets/js/bladejs/student_multi_enrollment_selection.js') !!}"></script>