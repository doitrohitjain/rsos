@extends('layouts.default')
@section('content')
	<div id="main">
	<div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="seaction">
                        <h6><span style='color:red;'><b></b></span></h6>
                        <div class="card">
                            <div class="card-content">
                            <h6><a href="{{route('all_listing')}}" class="btn btn-xs btn-info right">Back</a></h6><h6></h6>  
                          
                                <h6></h6>  
                                <h6><b>{{$page_title}}</b></h6>
                        
                           </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
								
									<!--<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										<strong>
										@if($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										 </strong>
									</span>-->
									
									<h4 class="card-title">{{ $page_title; }} </h4>
									
									<table>
										<tr>
											<td>Exam Center Code 10th</td>
											<td>@php echo @$examCenterData->ecenter10; @endphp</td>
											<td>Exam Center Code 12th</td>
											<td>@php echo @$examCenterData->ecenter12; @endphp</td>
										</tr>
										<tr>
											<td>AI Center Code</td>
											<td>@php echo @$examCenterData->ai_code; @endphp</td>
											<td>Student Capacity</td>
											<td>@php echo @$examCenterData->capacity; @endphp</td>
										</tr>
									</table>
							
									<div class="row form-center">
										@include('elements.ajax_validation_block') 
										{{ Form::open(['route' => [request()->route()->getAction()['as'], $e_exam_center_id], 'id' => $model]) }}
										{!! Form::token() !!}
										{!! method_field('post') !!}
										{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
										<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
										<input type="hidden" name='id' value='@php echo $e_exam_center_id; @endphp'>
										<input type="hidden" name='stream' value='1' id='stream'>
										
										<div class="row">
											<div class="input-field col m12 s12">
												@php $lbl='AI Center'; $placeholder = "Select ". $lbl; $fld='ai_code'; 
												@endphp
												<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
												{!! Form::select($fld,$aiCenterList, @$master->$fld, ['class' => 'ai_code form-control select2 select2a browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
												@include('elements.field_error')
											</div>
											<div class="input-field col s12">
												@php $lbl='Supplementary-10th (Auto field according to AI Center)'; $fld='student_supp_10'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'supp-student-10-input form-control num','maxlength'=>6,'minLength'=>1,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
										</div>
										
										<div class="row">
											<div class="input-field col s12">
												@php $lbl='Supplementary-12th (Auto field according to AI Center)'; $fld='student_supp_12'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'supp-student-12-input form-control num','maxlength'=>6,'minLength'=>1,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
										</div>
										
										<div class="row">
											<div class="input-field col s12">
												@php $lbl='Stream-'.$stream.'-10th'; $fld='student_strem'.$stream.'_10'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'student-10-input form-control num','maxlength'=>6,'minLength'=>1,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
										</div>
										
										<div class="row">
											<div class="input-field col s12">
												@php $lbl='Stream-'.$stream.'-12th'; $fld='student_strem'.$stream.'_12'; @endphp
												<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
												{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'student-12-input form-control num','maxlength'=>6,'minLength'=>1,'autocomplete'=>'off']); !!}
												@include('elements.field_error')
											</div>
										</div>
										
										@php $total=0; @endphp
										<div class="row">
											<table>
												<tr>
													<td>Supplementary-10th</td>
													<td><span class="supp-student-10"></span></td>
												</tr>
												<tr>
													<td>Supplementary-12th</td>
													<td><span class="supp-student-12"></span></td>
												</tr>
												<tr>
													<td>Stream@php echo $stream; @endphp_10th</td>
													<td><span class="student-10"></span></td>
												</tr>
												<tr>
													<td>Stream@php echo $stream; @endphp_12th</td>
													<td><span class="student-12"></span></td>
												</tr>
												<tr>
													<td>Total</td>
													<td><span class="student-total-10-12"></span></td>
												</tr>
											</table>
										</div>
										
										</br>
										<div class="row">
											<div class="col m8 s7 mb-3">
												<button class="btn cyan waves-effect waves-light right  border-round  gradient-45deg-deep-orange-orange" type="reset">Reset
												</button>
											</div>
											<div class="col m4 s5 mb-3">
												<button class="btn cyan waves-effect waves-light right  border-round" type="submit" name="action"> Submit & Next
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
	
<style>
.form-center { width: 60%; margin-left:20% !important; }
</style>

@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/examcenter_aicenter_mapping_stream1.js') !!}"></script> 
@endsection 


 

