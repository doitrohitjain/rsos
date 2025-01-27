@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							 
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									<h4 class="card-title">{{ @$page_title; }} </h4>
									@include('elements.ajax_validation_block')
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $esupp_id, 3], 'id' => @$model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'> 
									<div class="row">
											<fieldset>
												<legend>
													<span style="font-weight: bold;color:#000000;">Details of Student Mark Rejection(छात्र मार्क अस्वीकृति का विवरण)</span>
												</legend>  
													<div class="row">
														<div class="col m12 s12">
															@php $lbl='अस्वीकार दस्तावेज़ (Rejected Documents)'; $placeholder = "Select ". $lbl; $fld='aicenter_rejected_marksheet_document'; @endphp
															<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
															<div class="input-field">
															{!! Form::select($fld,@$supp_rejection_value_status,2, ['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype course','placeholder' => $placeholder,'id'=>'course' ]) !!}
															
															@include('elements.field_error')
															</div>
														</div>
													</div> 
													<div class="row">
														<div class="input-field col s12">
															@php $lbl='रिमार्क (Remark)'; $fld='aicenter_remark'; @endphp
																<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
															{!!Form::textarea($fld,null,['id'=> $fld,'type'=>'text','class'=>' form-control','autocomplete'=>'off','style'=>'']); !!}
															@include('elements.field_error')
														</div>
													</div>
											</fieldset>
										</div>
										<br>
				
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right show_confirm" type="button" name="action"> Save
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
	<script>
		$('.show_confirm').on('click', function (event) {
		event.preventDefault();
		 var inputLen = $("#aicenter_remark").val().length;
		 var inputLens = $("#course").val().length;
			if (inputLens == 0){
				swal({
				title: 'Rejection documents selection is required',
				icon: 'error',
				showConfirmButton: false,
				timer: 10000
				});
			}
			else if (inputLen == 0){
				swal({
				title: 'Rejection remark is required',
				icon: 'error',
				showConfirmButton: false,
				timer: 10000
				});
			}else{
				msg = "Are you sure you want to submit?";
				swal({
				title: 'Are you sure?',
				text: msg,
				icon: 'info',
				buttons: ["Cancel", "Yes!"],
				}).then(function(value) {
				if (value) {
				$("#Supplementary").submit();
				}
			});
		}
	});
	</script>
@endsection
<style>
.select2-container--default .select2-results > .select2-results__options{
	max-width:1000px;
}
.select2-selection__rendered{
	max-width:800px;
}
</style>
 