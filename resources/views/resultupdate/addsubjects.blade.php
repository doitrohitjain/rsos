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
                        <h6><a href="{{ route('updateindex', [Crypt::encrypt($data->enrollment)]) }}" class="btn btn-xs btn-info right">back</a></h6>  
                           
                    
                            <h6>{{$title}}<h6>
	                    </div>
                    </div>
                </div> 
			</div>
		</div>
        <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{$title}}</h4>
									{{ Form::open(['url'=>url()->current(),'id'=>'addsubjects']) }}
									{!! Form::token() !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									{{ method_field('PUT') }}
									<div class="row">
										<div class="input-field col m4 s12">
											@php $lbl='उपस्थिति पंजी (Enrollment)'; $fld='enrollment'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num ','autocomplete'=>'off','id'=>'enrollment','disabled'=>'disabled']); !!}
											@include('elements.field_error')
										</div>
                                        <div class="input-field col m4 s12">
                                            <input type="hidden" name='student_id' class='checksubject' value='{{@$data->id}}' id='studentid'>
											@php $lbl='विषय जोड़ें (Add Subject)'; 
											$fld='subject_id';
											@endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::select($fld,$subjects,null,['type'=>'Select ','class'=>'form-control','class'=>'checksubject','autocomplete'=>'off','id'=>'subjectid','placeholder'=>'Select Subject','required']); !!}
											@include('elements.field_error')
										</div>
                                        <div class="input-field col m4 s12">
											@php $lbl='परीक्षा वर्ष (Exam Year)'; 
											$fld='exam_year';
											@endphp
											
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::select($fld,$exam_year,null,['type'=>'Select','class'=>'form-control','autocomplete'=>'off','placeholder'=>'Select Exam Year','required']); !!}
											@include('elements.field_error')
										</div>
                                        <div class="input-field col m4 s12">
											@php $lbl='परीक्षा वर्ष (Exam Year)'; 
											$fld='exam_month';
											@endphp
											
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            {!!Form::select($fld,$exam_month,null,['type'=>'Select','class'=>'form-control','autocomplete'=>'off','placeholder'=>'Select Exam Year','required']); !!}
											@include('elements.field_error')
										</div>

									</div>
									<div class="row">
									</div
									<div class="row">
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
                            </div>
                        </div>
                    </div>
	</div>
</div>	

@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/addsubjects.js') !!}"></script> 
@endsection 