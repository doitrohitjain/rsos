@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
						</ol>
					</div>
				</div>
			</div>
        </div> 
	
        <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<span class="invalid-feedback" role="alert" style="color:red;font-size:18px;">
										<strong>
										@if ($errors->any())
										 @foreach ($errors->all() as $error)
										 <div>{{$error}}</div>
										 @endforeach
										 @endif
										</strong>
									</span>
										
									<h6>{{ $title }}
									<a href="{{ url()->previous() }}" class="btn btn-xs btn-info right">Back</a>
									
									<h6>
									
									<h6>
										<span class="z-depth-2" style="color:red;font-size:16px;line-height: 1.6;">
											
										</span>
									</h6>
								</div>
							</div>
							
							<div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
							
                                <div id="html-view-validations">
                                    {!! Form::open(array('url' => url()->current(),'method'=>'POST','id' => $model)) !!}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									
                                    <div class="row">
                                        <div class="input-field col s6">
                                            @php $lbl='स्लॉट प्रारंभ समय (Slot Start Time)'; $fld='date_time_start'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
                                            <input type="datetime-local"  name="{{ $fld }}" value="" class="form-control" autocomplete="off" id="{{ $fld }}" placeholder="Enter ">
                                            <input type="hidden" name ="user_examiner_map_id" value="{{encrypt(@$user_examiner_map_id)}}"  >
											
                                            @include('elements.field_error')
                                        </div>
										<div class="input-field col s6">
                                            @php $lbl='स्लॉट समाप्त समय (Slot End Time)'; $fld='date_time_end'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											<input type="datetime-local"  name="{{ $fld }}" value="" class="form-control  "   autocomplete="off" id="{{ $fld }}" placeholder="Enter ">
                                            @include('elements.field_error')
                                        </div>
										<div class="input-field col s6">
                                            @php $lbl='स्लॉट अभ्यर्थी संख्या (Slot Student Count)'; $fld='batch_student_count'; @endphp
                                            <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											
                                            {!!Form::text($fld,null,['type'=>'text','class'=> $fld . 'form-control','autocomplete'=>'off']); !!}
                                            @include('elements.field_error')
                                        </div>
										</div>
										<div class="row">										
										<label>		
										<input type='checkbox' id="selectAll" name=''/><span>Select All</span>
										</label>
										</div>
										<table>
										<thead>
											<tr>
												<th>#</th>
												<th>Name</th>
												<th>Enrollment</th>
												<th>Select for Slot</th>
												
											</tr>
										</thead>
										<tbody>  
										@php 
										$i=1; 
										@endphp       
                                        									
										@foreach(@$master as $k=>$data)
										@if($data->student_practical_slot_id == null)
											   
													<tr>
													
														<td>{{ $i; }}</td>
														
														<td>{{ @$data->name }}</td>
														<td>{{ @$data->enrollment }}</td>
														<td>
															<label>
															<?php
															$is_check_class = '';
															if(@$data->practical_absent=='1'){
																//$is_check_class= 'checked="checked"';
															}
															?>
															<input type='checkbox' id="addinslot<?php echo $i; ?>   " name='data[]' class="practical_absent practical_absent_<?php echo $i; ?> check_absent_marks " <?php echo $is_check_class; ?> value="{{encrypt($data->id)}}"/><span></span>
															</label>
														</td>
														<!-- <td>
															<input type='text' id="finalPracticalMarks<?php echo $i; ?>" name='data[{{ $k }}][final_practical_marks]' class="final_practical_marks final_practical_marks_<?php echo $i; ?>  check_absent_marks" value="{{ ''; }}" <?php if(@$data->practical_absent=='1'){ echo "readonly"; } ?>>
															<input type='hidden' name='data[{{ $k }}][student_allotment_marks_id]' class='student_allotment_marks_id' value='{{ Crypt::encrypt($data->id) }}'>
														</td> -->
														
													</tr>
													@php  $i++; @endphp
													@endif
												
												@endforeach
												</tbody>
											</table>                                       
										
                                        <div class="row">
                                            <div class="col m10 s12 mb-3">
                                                <!--<button class="btn cyan waves-effect waves-light right" type="reset">
                                                <i class="material-icons right">clear</i>Reset
                                                </button>-->
												<a href="#" class="btn cyan waves-effect waves-light right">Reset</a>
                                            </div>
                                            <div class="col m2 s12 mb-3">
                                                <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                                                <i class="material-icons right">send</i>
                                                </button>
                                            </div>
                                        </div>
									</div>
                                    {!! Form::close() !!}  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
						</div>
					</div>
				</div>
			</div>
		</div>
		

		<div class="content-overlay"></div>
		
    </div>
</div> 
@endsection
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/createpracticalexaminerslot.js') !!}"></script> 
@endsection 


