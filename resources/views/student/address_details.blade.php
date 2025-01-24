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
						<div class="">
							<div class="col s12 m12 l12">
								<div id="Form-advance" class="card card card-default scrollspy address-page">
									<div class="card-content row">
										<h4 class="card-title">{{ $page_title; }} </h4>
										@php 
											//dd($master);
										@endphp
										@include('elements.ajax_validation_block') 
										{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model,'autocomplete'=>'off']) }}
										{!! Form::token() !!}
										{!! method_field('PUT') !!}
										{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
										<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
										<fieldset>
											<legend>स्थायी पता (Permanent Address)</legend>
											<div id="permanent_address">
												<div class="row">
													<div class="input-field col m6 s12">
														@php $lbl='पता पंक्ति 1 (Address Line 1)'; $fld='address1'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!!Form::text($fld,@$master->$fld,['class'=>'checkBrowserIssue form-control uppercase ','autocomplete'=>'off','id'=>$fld,'required'=>'required']); !!}
														@include('elements.field_error')
													</div>
													<div class="input-field col m6 s12">
														@php $lbl='पता पंक्ति 2 (Address Line 2)'; $fld='address2'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}</h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'checkBrowserIssue2 form-control uppercase ','maxlength'=>70,'minLength'=>2,'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
													</div>
												</div>
												
												<div class="row">
													<div class="input-field col m6 s12">
														@php $lbl='पता पंक्ति 3 (Address Line 3)'; $fld='address3'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}</h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'checkBrowserIssue3  uppercase form-control','maxlength'=>70,'minLength'=>2,'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
													</div>
													<div class="input-field col m6 s12">
														@php $lbl='चयन राज्य (State)'; $placeholder = "Select ". $lbl; $fld='state_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$state_list, @$master->$fld, ['id' => $fld,'class' => 'form-control state_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														{{-- {!!  Form::label($fld, $lbl) !!} --}}
														@include('elements.field_error')
													</div>
												</div>
							
												<div class="row">
													<div class="input-field col m6 s12">
														@php $lbl='चयन जिला (District):'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$district_list, @$master->$fld, ['id' => $fld,'class' => 'form-control district_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														{{-- {!!  Form::label($fld, $lbl) !!} --}}
														@include('elements.field_error')
													</div>
													
													<div class="input-field col m6 s12"> 
														@php  if(!empty($master) && $master->state_id==6){  @endphp
														<span class="tehsil_id_section" >
														@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='tehsil_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$tehsil_list, @$master->$fld, ['id' => $fld,'class' => 'form-control tehsil_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														@include('elements.field_error')
														</span>
														
														<span class="tehsil_name_section"  style="display:none" >
														@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='tehsil_name'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','id' => $fld,'class'=>'form-control  uppercase tehsil_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
														</span>
														
														@php  } else if(!empty($master) && $master->state_id !=6){ @endphp
														<span class="tehsil_id_section" style="display:none" >
														@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='tehsil_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$tehsil_list, @$master->$fld, ['class' => 'form-control tehsil_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														@include('elements.field_error')
														</span>
														
														<span class="tehsil_name_section" >
														@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='tehsil_name'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control  uppercase tehsil_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
														</span>
														
														@php  } else { @endphp
														<span class="tehsil_id_section" >
														@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='tehsil_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$tehsil_list, @$master->$fld, ['class' => 'form-control tehsil_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														@include('elements.field_error')
														</span>
														
														<span class="tehsil_name_section"  style="display:none" >
														@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='tehsil_name'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control  uppercase tehsil_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
														</span>
														@php } @endphp
													</div>
													
												</div>
												<div class="row">
													<div class="input-field col m6 s12">
														@php  if(!empty($master) && $master->state_id==6){  @endphp
														<span class="block_id_section" >
														@php $lbl='चयन ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='block_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$block_list,@$master->$fld, ['class' => 'form-control block_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														@include('elements.field_error')
														</span>
														
														<span class="block_name_section" style="display:none" >
														@php $lbl='चयन  ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='block_name'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control  uppercase block_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
														</span>
														
														@php } else if(!empty($master) && $master->state_id !=6){  @endphp
														<span class="block_id_section" style="display:none" >
														@php $lbl='चयन ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='block_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$block_list, @$master->$fld, ['class' => 'form-control block_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														@include('elements.field_error')
														</span>
														
														<span class="block_name_section" >
														@php $lbl='चयन  ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='block_name'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control block_name uppercase ','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
														</span>
														
														@php  } else {  @endphp
														<span class="block_id_section" >
														@php $lbl='चयन ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='block_id'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!! Form::select($fld,$block_list, @$master->$fld, ['class' => 'form-control block_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','id'=>$fld]) !!}
														@include('elements.field_error')
														</span>
														
														<span class="block_name_section" style="display:none" >
														@php $lbl='चयन  ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='block_name'; @endphp
														<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control uppercase  block_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
														</span>
														@php  }  @endphp
														
													</div>
													
													<div class="input-field col m6 s12">
														@php $lbl='चयन शहर/गाँव (City/Village)'; $fld='city_name'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'txtOnly form-control uppercase ','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
													</div>
												</div>
												<div class="row">
													<div class="input-field col m6 s12">
														@php $lbl='पिन कोड (Pincode)'; $fld='pincode'; @endphp
														<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
														{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control  uppercase num','maxlength'=>6,'minLength'=>6,'autocomplete'=>'off','id'=>$fld]); !!}
														@include('elements.field_error')
													</div>
												</div> 
												
											</div>
										</fieldset>
										<div id="current_address">
											@include('elements.current_address_details')
										</div>
										<br>
										<div class="row">
											<div class="col m10 s12 mb-3">
												<button class="btn cyan waves-effect waves-light right btn_disable " type="submit" name="action"> Save & Continue
												</button>
											</div>
											<div class="col m2 s10 mb-3">
											  <a href="{{$route}}" class="btn cyan waves-effect waves-light right">Reset</a> 
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
@section('customjs')
	<script>
		var master = '<?php if(isset($master)){ echo json_encode(@$master); }?>';
	</script>
	<script src="{!! asset('public/app-assets/js/bladejs/current_address_details.js') !!}"></script> 
	<script src="{!! asset('public/app-assets/js/bladejs/address_details.js') !!}"></script>
@endsection 
 

