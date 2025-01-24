@extends('layouts.default')
@section('content')
@if(!empty(@$id = Auth::user()->id))
	<div id="main">
		<div class="row">
		@endif
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								 
							</div>
						</div> 
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									<h4 class="card-title">{{ $page_title; }} </h4>
									@include('elements.ajax_validation_block') 
									{{ Form::open(['route' => [request()->route()->getAction()['as']], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<div class="row">
										<div class="input-field col s3"></div>
										<div class="input-field col s6">
											@php $lbl='Enrollment No.'; $fld='enrollment'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'requried form-control num','maxlength'=>11,'minLength'=>11,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
										</div>
									</div>
									<div class="row">
										<div class="input-field col s3"></div>
										<div class="input-field col s6">
											@php $lbl='Mobile Number'; $fld='mobile'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'requried form-control num','maxlength'=>10,'minLength'=>10,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
										</div>
									</div>
									<div class="row">
										<div class="input-field col s3"></div>
										<div class="input-field col s6">
											@php $lbl='Date of Birth(dd/mm/yyyy) '; $fld='dob'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$dobFormat,['class'=>'requried datepicker','autocomplete'=>'off','placeholder' => $lbl,]); !!}
											@include('elements.field_error')
										</div> 
									</div>  

									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn green waves-effect waves-light right show_confirm" type="submit" name="action"> 
												Search & Continue
											</button>
										</div>
										<div class="col m2 s10 mb-3">
											<button class="btn red waves-effect waves-light right" type="reset">
												Reset
											</button>
										</div>
									</div>
									{{ Form::close() }}
								</div>  
							</div>
						</div>
					</div>
				</div>
			</div><br><br><br><br>
	@if(!empty(@$id = Auth::user()->id))
		</div>
	</div><br><br><br><br>
	@endif
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/supp_payment/supp_admission_fee_payment.js') !!}"></script> 
@endsection