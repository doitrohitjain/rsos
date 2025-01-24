@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.supplementary_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									<span class="card-title" style="font-size: 22px;"><b>{{ $page_title; }} </b></h6>
									@include('elements.ajax_validation_block') 
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<div class="row">
										<div class="input-field col s3">
										</div>
										<div class="input-field col s6">
											@php $lbl='नामांकन संख्या (Enrollment Number)'; $fld='enrollment'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control num','maxlength'=>15,'minLength'=>10,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
										</div> 
									</div>
									 
				
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right show_confirm" type="submit" name="action"> Search & Continue
											</button>
										</div>
										<div class="col m2 s10 mb-3">
											<button class="btn cyan waves-effect waves-light right" type="reset">Reset
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
	</div></br></br></br>
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/supplementary/find_enrollment.js') !!}"></script> 
@endsection 
 


