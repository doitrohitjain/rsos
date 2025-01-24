@extends('layouts.default')
@section('content')
<div id="main">
    <div class="row">
		<div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="seaction">
					<div class="card">
 
                            <div class="card-content">
							
							<h6><span><b>{{$page_title}}</b></span>
                            <a href="{{ route('student_applications') }}" class="btn btn-xs btn-info right">BacK</a>  </h6>
                           </div>
						  
                            
                        </div>
                    </div>
                </div>
            </div>
    
				
<!-- Form Advance -->
                <div class="col s12 m13 l12">
					<div id="Form-advance" class="card card card-default scrollspy">
						<div class="card-content">						
							<h4 class="card-title">{{ @$page_title; }} </h4>
							<br>
							@include('elements.ajax_validation_block')
							{{ Form::open(['url'=>url()->current(),'id'=>'updatessoform']) }}
							{!! method_field('PUT') !!}
								<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
								<input type="hidden" name='student_id' value="{{$studentdata->id}}" id='ajaxRequest'>
								<div class="row">
								<div class="col m3 s11">
										@php 
										    $lbl="नामांकन(Enrollment)"; $fld='enrollment'; 
										@endphp 
										<span class="small_lable">@php echo $lbl ; @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,$studentdata->$fld,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl,'readonly'=>'readonly']); !!}
											@include('elements.field_error')	
										</div>
									</div>
									<div class="col m3 s11">
									{!!Form::hidden('course',$studentdata->course,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
									{!!Form::hidden('exam_month',$studentdata->exam_month,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
										@php 
										    $lbl="नाम (Name)"; $fld='name'; 
										@endphp 
										<span class="small_lable">@php echo $lbl; @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,$studentdata->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'readonly'=>'readonly']); !!}
											@include('elements.field_error')	
										</div>
									</div>

									<div class="col m3 s11">
										@php 
											$lbl="पिता का नाम (Father's Name)"; $lbl; $fld='father_name'; 
										@endphp
										<span class="small_lable">@php echo $lbl ; @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'readonly'=>'readonly']); !!}
											@include('elements.field_error')	
										</div>
									</div>

									<div class="col m3 s11">
										@php 
											$lbl="माँ का नाम (Mother's Name)"; $lbl; $fld='mother_name';
										@endphp
										<span class="small_lable">@php echo $lbl ; @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,@$studentdata->$fld,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl,'readonly'=>'readonly']); !!}
											@include('elements.field_error')	
										</div>
								    </div>
								</div>
								<div class='row'>
									<div class="col m3 s11">
										@php 
											$lbl='जन्म की तारीख (Date of Birth)(MM-DD-YYYY)'; $placeholder = "Select ". $lbl; $fld='dob'; 
										@endphp
										<span class="small_lable">@php echo $lbl; @endphp </span>
										<div class="input-field"> 
											@if(!empty(@$studentdata->$fld))
											@php 
												$dobFormat = @$studentdata->$fld;
												$dobFormat = date("M d, Y",strtotime(@$dobFormat));
											@endphp
											@endif
											{!!Form::text($fld,@$dobFormat,['class'=>'dob form-control ','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,'readonly'=>'readonly']); !!}
											@include('elements.field_error')	
										</div>
									</div>
									
									<div class="col m3 s11">
										@php 
										    $lbl="पाठ्यक्रम (Course)"; $fld='course'; 
										@endphp 
										<span class="small_lable">@php echo $lbl ; @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,$studentdata->$fld,['type'=>'text','class'=>'form-control ','autocomplete'=>'off','placeholder' => $lbl,'readonly'=>'readonly']); !!}
											@include('elements.field_error')	
										</div>
									</div>
									<div class="col m3 s11">
										@php 
										    $lbl="एसएसओ(SSO)"; $fld='ssoid'; 
										@endphp 
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,$studentdata->$fld,['type'=>'text','class'=>'form-control sso_id ssoinput','autocomplete'=>'off','placeholder' => $lbl]); !!}
											@include('elements.field_error')	
										</div>
									</div>
                                </div>
								<div class="row">
									<div class="col m10 s12 mb-3">
										<button class="btn cyan waves-effect waves-light right ssocheck" style="background: linear-gradient(45deg,#303f9f,#7b1fa2); type="submit" name="action">Update
										</button>
									</div>
									<div class="col m2 s10 mb-3">
										 <a href="{{ route('student_applications') }}" class="btn btn-xs btn-info right">Cancel</a>
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


@endsection
@section('customjs')
<script src="{!! asset('public/app-assets/js/bladejs/update_sso_details.js') !!}"></script> 
@endsection