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
								<h6><a href="{{ route('printduplicatemarksheetcertificate',Crypt::encrypt(@$data->ENROLLNO)) }}" class="btn btn-xs btn-info right">Download Marksheet And Certificate</a></h6>  
								<h6>{{$title}}<h6>
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									<h4 class="card-title">{{$title}}</h4>
										{{ Form::open(['url'=>url()->current(),'id'=>'updatepastdata']) }}
										{{ method_field('PUT') }}
										<div class="row">
											<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
											<div class="input-field col m3 s12">
												@php $lbl='Enrollment'; $fld='ENROLLNO'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!} @php  @endphp</h8>
												{!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','disabled'=>'disabled']); !!}
												@include('elements.field_error')
											</div>
											<div class="input-field col m3 s10">
												@php $lbl='Name'; $fld='NAME'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','required'=>true]); !!}
												@include('elements.field_error')
											</div>
											<div class="input-field col m3 s10">
												@php $lbl='Father Name'; $fld='FNAME'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','required'=>true]); !!}
												@include('elements.field_error')
											</div>
											<div class="input-field col m3 s10">
												@php $lbl='Mother Name'; $fld='MNAME'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$data->$fld,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','required'=>true]); !!}
												@include('elements.field_error')
											</div>
										</div>
										<div class="row">
											<div class="input-field col m3 s10">
												@php $lbl='Date of birth'; $fld='DOB';
												$dobFormat = @$data->$fld;
												$dobFormat = date("M d, Y",strtotime(@$dobFormat));
												@endphp
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,$dobFormat,['type'=>'text','class'=>'form-control datepicker',"readonly"=>true,'autocomplete'=>'off','required'=>true]); !!}
												@include('elements.field_error')
											</div>
											<div class="input-field col m3 s10">
												@php $lbl='Result Date'; $fld='ResultDate';
												$dobFormat2 = @$data->$fld;
												$dobFormat2 = date("M d, Y",strtotime(@$dobFormat2));
												@endphp
												<h8>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,$dobFormat2,['type'=>'text','class'=>'form-control datepicker',"readonly"=>true,'autocomplete'=>'off','required'=>true]); !!}
												@include('elements.field_error')
											</div>
											<div class="input-field col m3 s10">
												@php $lbl='Exam Month Year'; $fld='EX_YR'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::select($fld,$displayexammonth,@$data->$fld,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','required'=>true,'placeholder'=>'select exam year Month']); !!}
												@include('elements.field_error')
											</div>
										</div>
										<div class="row">
											<div class="col m9 s12 mb-3">
												<button class="btn cyan waves-effect waves-light right" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);" type="submit" name="action">Submit 
													<i class="material-icons right"></i>
												</button>
											</div>
											<div class="col m2 s12 mb-3">
												<button class="btn cyan waves-effect waves-light right gradient-45deg-deep-orange-orange" type="reset">
													<i class="material-icons right">clear</i>Reset
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
    <script src="{!! asset('public/app-assets/js/bladejs/updatepastdta.js') !!}"></script> 
@endsection 

