@extends('layouts.guest')
@section('content')
<div class="card">
	<div class="card-content">
		 <center>
			<span style="color:#00bcd4;font-size:20px;padding-top:20px;">
				<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="materialize logo" width="40px" height="35px"/> 
			</span>
			<span style="color:#00bcd4;font-size:20px;padding-top:0px;">
				@php echo Config::get('global.siteTitle'); @endphp
			</span>
		</center><br>
		
		<span class="red-text">
            <b>Note : For Download the result Please enter the mandatory fields.(परिणाम डाउनलोड करने के लिए कृपया अनिवार्य फ़ील्ड दर्ज करें।)</b>
        </span>
		
		<div id="tap-target" class="card card-tabs">

        <div class="card-content">
            {{ Form::open(['route' =>[request()->route()->getAction()['as']],'id'=>'result','autocomplete'=>'off']) }}
			{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
			<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
			
			<div class="row">
				<div class="row">
					<?php // 'required'=>'required', ?>
					<div class="col m4 s12">
						@php 
						$star = Config::get('global.starMark');
						$lbl='नामांकन (Enrollment) XXXXXXXXXX'; $fld='enrollment'; @endphp 
						<span class="small_lable">@php echo $lbl . $star; @endphp </span>
						<div class="input-field">
						{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
						@include('elements.field_error')    
						</div>
					</div>
					
					<div class="col m4 s12">
						@php $lbl='जन्म तिथि(Date of Birth) (DD-MM-YYYY)'; $placeholder = "Select ". $lbl; $fld='dob'; @endphp
						<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field"> 
							{!!Form::text($fld,null,['class'=>'dob form-control datepicker','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,]); !!}
							@include('elements.field_error')	
						</div>
					</div>
					
					<div class="col m4 s12">
						@php $lbl='कैप्चा ( captcha )'; $placeholder = "Select ". $lbl; $fld='captcha'; @endphp
						<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field"> 
							<?php echo $captchaImage; ?>
							{!!Form::text($fld,null,['class'=>'captcha form-control num','minLength'=>1,'maxLength'=>6,'autocomplete'=>'off','id'=>'captcha','placeholder' => $lbl,]); !!}
							@include('elements.field_error')	
						</div>
					</div>
				</div><br>
			
				<div class="row">
					<div class="col m10 s12 mb-2">
						<button class="btn cyan waves-effect waves-light right" type="submit" name="action"> Search</button>
					</div>
					
					<div class="col m2 s12 mb-2">
						<a href="{{route('result')}}" class="btn cyan waves-effect waves-light right">Reset</a>
					</div>
				</div> 
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/result_details2.js') !!}"></script> 
@endsection 
      

