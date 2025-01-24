@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
						</div>
						<div class="">
							<div class="col s12 m12 l12">
								<div id="Form-advance" class="card card card-default scrollspy address-page">
									<div class="card-content row">
										<h4 class="card-title">{{ $page_title; }} </h4>
										{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'autocomplete'=>'off']) }}
										{!! Form::token() !!}
										{!! method_field('PUT') !!}
										<fieldset>
												<legend>एआईसेंटर विवरण(AiCentre Details)</legend>
												<div class="row">
													<div class="col m12 s12">
													@php $lbl='AiCentre Update'; $lbl2 = "AiCentre"; $placeholder = "Select ". $lbl2; $fld='ai_code'; @endphp
													<span class="small_lable">
														@php echo $lbl2.Config::get('global.starMark'); @endphp </span>
													<div class="input-field">
													{!! Form::select($fld,@$aiCenters,$studentdata->ai_code, ['class' => 'select2 browser-default form-control center-align','requried' => 'requried','placeholder' => $placeholder]) !!}

													@include('elements.field_error')	
													</div>
													</div>
													</div>
										 </fieldset><br>
										   <div class="row">
											<div class="col m10 s12 mb-3">
												<button class="btn cyan waves-effect waves-light right btn_disable " type="submit" name="action"> Save & Continue
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
	</div>
@endsection 
 
 

