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
                            <h6>{{$title}}<h6>
	                    </div>
                    </div>
                    <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{$title}}</h4>
									{{ Form::open(['url'=>url()->current(),'id'=>'editresult']) }}
									{!! Form::token() !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									{{ method_field('PUT') }}
									<div class="row">
										<div class="input-field col m4 s12">
											@php $lbl='उपस्थिति पंजी (Enrollment)'; $fld='enrollment'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','disabled'=>'disabled']); !!}
											@include('elements.field_error')
										</div>

										<div class="input-field col m4 s12">
											@php $lbl='विषय कोड (Subject Code)'; $fld='subject_id'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$subjects,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','disabled'=>'disabled']); !!}
                                            @include('elements.field_error')
										</div>
                                       @if(!empty($toc_data) || ($data->stream == '2'))	
									   <div class="input-field col m4 s12">
										   @php $lbl='सत्रीय अंक (Sessional Marks)'; $fld='sessional_marks'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                           <br>	
											<h8  style="line-height: 38px;">0<br><hr></h8>
											{!!Form::hidden($fld,0,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','id'=>'sessionalmarks1','autocomplete'=>'off','required'=>'true']); !!}
                                            @include('elements.field_error')
										</div>
										@else	
											<div class="input-field col m4 s12">
												@php $lbl='सत्रीय अंक (Sessional Marks)'; $fld='sessional_marks'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','id'=>'sessionalmarks1','autocomplete'=>'off','required'=>'true']); !!}
												@include('elements.field_error')
											</div>
										@endif

									</div>
									<div class="row">
									@if(!empty($toc_data) || ($data->stream == '2'))
									<div class="input-field col m4 s12">
										@php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='sessional_marks_reil_result'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                           <br>	
											<h8  style="line-height: 38px;">0<br><hr></h8>
											{!!Form::hidden($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','autocomplete'=>'off','id'=>'ExamSubjectSessionalMarksReilResult',"onkeyup"=>"totalmarks()",'required'=>'true']); !!}
                                            @include('elements.field_error')
											</div>
										@else
										<div class="input-field col m4 s12">
											@php $lbl='अंतिम सेशनल मार्क्स  (Final Sessional Marks)'; $fld='sessional_marks_reil_result'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','autocomplete'=>'off','id'=>'ExamSubjectSessionalMarksReilResult',"onkeyup"=>"totalmarks()",'required'=>'true']); !!}
                                            @include('elements.field_error')
										</div>
										
										@endif	
                                        @if($subject_detalis->practical_type  == 0) 
											<div class="input-field col m4 s12">
												
												@php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='final_practical_marks'; @endphp
												
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												<br>	
												<h8  style="line-height: 38px;">999<br><hr></h8>
												{!!Form::hidden($fld,'999',['type'=>'text','class'=>'form-control num practicalmax','class'=>'practicalmax','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks',"onkeyup"=>"totalmarks()",'required'=>'true','readonly'=>'readonly']); !!}
												@include('elements.field_error')
											</div>
                                        @else
											<div class="input-field col m4 s12">
												@php $lbl='अंतिम व्यावहारिक अंक (Final Practical Marks)'; $fld='final_practical_marks'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num practicalmax','class'=>'practicalmax','autocomplete'=>'off','id'=>'ExamSubjectFinalPracticalMarks',"onkeyup"=>"totalmarks()",'required'=>'true']); !!}
												@include('elements.field_error')
											</div>
										@endif
										<div class="input-field col m4 s12">
											@php $lbl='फाइनल थ्योरी मार्क्स (Final Theory Marks)'; $fld='final_theory_marks'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num theorymax','class'=>'theorymark','autocomplete'=>'off','id'=>'ExamSubjectFinalTheoryMarks',"onkeyup"=>"totalmarks()",'required'=>'true']); !!}
											<span style="color:red"> Please convert mark with 0.9 as per rules***.</span>
											@include('elements.field_error')
										</div>
									</div>

									<div class="row">
										<div class="input-field col m4 s12">
											@php $lbl='कुल मार्क (Total Marks)'; $fld='total_marks'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','id'=>'ExamSubjectTotalMarks','required'=>'true','readonly'=>'readonly']); !!}
                                            @include('elements.field_error')
										</div>

										<div class="input-field col m4 s12">
											@php $lbl='अंतिम परिणाम Final Result'; 
											$fld='final_result';
											if($data->$fld=='P'){
												$data->$fld=1;
											}
											@endphp
											
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::select($fld,$result,@$data->$fld,['type'=>'Select','class'=>'form-control','autocomplete'=>'off','placeholder'=>'Select Final Result','required'=>'true']); !!}
											@include('elements.field_error')
										</div>
										@if(!empty($toc_data))
										<div class="input-field col m4 s12">
											@php $lbl='टीओसी (Toc)'; $fld='toc'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											<br>	
											<h8  style="line-height: 38px;">Yes<br><hr></h8>
										</div>
			                            @endif
										<div class="input-field col m4 s12">
											@php $lbl='टिप्पणी Remarks'; 
											$fld='remarks';
											@endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!! Form::textarea($fld,@$data->$fld,['class'=>'form-control', 'rows' => 10, 'cols' => 40]) !!}
											<!-- {!!Form::select($fld,$result,@$data->$fld,['type'=>'Select','class'=>'form-control','autocomplete'=>'off','placeholder'=>'Select Final Result','required'=>'true']); !!} -->
											@include('elements.field_error')
										</div>
									</div>
									<div class="row">
										<div class="col m10 s12 mb-3">
										<button class="btn cyan waves-effect waves-light right show_confirm" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);" type="submit" name="action">Submit
											</button>
										</div>
										<div class="col m2 s12 mb-3">
										<button class="btn cyan waves-effect waves-light right gradient-45deg-deep-orange-orange" type="reset">Reset
											</button>
										</div>
									</div>
								{{ Form::close() }}
								<h7 style="font-weight:bold;color:red;">*** Theory Marks conversion rules.</h7><br>
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
<script>
	var stream = '<?php echo  @$data->stream; ?>';
	var tocdata   = '<?php echo  @$toc_data->subject_id; ?>';
	var sessional_max_marks = '<?php echo @$subject_detalis->sessional_max_marks; ?>';
	var theory_max_marks = '<?php echo @$subject_detalis->theory_max_marks; ?>';
	var practical_max_marks = '<?php echo @$subject_detalis['practical_max_marks']; ?>';
	var prctical_type =  '<?php echo @$subject_detalis->practical_type ; ?>';
</script>
    <script src="{!! asset('public/app-assets/js/bladejs/editresult.js') !!}"></script> 
@endsection 
