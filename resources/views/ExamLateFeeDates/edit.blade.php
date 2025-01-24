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
							<h6><a href="{{ route('examdateindex') }}" class="btn btn-xs btn-info right">back</a></h6>
							<h6>{{ $page_title; }}<h6>
							</div>
							</div>
							<div id="Form-advance" class="card card card-default scrollspy">
								<div class="card-content">
									{{ Form::open(['route' => ['examdateedit', $id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
									
									<div class="row">
										<div class="input-field col m6 s12">
											@php $lbl='Stream:'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
											<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
											{!! Form::select($fld,$stream_arr, @$master->$fld, ['class' => 'form-control stream select2 select2a browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
											{{-- {!!  Form::label($fld, $lbl) !!} --}}
											@include('elements.field_error')
										</div>
										<div class="input-field col m6 s12">
											@php $lbl='Gender:'; $placeholder = "Select ". $lbl; $fld='gender_id'; @endphp
											<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
											{!! Form::select($fld,$gender_arr, @$master->$fld, ['class' => 'form-control gender_id select2 select2a browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
											{{-- {!!  Form::label($fld, $lbl) !!} --}}
											@include('elements.field_error')
										</div>
									</div>
									
									<div class="row">
						               <div class="input-field col s6">
											@php $lbl='From Date'; $fld='from_date'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@date("M d, Y",strtotime($master->$fld)),['class'=>'form-control from_date','autocomplete'=>'off','readonly'=>'readonly']); !!}
											@include('elements.field_error')
										</div>
										<div class="input-field col s6">
											@php $lbl='To Date'; $fld='to_date'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}</h8>
											{!!Form::text($fld,@date("M d, Y",strtotime($master->$fld)),['type'=>'text','class'=>'form-control to_date','autocomplete'=>'off','readonly'=>'readonly']); !!}
											@include('elements.field_error')
										</div>
									</div>
				
									<div class="row">
										<div class="input-field col s6">
											@php $lbl='Late Fee'; $fld='late_fee'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control num','maxlength'=>6,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
										</div>
										<div class="input-field col m6 s12">
											@php $lbl='IS For Supplementary'; $placeholder = "Select ". $lbl; $fld='is_supplementary'; @endphp
											<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
											{!! Form::select($fld,$yes_no, @$master->$fld, ['class' => 'form-control stream select2 select2a browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
											{{-- {!!  Form::label($fld, $lbl) !!} --}}
											@include('elements.field_error')
										</div>
									</div>
									<div class="row"> 
										<div class="input-field col s6">
											@php $lbl='Allow Extra Days For Locked'; $fld='latefee_extra_days'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php //echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control num','maxlength'=>6,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
										</div>
										<div class="input-field col s6">
											@php $lbl='Ordering'; $fld='ordering'; @endphp
											<h8>{!!Form::label($fld, $lbl) !!}@php //echo Config::get('global.starMark'); @endphp</h8>
											{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'form-control num','maxlength'=>6,'autocomplete'=>'off']); !!}
											@include('elements.field_error')
										</div>
									</div>
				
				
									<div class="row">
										<div class="col m10 s12 mb-3">
											<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Update
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
	<script src="{!! asset('public/app-assets/js/bladejs/late_fee_dates.js') !!}"></script>
@endsection 
 

