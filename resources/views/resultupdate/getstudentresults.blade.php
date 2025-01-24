@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
	  <div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
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
      <div id="tap-target" class="card card-tabs">
        <div class="card-content">
             {!! Form::open(array('route' => 'Getstudentresults','method'=>'POST')) !!}
            <div class="row">
              <div class="row">
            <div class="col m3 s12">
                @php $lbl='enrollment'; $fld='enrollment'; @endphp 
               <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
                @include('elements.field_error')    
                </div>
            </div>
			<div class="col m3 s12">
			@php $lbl='(Date of Birth)(MM-DD-YYYY)'; $placeholder = "Select ". $lbl; $fld='dob'; @endphp
			<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field"> 
				@if(!empty(@$studentdata->$fld))
				@php 
					$dobFormat = @$studentdata->$fld;
					$dobFormat = date("M d, Y",strtotime(@$dobFormat));
				@endphp
				@endif
				{!!Form::text($fld,@$dobFormat,['class'=>'dob form-control datepicker','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,]); !!}
			 	@include('elements.field_error')	
			</div>
			</div>
			<div class="col m3 s12 captcha">
                 <span>{!! captcha_img() !!}</span>
                 <button type="button" class="btn btn-danger" class="reload" id="reload" >Refresh</button> 
                </div>
            </div>
			<div class="col m3 s12 ">
                @php $lbl='captcha'; $fld='captcha'; @endphp 
               <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl, 'id' => 'captcha']); !!}
                @include('elements.field_error')    
                </div>
            </div>
                    
          </div>
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-2">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Submit
            </button>
          </div>
          <div class="col m2 s12 mb-2">
            <a href="{{route('studentchecklists')}}" class="btn cyan waves-effect waves-light right">Reset </a>
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
      </div>
	</div>

@endsection
@section('customjs')
 <script src="{!! asset('public/app-assets/js/bladejs/captcha_details.js') !!}"></script> 
@endsection 
