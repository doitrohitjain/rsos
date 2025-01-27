@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.sessional_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div class="col s12 m12 l12" style="2background-color: #4f505245;'">
								<div id="Form-advance" class="card card card-default scrollspy">
									<div class="card-content">
										<h4 class="card-title">{{ $page_title; }}</h4>
										<div class="col x25 m12 s12"> @foreach($master as $secotionFld => $values) 
											@if(@$values['data'] && $values['data'] != null) 
												<div class="card">
													<div class="card-content invoice-print-area"> 
														@php echo $values['seciontLabel'];  @endphp 
														<div class="divider mb-3 mt-3"></div>
														<div class="row">
															<div class="col m12 s12">
																<table><tr> 
																	@php $counter = 0; @endphp 
																	@php if($values['data'] != null){ @endphp 
																	@foreach(@$values['data'] as $fld => $lbl) 
																	@php $showTr = false;
																	if($counter%2 === 0){ $showTr = true; } 
																	if($showTr){  echo "</tr><tr>"; } 
																	@endphp 
																	<td width="20%">@php echo $lbl['label']; @endphp </td>
																	<td width="5%"> @php echo " : "; @endphp </td>
																	<td width="20%"> @php echo $lbl['value']; @endphp </td>
																	@php $counter++; @endphp 
																	@endforeach 
																	@php } @endphp 
																	</tr>
																</table>
															</div>
														</div>
													</div>
													<div class="divider mb-3 mt-3"></div>
												</div>
											@endif 
											
											@endforeach 
											 
										</div>
									</div>
									<div class="row"></div>
									<div class="card-content"> 
										<div class="col s12 m12 l12">
											<div id="Form-advance" class="card card card-default scrollspy">
												<div class="card-content">

													@include('elements.ajax_validation_block') 
													{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
													{!! Form::token() !!}
													{!! method_field('PUT') !!}
													{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
													<fieldset class="scheduler-border col-md-12 col-xs-12 left">
														<legend class="scheduler-border fieldsetLable-newll">
															<label class="">Subject Details (विषय का विवरण)</label>
														</legend>
														<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
													    <div class="row"> 
															<div class="input-field col s12">
																<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
																	<strong> 
																		@if ($errors->any()) 
																			@foreach ($errors->all() as $error)
																				<div>{{$error}}</div>
																			@endforeach
																		@endif
																	</strong>
																</span>
															</div>  
														@php $k=0; 
														//dd($examSubjectsList);
														@endphp
														@foreach($examSubjectsList as $subject_id => $marks) 
															@if($k == 0)
															<div class="input-field2 col s12">
																<div class="col s12"> 
																	<b>
																		<div class="col s4">
																			Subject Name(Code)
																		</div>
																		<div class="col s3">
																			Max Marks  @php echo Config::get('global.starMark'); @endphp
																		</div> 
																		<div class="col s4">
																			Obtained Marks  @php echo Config::get('global.starMark'); @endphp
																		</div>
																		<div class="col s4">
																			Is Absent @php echo Config::get('global.starMark'); @endphp
																		</div>
																		
																	</b>
																</div>
															@endif
															<div class="input-field2 col s12">
																@php
																	$lbl=$subject_list[$subject_id]; 
																	$placeholder =$lbl;  
																	$fld='subject_id[' . $subject_id . ']'; 
																@endphp  
																<div class="col s10"> 
																	<div class="col s5">
																		<div class="input-field2">
																			<h8><strong>{!!Form::label($fld, $lbl) !!} @php echo Config::get('global.starMark'); @endphp</strong></h8>
																		</div>
																	</div> 
																	<div class="col s3">
																		<div class="input-field2">
																			<h8> <b> @php echo $maxMarks[$subject_id]; @endphp </b></h8>
																		</div>
																	</div>
																	<div class="col s4">
																		<div class="input-field2">
																			@php $value = $examSubjectsList[$subject_id]; @endphp 
																			{!!Form::text($fld,@$value,['class'=>'form-control num sessional_marks', 'data-min' => $minMarks[$subject_id],  'data-max' =>  $maxMarks[$subject_id], 'required' => 'required' ,'maxlength'=> '2', 'minLength'=> '1','autocomplete'=>'off']); !!}
																			@include('elements.field_error')
																		</div>
																	</div>
																</div>
															</div>
															@php $k++; @endphp
														@endforeach
													</div> 
												</fieldset>	
												<div class="row"></div>
												<br>	  
												 
												<div class="row">
													<div class="col m10 s12 mb-3">
														<button class="btn cyan waves-effect waves-light right show_confirm" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);" type="submit" name="action"> Save & Continue
														</button>
													</div>
													<div class="col m2 s10 mb-3">
														<button class="btn cyan waves-effect waves-light right gradient-45deg-deep-orange-orange" type="reset">Reset
														</button>
													</div>
												</div>
												{{ Form::close() }}
														
												</div> 
												<div class="row"></div>
												<br>	  
												 
											</div>
										</div>
									</div>
									<div class="row"></div>
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
	<script src="{!! asset('public/app-assets/js/bladejs/sessional/marks_details.js') !!}"></script> 
@endsection 
 


