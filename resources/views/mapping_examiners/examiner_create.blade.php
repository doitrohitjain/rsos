@extends('layouts.default')
@section('content')

<style>
	  .frees {
        pointer-events: none;
      }
</style>
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
                <div class="seaction">
					<div class="card">
					<div class="card-content">
						<h6><a href="{{route('mapping_examiners')}}" class="btn btn-xs btn-info right">Examiner_List</a></h6>
						 <h6>Add Examiner</h6>
						</div>
					</div>
                    <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                {{-- <h4 class="card-title">Add examiner</h4> --}}
								@include('elements.ajax_validation_block')
									{{ Form::open(['url'=>url()->current(),'method'=>'POST','id'=>'TheoryExaminerForm']) }}
									{!! Form::token() !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<div class="row">
										<div class="input-field col m3 s12">
											@php $lbl='Exam Year'; $fld='exam_year'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::select($fld,$exam_year,old($fld),['class'=>'select2 browser-default form-control','autocomplete'=>'off','placeholder'=>'Select Exam_Year']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m3 s12">
											@php $lbl='Exam Session'; $fld='exam_month'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::select($fld,$exam_sessions,old($fld),['class'=>'select2 browser-default form-control','autocomplete'=>'off','placeholder'=>'Select Exam_Sessions']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m3 s12">
											@php $lbl='SSO Id'; $fld='ssoid'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control  sso_id','id'=>'examiner_ssoid','autocomplete'=>'off']); !!}
                                            @include('elements.field_error')
										</div>
                                        <div class="input-field col m3 s12">
											@php $lbl='Name of the Examiner'; $fld='name'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,old($fld),['type'=>'text','class'=>'examiner_name form-control frees','autocomplete'=>'off','id'=>'ExaminerName','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
										</div>
									</div>

									<div class="row">
										<div class="input-field col m3 s12">
											@php $lbl='Mobile number with SSO'; $fld='mobile'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,old($fld),['type'=>'text','class'=>'mobile form-control frees','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m3 s12">
											@php $lbl='Designation'; $fld='designation'; 
											    $value="Theroy Examiner";
												@endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control designation frees','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
										</div>
										<input type="hidden"  value="Theroy Examiner" name="role">
                                        {{-- <div class="input-field col m3 s12">
											@php $lbl='Additional Mobile Number'; $fld=''; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}</h8>
                                            {!!Form::text($fld,old($fld),['type'=>'text','class'=>'form-control num','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks']); !!}
                                            @include('elements.field_error')
										</div> --}}
                                        
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
</div>				
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/mapping_examiners.js') !!}"></script> 
@endsection 


