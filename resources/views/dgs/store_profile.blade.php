@extends('layouts.default')
@section('content')
<style>
	.frees {
		pointer-events: none;
	}
</style>
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="">{{ $title }}</a></li>
						</ol>
					</div>
				</div>
			</div>
        </div>
		<div class="col s12">
            <div class="container">
                <div class="seaction">
					<div class="card">
						<div class="card-content">
							<h6>
								<a href="{{route('create_profile')}}" class="btn btn-xs btn-info right gradient-45deg-deep-orange-orange">
									Back
								</a>
							</h6>
							<h6>&nbsp;</h6>
						</div>
					</div>
                    <div class="col s12 m12 l12">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content"> 
								@include('elements.ajax_validation_block')
								{{ Form::open(['url'=>url()->current(),'method'=>'POST','id'=>'CreateDgsStudents'])}}
									{!! Form::token() !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<input type="hidden" name='ai_code' value="{{$ai_code}}" id='ajaxRequest'>
									<table>
										<thead>
											<tr>
												<th width="10%" style="align:center;">क्र.सं.(Sr.No.)</th>
												<th width="48%" style="align:center;">छात्र का नाम(Student's Name)</th>
												<th width="48%" style="align:center;">जन्म की तारीख (Date of Birth)(M D, YYYY)</th>
											</tr>
										</thead>
										<tbody>
											<div class="row">
												<?php	  
													for(@$i=1;$i<=$StudentCount;$i++){
												?>
													<tr>
														<td width="4%">{{@$i}}</td>
														<td width="48%">
															<div class=" m6 s12">
																@php $lbl='छात्र का नाम(Student\'s Name)'; $fld='name[]'; @endphp
																<div class="input-field">	
																	{!!Form::text($fld,old($fld),['type'=>'text','class'=>'examiner_name textinput uppercase form-control ','autocomplete'=>'off','id'=>'name_'."$i" ,'placeholder'=>'छात्र का नाम(Enter Student\'s Name)']); !!}
																@include('elements.field_error')
																</div>
															</div>
														</td>
														<td width="48%">
															<div class=" m7 s12">
																@php $lbl='जन्म की तारीख (Date of Birth)(M D, YYYY)'; $placeholder = "Select ". $lbl; $fld='dob[]'; @endphp
																
																<div class="input-field">
																	{!!Form::text($fld,@$dobFormat,['class'=>'dob form-control textinput datepicker my_date_picker','autocomplete'=>'off','id'=>'dob_'."$i",'placeholder' => $lbl]); !!}
																	@include('elements.field_error')	
																</div>
															</div>	
														</td>
													</tr>
												<?php } ?>	
											</div>	
										</tbody>
									</table>
									<br>
									
								
									<div class="row">
									<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right gradient-45deg-green-teal" type="submit" name="action">submit 
												<i class="material-icons right"></i>
											</button>
										</div>
										<!--<div class="col m2 s12 mb-3">
											<button class="btn cyan waves-effect waves-light  gradient-45deg-purple-deep-orange">Add More 
												<i class="material-icons right"></i>
											
											</button>
										</div>-->
										<div class="col m2 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right" type="reset">
												<i class="material-icons right">clear</i>Reset
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
<script src="{!! asset('public/app-assets/js/bladejs/dgs_store_details.js') !!}"></script> 
@endsection 
