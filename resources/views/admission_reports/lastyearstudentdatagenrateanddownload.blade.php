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
	<br>
	<br>
		@can('genrate_last_year_student_details')	
			<div id="tap-target" class="card card-tabs">
			<div class="card-content">
			<h4 class="header">&nbsp;<span style="color:Blue;">Generate Excel </span></h4>
				 {!! Form::open(array('route' => 'generate_last_years_student_subject_data_setting','method'=>'POST')) !!}
				<div class="row">
					<div class="col m3 s10">
					 @php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">
					{!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype','placeholder' => $placeholder,'required'=>'true']) !!}
					@include('elements.field_error')
				</div>
				</div>
				<div class="col m3 s12">
					 @php $lbl='Exam Year'; $placeholder = "Select ". $lbl; $fld='exam_year'; @endphp
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">
					{!! Form::select($fld,@$admission_sessions,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype ','placeholder' => $placeholder,'required'=>'true']) !!}
					@include('elements.field_error')
				</div>
				</div>
				
				<!--<div class="col l2 m3 s12">
					 @php $lbl='Exam Month'; $placeholder = "Select ". $lbl; $fld='exam_month'; @endphp
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">
					{!! Form::select($fld,@$exam_month,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype','placeholder' => $placeholder,'required'=>'true']) !!}
					@include('elements.field_error')
				</div>
				</div>-->
				
				
				<div class="col m3 s12">
					 @php $lbl='Offset Start'; $placeholder = "Select ". $lbl; $fld='offsetstart';
					 @endphp
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">
				   {!!Form::text($fld,$offsetstart,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','id'=>'sessionalmarks1','autocomplete'=>'off','required'=>'true']); !!}
					@include('elements.field_error')
				</div>
				</div><div class="col  m3 s12">
					 @php $lbl='Limit'; $placeholder = "Select ". $lbl; $fld='limit';
					 @endphp
					<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					<div class="input-field">
				   {!!Form::text($fld,$limit,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','id'=>'sessionalmarks1','autocomplete'=>'off','required'=>'true']); !!}
					@include('elements.field_error')
				</div>
				</div>
				
				
			  </div>
			  
			</div><br>
			<div class="row">
			   <div class="col m10 s12 mb-3">
				<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Generate
				</button>
			  </div>
			</div> 
			{{ Form::close() }}
		  </div>
			</div>
		@endcan
	<div id="tap-target" class="card card-tabs">
        <div class="card-content">
		<h4 class="header">&nbsp;<span style="color:green;">Download Excel </span></h4>
             {!! Form::open(array('route' => 'download_last_years_student_subject_data_setting','method'=>'POST')) !!}
            <div class="row">
                <div class="col  m3 s12">
                 @php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype','placeholder' => $placeholder,'required'=>'true']) !!}
                @include('elements.field_error')
            </div>
            </div>
			<div class="col  m3 s10">
                 @php $lbl='Exam Year'; $placeholder = "Select ". $lbl; $fld='exam_year'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$admission_sessions,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype ','placeholder' => $placeholder,'required'=>'true']) !!}
                @include('elements.field_error')
            </div>
            </div>
			
			<!--<div class="col l2 m4 s12">
                 @php $lbl='Exam Month'; $placeholder = "Select ". $lbl; $fld='exam_month'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$exam_month,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype','placeholder' => $placeholder,'required'=>'true']) !!}
                @include('elements.field_error')
            </div>
            </div>-->
			<div class="col  m3 s12">
                 @php $lbl='Offset Start'; $placeholder = "Select ". $lbl; $fld='offsetstart';
				 @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
               {!!Form::text($fld,$offsetstart,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','id'=>'sessionalmarks1','autocomplete'=>'off','required'=>'true']); !!}
                @include('elements.field_error')
            </div>
            </div>
			<div class="col  m3 s12">
                 @php $lbl='limit'; $placeholder = "Select ". $lbl; $fld='limit';
				 @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
               {!!Form::text($fld,$limit,['type'=>'text','class'=>'form-control num','class'=>'sessonialmarks','id'=>'sessionalmarks1','autocomplete'=>'off','required'=>'true']); !!}
                @include('elements.field_error')
            </div>
            </div>    
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-2">
            <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Download
            </button>
          </div>
          <div class="col m2 s12 mb-2">
            <!--<a href="{{route('aicodewisesubjectsdatastudents')}}" class="btn cyan waves-effect waves-light right">Reset </a>-->
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
  
  
      </div>
	</div>
</div>

@endsection


@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/singleexamcenter_details.js') !!}"></script> 
@endsection 
