@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card">
							<div class="card-content">
							<p class="caption mb-0"><h6>{{@$page_title}} <span style="margin-left: 85%;"><a href="{{ route('tabletableslisting') }}" class="btn btn-xs btn-info pull-right">Back</a></span>
							<h6></p>
							</div>
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									@include('elements.ajax_validation_block') 
									{{ Form::open(['route' => [request()->route()->getAction()['as']],'id' => $model]) }}
									{!! Form::token() !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<div class="row">
										<div class="col m6 s12">
										@php $lbl='पाठ्यक्रम (course)'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$course,null, ['class' => 'select2 browser-default form-control center-align course','placeholder' => $placeholder,'id'=>'course' ]) !!}

										@include('elements.field_error')
										</div>
										</div>
										<div class="col m6 s12">
										@php $lbl='विषय (Subjects)'; $placeholder = "Select ". $lbl; $fld='subjects'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$empty,null, ['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder ,'id'=>'subjectstype']) !!}

										@include('elements.field_error')
										</div>
										</div>
									</div></br>
									<div class="row">
										<div class="col m6 s12">
										@php $lbl='परीक्षा प्रारंभ समय (Exam Time Start)'; $placeholder = "Select ". $lbl; $fld='exam_time_start'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$exam_time_table_start_end_time1,null, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder]) !!}

										@include('elements.field_error')
										</div>
										</div>
									<div class="col m6 s12">
										@php $lbl='परीक्षा समाप्ति समय (Exam Time End)'; $placeholder = "Select ". $lbl; $fld='exam_time_end'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$exam_time_table_start_end_time,null, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder ]) !!}

										@include('elements.field_error')
										</div>
										</div>
										</div></br>
										<div class="row">
									<div class="col m6 s12">
									@php $lbl='परीक्षा तिथि(Exam Date)(DD/MM/YYYY)'; $fld='exam_date'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field"> 
									{!!Form::text($fld,null,['class'=>'form-control datepicker','autocomplete'=>'off']); !!}
									@include('elements.field_error')	
									</div>
									</div>

										<div class="col m6 s12">
										@php $lbl='स्ट्रीम (Steram)'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
										{!! Form::select($fld,@$stream_id,null, ['class' => 'select2 browser-default form-control center-align','placeholder' => $placeholder ]) !!}

										@include('elements.field_error')
										</div>
										</div>
									</div>
				           			<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right show_confirm" type="submit" name="action"> Save & Continue
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
	<script src="{!! asset('public/app-assets/js/bladejs/timetable_details.js') !!}"></script> 
@endsection 

 


