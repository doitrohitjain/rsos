@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
					
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									<h4 class="card-title">{{ $page_title; }} </h4>
									@include('elements.ajax_validation_block')
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<div class="row">
										<fieldset>
											<legend>
												<span style="font-weight: bold;color:#000000;">Mobile number Edit Details(मोबाइल नंबर विवरण संपादित करें)</span>
											</legend>
											<div class="row">
												<div class="col m12 s12">
												@php $lbl='मोबाइल नंबर (Mobile Number)'; $fld = "mobile"; @endphp
												<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
												<div class="input-field">
												{!!Form::text($fld,@$updatestudentmobile->$fld,['type'=>'text','class'=>' form-control num ','maxlength'=>10,'minLength'=>10,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
												</div>
												</div> 
												
											</div>
										</fieldset>
									</div><br>
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right show_confirm" type="submit" name="action"> Save & Continue
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
	<script src="{!! asset('public/app-assets/js/bladejs/bank_details.js') !!}"></script>
@endsection
<style>
.select2-container--default .select2-results > .select2-results__options{
	max-width:1000px;
}
.select2-selection__rendered{
	max-width:800px;
}
</style>