<h4 class="card-title">Search Enrollment</h4>
									@include('elements.ajax_validation_block') 
									{{Form::open(['route' => 'updateindex','method'=>"POST",'id'=>'form_id']) }}
									{!! Form::token() !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<div class="row">
										<div class="input-field col s3">
										</div>
										<div class="input-field col s6">
											@php $lbl='नामांकन संख्या (Enrollment Number)'; $fld='enrollment'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control num','maxlength'=>11,'minLength'=>11,'autocomplete'=>'off','required'=>true]); !!}
											@include('elements.field_error')
										</div> 
									</div>
									 
				
									<div class="row">
										<div class="input-field col s3">
										</div>

										<div class="input-field col s3">
											<button class="btn cyan waves-effect waves-light right show_confirm" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);" type="submit" name="action"> Search & Continue
											</button>
											
										</div>
										<div class="input-field col s3">
											<button class="btn cyan waves-effect waves-light right gradient-45deg-deep-orange-orange" type="reset">Reset
											</button>
										</div>
									</div>
									{{ Form::close() }}
    
	<style>
		label {
			font-size: 1.5rem;
			color: #9e9e9e;
		}
		.error{
			color: red;
		}
	</style>
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/update_filter_details.js') !!}"></script> 
@endsection 