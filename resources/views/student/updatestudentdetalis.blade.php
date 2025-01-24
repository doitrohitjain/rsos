@extends('layouts.default')
@section('content')
<div id="main">
    <div class="row">
		<div class="col s12">
			<div class="container">
				<div class="col s12 m12 l12">
					<div class="card"> 
						{{-- @include('elements.student_top_navigation') --}}
					</div>
			    </div>
<!-- Form Advance -->
                <div class="col s12 m13 l12">
					<div id="Form-advance" class="card card card-default scrollspy">
						<div class="card-content">						
							<h4 class="card-title">{{ $page_title; }} </h4>
							@if(@$Fresh_Student_Verificaiton_conditions)
								 
							@else
								<div class="col m12 s12 mb-1">
									<span class="badge cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style="float:left !important">
										Note: The photograph and signature should of minimum 10 kb and maximum 50 kb size and file format( jpeg / png / gif). <span class="starmark" style="color:red;"> *</span>
									</span>
								</div>
								@include('elements.image_input')
								<br>
							@endif
							
							@include('elements.ajax_validation_block')
							{{ Form::open(['url'=>url()->current(),'id'=>'updatedetailsform']) }}
							{!! method_field('PUT') !!}
								<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
								<input type="hidden" name='photograph' value='{{@$master->photograph}}' >
								<input type="hidden" name='signature' value='{{@$master->signature}}' >
								<div class="row">
									<div class="col m3 s11">
									{!!Form::hidden('course',$studentdata->course,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
										@php 
										    $lbl="नाम (Name)"; $fld='name'; 
										@endphp 
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,$studentdata->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'required'=>true]); !!}
											@include('elements.field_error')	
										</div>
									</div>

									<div class="col m3 s11">
										@php 
											$lbl="पिता का नाम (Father's Name)"; $lbl; $fld='father_name'; 
										@endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'required']); !!}
											@include('elements.field_error')	
										</div>
									</div>

									<div class="col m3 s11">
										@php 
											$lbl="माँ का नाम (Mother's Name)"; $lbl; $fld='mother_name';
										@endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'required']); !!}
											@include('elements.field_error')	
										</div>
								    </div>
									<div class="col m3 s11">
										@php 
											$lbl='मोबाइल (Mobile)'; $lbl; $fld='mobile'; 
										@endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','maxlength' => 10,'placeholder' => $lbl,'required']); !!}
											@include('elements.field_error')	
										</div>
								    </div>
								</div>
								<div class='row'>
									<div class="col m4 s11">
										@php 
											$lbl='जन्म की तारीख (Date of Birth)(MM-DD-YYYY)'; $placeholder = "Select ". $lbl; $fld='dob'; 
										@endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field"> 
											@if(!empty(@$studentdata->$fld))
											@php 
												$dobFormat = @$studentdata->$fld;
												$dobFormat = date("M d, Y",strtotime(@$dobFormat));
											@endphp
											@endif
											{!!Form::text($fld,@$dobFormat,['class'=>'dob form-control datepicker','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,'required']); !!}
											@include('elements.field_error')	
										</div>
									</div>
									<div class="col m4 s11">
										@php 
											$lbl='मध्यम (Medium)'; $lbl; $fld='medium'; 
											$placeholder = "Select ". $lbl; 
										@endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!! Form::select($fld,@$medium,@$applicationdata->$fld, ['id' => $fld,'class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}	
										</div>
								    </div>
									
									
                                </div>
								<div class="row" >
								<div class="col m4 s12">
										@php $lbl='पिछली योग्यता (Previous Qualification)'; $placeholder = "Select ". $lbl; $fld='pre_qualification'; @endphp
										@if($studentdata->course == 12)
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										@elseif($studentdata->course == 10)
										<span class="small_lable">@php echo $lbl; @endphp </span>
										@endif
										<div class="input-field">
										{!! Form::select($fld,@$pre_qualifi,@$applicationdata->$fld, ['id' => $fld,'class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}
										
										@include('elements.field_error')	
										</div>
									</div>
									@php 
				$fld='course'; 
				$janDivShowHide = "";
				if($studentdata->$fld != 12 ){
					$janDivShowHide = "display: none;";
				}
				@endphp
				<div class="col m4 s12" id='source_other1' style="{{$janDivShowHide}}">
				@php $lbl='10वीं उत्तीर्ण करने का वर्ष (Year of Passing 10th):'; $placeholder = "Select ". $lbl; $fld='year_pass'; @endphp
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
				{!! Form::select($fld,@$rsos_years,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align year_pass','placeholder' => $placeholder]) !!}
				@include('elements.field_error')	
				</div>
				</div>

				<div class="col m4 s12" id='source_other' style="{{$janDivShowHide}}">
				@php $lbl='10वीं बोर्ड (Board 10th)'; $placeholder = "Select ". $lbl; $fld='board'; @endphp
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">
				{!! Form::select($fld,@$getBoardList,@$applicationdata->$fld, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder,'id'=>'board']) !!}
				@include('elements.field_error')	
				</div>
				</div>
								</div>
								 <div class="row"></br>
				                  <div class="step-actions right">
										  <div class="row">
											<div class="col m4 s12 mb-1">
											  <button class="green btn submitBtnCls submitconfirms" type="submit" name="action">
											  Submit 
											  </button>
											</div>
											@php
											    $route=route('searchstudentdetail');
												if(@$last_action_name){
													$route=$last_action_name;
												}
												
                                                if(@$role_id == @$aoRole){	
												    $route=route($last_action_name,Crypt::encrypt($studentdata->id));
												}
											@endphp
											<div class="col m4 s12 mb-2">
											 <a href="{{$route}}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text">Back</a>											  
											</div>
											<div class="col m1 s12 mb-3">
											<button class="waves-effect waves dark btn btn-primary next-step" type="reset">Reset
										</button>
											  
											</div>
										  </div>
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
<script>
	 
	var course = '<?php echo $course; ?>';
	
</script>

@endsection
	
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/updatestudentdetailsprint.js') !!}"></script> 
@endsection 