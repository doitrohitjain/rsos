@extends('layouts.default')
@section('content')
<style>
.editMode {
  font-size: 100%;
  color: green;
  border-style: solid;
}
</style>
<?php use App\Helper\CustomHelper; ?> 
	<div id="main">
		<div class="row">
			<div class="col s12">
				@php $genAdmSucjectCountFour = false; @endphp
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.reval_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div class="col s12 m12 l12" style="background-color: #4f505245;'">
								<div id="Form-advance" class="card card card-default scrollspy">
									<div class="card-content"> 
										@php 
											$attentionMsg = null;
											$attentionMsg = "कृपया संबंधित विषय के चेकबॉक्स को चेक करें कि आप किस विषय का पुनर्मूल्यांकन करना चाहते हैं। (Please check the checkbox respective subject which subject you want to reval.)";
										@endphp
										
										
										<h4 class="card-title">{{ $page_title; }}</h4>
										<div class="col x25 m12 s12"> 
										<div class="input-field col s12">
											<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
												<strong> 
													@if($errors->any()) 
														@foreach ($errors->all() as $error)
															<div>{{$error}}</div>
														@endforeach
													@endif
												</strong>
											</span>
										</div> 
										<fieldset>
											<legend><h5>व्यक्तिगत विवरण(Personal details)</h5></legend>
														 
										<table class="table"> 
											
											
											<tr>
												<td style="font-size:16px;font-weight: bold;">नामांकन संख्या (Enrollment Number) </td>
												<td style="color: green;font-size:20px;font-size:16px;font-weight: bold;"">{{ @$master->enrollment  }}</td>
											</tr>
											<tr>
												<td width="25%" style="font-size:16px;font-weight: bold;"> पूरा नाम (Full Name) </td>
												<td width="25%">{{ @$master->name}}</td> 
												<td width="25%" style="font-size:16px;font-weight: bold;">पिता का नाम (Father's Name) </td>
												<td width="25%">{{ @$master->father_name  }}</td>
											</tr>
											   <td style="font-size:16px;font-weight: bold;">मोबाइल नंबर (Mobile No.) </td>
												<td><div contentEditable="true" class="edit num" name="mobile" id="<?php echo @$master->id; ?>" data-old_val="<?php echo @$master->mobile;?>" data-val="<?php echo $master->mobile;?>"> <?php echo $master->mobile; ?> 
											    </div> 
												</td>
											
												<td style="font-size:16px;font-weight: bold;">पिन कोड (Pincode) </td>
												<td>{{ @$master->Address->pincode  }}</td>
											</tr>
											<tr>
												<td style="font-size:16px;font-weight: bold;">प्रवेश प्रकार (Admission Type) </td>
												<td>
													@if(@$master->adm_type && @$adm_types[$master->adm_type])
														{{ @$adm_types[$master->adm_type]  }}
													@endif
												</td>
												<td style="font-size:16px;font-weight: bold;">एसएसओ(ssoid) </td>
												<td>{{ @$master->ssoid }}
													</td> 
											</tr>
											@if(@$master->course == 12)
											<tr>
												<td style="font-size:16px;font-weight: bold;">पूर्व योग्यता (Pre Qualification) </td>
												<td>
													{{@$pre_qualifi[$master->application->pre_qualification]  }}
													
												</td>
												<td style="font-size:16px;font-weight: bold;">वर्ष पास (Year Pass) </td>
												<td>{{ @$rsos_years[$master->application->year_pass]}}
													</td> 
											</tr>
											@endif
											
											 
										</table>
										
									</fieldset>
											
										</div>
									</div>
									
									<div class="card-content"> 
										<div class="col s12 m12 l12"> 

											{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
											{!! Form::token() !!}
											{!! method_field('PUT') !!}
											<input type="hidden" name='student_id' value='{{ @$estudent_id }}' id='student_id'>
											<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
												
												@if(@$ExamSubject)

												
												<fieldset>
													<legend>
														<span style="font-weight: bold;color:#000000;">पुनर्मूल्यांकन आवेदन का प्रकार (Reval Application Type)</span>
													</legend>
													@php $revalTypeMsg = "प्रत्येक विषय के पुनर्मूल्यांकन का शुल्क ". @$reval_per_subject_fee[1] . " है, जबकि प्रत्येक विषय के आरटीआई शुल्क ". @$reval_per_subject_fee[2] . " है।(The fee for " . $reval_types[1]." per subject is ". @$reval_per_subject_fee[1] . ", while the fee for " . $reval_types[2]." per subject is ". @$reval_per_subject_fee[2] .".)"; @endphp
														@if(@$revalTypeMsg)
															<span style="font-size:16px;color:blue;">
																<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="14" alt="RSOS"/>
																{{ $revalTypeMsg }} </span>
														@endif
													<div class="row">
														
														<div class="input-field col  s12 m12 l12">
															@php $lbl='पुनर्मूल्यांकन आवेदन का प्रकार (Reval Application Type)'; $fld='reval_type'; 
															@endphp
															<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
															{!! Form::select($fld,$reval_types,@$alredyPresent->$fld, ['id' => $fld,'class' => 'form-control state_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
															@include('elements.field_error')
														</div>
												</fieldset>
												

												<fieldset>
													<legend><h5>विषयों का विवरण (Subjects details)</h5></legend>
													@if(@$attentionMsg)
														<span style="font-size:14px;color:blue;">
															<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="14" alt="RSOS"/>
															{{ $attentionMsg }} </span>
													@endif
											
													<div class="row">
														 
														<p> 
															@foreach($ExamSubject as $i => $value)
																<div class="col m4 s4 mb-4">
																	@php
																		$lbl='विषय '. (@$i+1) .' (Subject '. (@$i+1) .')'; 
																		$placeholder ='Select Any';  
																		$fld='subject_id[' . @$value->subject_id . ']';  
																	@endphp 
																	
																	<label>
																		<?php  
																			$subject_check_status = CustomHelper::revalSubjectIsCheckedStatus(@$student_id,@$value->subject_id);
																			
																			$check_attribute = '';
																			
																			if(@$subject_check_status){
																				$check_attribute = 'checked="checked"';
																			} 
																		?> 
																		<input {{ @$check_attribute; }}  name="{{ @$fld; }}" type="checkbox" value="{{ @$value->subject_id; }}">
																		<span>{{ @$subject_list[@$value->subject_id] }}</span>
																	</label>
																	@include('elements.field_error')
																</div>
															@endforeach
														</p>  
													</div>
												</fieldset>
												@endif 
											</div>
											<div class=""> 
												<div class="row">
													<div class="col m10 s12 mb-3">
														<br>
														<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Save & Continue
														</button>
													</div>
													<div class="col m2 s10 mb-3">
														<br>
														<button class="btn cyan waves-effect waves-light right" type="reset">Reset
														</button>
													</div>
												</div>
											</div>
											{{ Form::close() }} 
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
	<script src="{!! asset('public/app-assets/js/bladejs/reval/reval_subject_details.js') !!}"></script>
@endsection 
	

