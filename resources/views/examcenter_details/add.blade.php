@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
							</div>
						</div>
						<div class="col s12 m12 l12">
						<div class="card">
							<div class="card-content">
							<h6><a href="{{ route('listing') }}" class="btn btn-xs btn-info right">back</a></h6>
							<h6>{{ $page_title; }}<h6>
							</div>
							</div>
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									@include('elements.ajax_validation_block') 
									{!! Form::open(array('url' => 'examcenter_details/examcenter_add','id' => $model)) !!}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name='school_id' value='{{@$schoolDataschoolid}}'>
									<input type="hidden" name='form_type' value='add'>
                                    <div class="row"> 
                                       @include('elements.forms.examcenter_details_form')
									</div> 
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right show_confirm2" type="submit" name="action"> Save & Continue
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
	</div>
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/examcenter_details_form.details.js') !!}"></script> 
@endsection 
 


