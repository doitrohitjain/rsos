@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card">
								@include('elements.student_top_navigation')
							</div>
						</div>
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
												<span style="font-weight: bold;color:#000000;">Account Holder Personal Details(खाताधारक का व्यक्तिगत विवरण)</span>
											</legend>
											<div class="row">
												<div class="input-field col s12 l4 m4">
													@php $lbl='बैंक खाता धारक का नाम (Account Holder Name)'; $fld='account_holder_name'; @endphp
													<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
													{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'txtOnly form-control uppercase','maxlength'=>70,'minLength'=>2,'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												<div class="input-field col s12 l4 m4">
													@php $lbl='बैंक खाता संख्या (Account Number)'; $fld='account_number'; @endphp
													<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
													{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control uppercase num','maxlength'=>25,'minLength'=>2,'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												<div class="input-field col s12 l4 m4">
													@php $lbl='उपरोक्त खाता संख्या के साथ लिंक  मोबाइल नंबर (Mobile)'; $fld='linked_mobile'; @endphp
													<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
													{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control uppercase  num','maxlength'=>10,'minLength'=>10,'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
											</div>
										</fieldset>
									</div>
									<div class="row">
											<fieldset>
												<legend>
													<span style="font-weight: bold;color:#000000;">Account Holder Bank Details(खाताधारक का बैंक विवरण)</span>
												</legend>
												<div class="row">
													<div class="input-field col  s12 m12 l12">
														@php $lbl='बैंक का नाम (Bank Name)'; $fld='bank_name';
															$selectedBankId = @$flipBanks[@$master->$fld];
														@endphp
													<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!! Form::select($fld,$banks,@$selectedBankId, ['id' => $fld,'class' => 'form-control state_id bank_name_state select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
														@include('elements.field_error')
													</div> 
													<div class="input-field col  s12 m12 l12">
														@php $lbl='राज्य का नाम (State Name)'; $fld='state_id';
															$selectedStateId = @$master->$fld;
														@endphp
														<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!! Form::select($fld,@$state_list,@$selectedStateId, ['id' => $fld,'class' => 'form-control bank_name_state state_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
														@include('elements.field_error')
													</div>
													
													<div class="input-field col s12 m12 l12">
														@php $lbl='बैंक आईएफएससी कोड(IFSC Code)'; $fld='ifsc_code'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!! Form::select($fld,$ifcs_list,@$master->$fld, ['id' => $fld,'class' => 'form-control state_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
														@include('elements.field_error')
													</div>
													
												</div>
												
												<div class="row">
													<div class="input-field col s6">
														@php $lbl='बैंक शाखा का नाम(Branch Name)'; $fld='branch_name'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!!Form::text($fld,@$master->$fld,['id' => $fld,'type'=>'text','class'=>'txtOnly form-control uppercase ','maxlength'=>70,'minLength'=>2,'autocomplete'=>'off','readonly'=>'readonly']); !!}
														@include('elements.field_error')
													</div>
													<div class="input-field col s6">
														@php $lbl='एमआईसीआर (MICR)'; $fld='MICR'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}</h8>
														{!!Form::text($fld,@$ifsccodefecthdata->$fld,['id' => $fld,'type'=>'text','class'=>' uppercase txtOnly form-control','maxlength'=>70,'minLength'=>2,'autocomplete'=>'off','disabled'=>'disabled']); !!}
														@include('elements.field_error')
													</div>
												</div>
												<div class="row">
													<div class="input-field col s12">
														@php $lbl='शाखा पता (Branch Address)'; $fld='branch_address'; @endphp
															<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!!Form::textarea($fld,@$ifsccodefecthdata->BRANCH_ADDRESS,['id'=> $fld,'type'=>'text','class'=>' uppercase  txtOnly form-control','autocomplete'=>'off','disabled'=>'disabled','style'=>'resize: none;']); !!}
														@include('elements.field_error')
													</div>
												</div>
											</fieldset>
										</div>
										<br>
				
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