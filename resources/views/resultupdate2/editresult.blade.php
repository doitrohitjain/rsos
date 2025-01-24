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
                <div class="seaction">
        	        <div class="card">
			            <div class="card-content">
                            <h6>Edit Result<h6>
	                    </div>
                    </div>
                    <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">Edit Result</h4>
									{{ Form::open(['url'=>url()->current(),'id'=>'form_id']) }}
									{!! Form::token() !!}
									{{ method_field('PUT') }}
									<div class="row">
										<div class="input-field col m4 s12">
											@php $lbl='उपस्थिति पंजी (Enrollment)'; $fld='enrollment'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m4 s12">
											@php $lbl='विषय कोड (Subject Code)'; $fld='subject_id'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m4 s12">
											@php $lbl='सत्रीय अंक (Sessional Marks)'; $fld='sessional_marks'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','required'=>'true']); !!}
                                            @include('elements.field_error')
										</div>
									</div>

									<div class="row">
										<div class="input-field col m4 s12">
											@php $lbl='सेशनल मार्क्स रील रिजल्ट (Sessional Marks Reil Result)'; $fld='sessional_marks_reil_result'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','id'=>'ExamSubjectSessionalMarksReilResult',"onkeyup"=>"totalmarks()",'required'=>'true']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m4 s12">
											@php $lbl='अंतिम व्यावहारिक अंक (Final practical Marks)'; $fld='final_practical_marks'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks',"onkeyup"=>"totalmarks()",'required'=>'true']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m4 s12">
											@php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='final_theory_marks'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','id'=>'ExamSubjectFinalTheoryMarks',"onkeyup"=>"totalmarks()",'required'=>'true']); !!}
											<span style="color:red"> Please convert mark with 0.9 as per rules***.</span>
											@include('elements.field_error')
										</div>
									</div>

									<div class="row">
										<div class="input-field col m4 s12">
											@php $lbl='कुल मार्क (Total Marks)'; $fld='total_marks'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','id'=>'ExamSubjectTotalMarks','required'=>'true']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m4 s12">
											@php $lbl='अंतिम परिणाम Final Result'; $fld='final_result'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::select($fld,$result,@$data->$fld,['type'=>'Select','class'=>'form-control','autocomplete'=>'off','placeholder'=>'Select Final Result','required'=>'true']); !!}
											@error($fld)
											<span class="invalid-feedback" role="alert" style="color:red;">
												  <strong>{{ $message }}</strong>
											  </span>
										   @enderror
										</div>
			
									</div>
									<div class="row">
										<div class="col m10 s12 mb-3">
											{{-- <button class="btn cyan waves-effect waves-light right" type="reset">
												<i class="material-icons right">clear</i>Reset
											</button> --}}
										</div>
										<div class="col m2 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right" type="submit" name="action">Save 
												<i class="material-icons right"></i>
											</button>
										</div>
									</div>
								{{ Form::close() }}
								<h7 style="font-weight:bold;color:red;">*** Thenory Marks conversion rules.</h7><br>
							<h7 style="color:red;">1.Marks will not convert for TOC.</h7><br>
                               <h7 style="color:red;">2.Marks will not convert for stream2 candidates.</h7><br>
							   <h7 style="color:red;">Marks will not convert for 2015 and before candidates.<h7>
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
    <script src="{!! asset('public/app-assets/js/bladejs/update_filter_details.js') !!}"></script> 
@endsection 

<script>
	function totalmarks(){
	var sessionalMarks=$("#ExamSubjectSessionalMarksReilResult").val();
	var practicalMarks=$("#ExamSubjectFinalPracticalMarks").val();
	var theoryMarks= $("#ExamSubjectFinalTheoryMarks").val();
	sessionalMarks =(sessionalMarks >0 && sessionalMarks <= 10)?sessionalMarks:0;
	practicalMarks =(practicalMarks >0 && practicalMarks <= 100)?practicalMarks:0;
	theoryMarks =(theoryMarks >0 && theoryMarks <= 100)?theoryMarks:0;
	var totalmarks=eval(sessionalMarks)+eval(practicalMarks)+eval(theoryMarks);
	$("#ExamSubjectTotalMarks").val(totalmarks);
}
</script>

