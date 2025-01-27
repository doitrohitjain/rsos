@extends('layouts.default')
@section('content')
	@include('elements.dashboard_ui_notifications')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									<h4 class="card-title">{{ $page_title; }} </h4>
									{{ Form::open(['route' => [request()->route()->getAction()['as']]]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									<div class="row">
										<fieldset>
											<legend>
												<span style="font-weight: bold;color:#000000;">Fresh Student Summary(ताजा छात्र सारांश)</span>
											</legend>
											<div class="row">
												<div class="input-field col s12 l4 m4">
													@php 
													$form_admission_academicyear_id = Config::get('global.form_admission_academicyear_id');
													$lbl='Exam Year'; $fld='exam_year'; 
													@endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,@$form_admission_academicyear_id,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												<div class="input-field col s12 l4 m4">
													@php $lbl='Course'; $fld='course'; @endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,Null,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												<div class="input-field col s12 l4 m4">
													@php $lbl='Stream'; $fld='stream'; @endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,Null,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
											</div>
											<div class="row">
												<div class="input-field col s12 l4 m4">
													@php $lbl='Lock Sumbitted'; $fld='locksumbitted'; @endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,Null,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												<div class="input-field col s12 l4 m4">
													@php $lbl='Is Eligible'; $fld='is_eligible'; @endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,Null,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												<div class="input-field col s12 l4 m4">
													@php $lbl='Is Only Issue Showing'; $fld='isonlyissue'; @endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,Null,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												
												<div class="input-field col s12 l4 m4">
													@php $lbl='Start Limit' . ''; $fld='startlimitinput'; @endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,'1000',['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
												<div class="input-field col s12 l4 m4">
													@php $lbl='End Limit' . ''; $fld='endlimitinput'; @endphp
													<h8>{!! Form::label($fld, $lbl,['class' => 'small_lable']) !!}</h8>
													{!!Form::text($fld,'2000',['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
													@include('elements.field_error')
												</div>
											</div>
										</fieldset>
									</div>
										<br>
				
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right show_confirm" type="submit" name="action"> Submit
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
<style>
.small_lable {
    font-weight: 600;
    color: black !important;
}
</style>