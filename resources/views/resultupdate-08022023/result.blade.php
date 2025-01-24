@extends('layouts.guest')
@section('content')
<style>
	.extraCss{
		font-size:16px;font-weight: bold;
	}

</style>
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
		<b>
		   Note : For Download the result Please enter the mandatory fields.(परिणाम डाउनलोड करने के लिए कृपया अनिवार्य फ़ील्ड दर्ज करें।)
		</b>
	</span> 
	
	<div id="tap-target" class="card card-tabs">
		<div class="card-content">
             {{ Form::open(['route' => [request()->route()->getAction()['as']], 'id' => 'result','autocomplete'=>'off']) }}
            <input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
            <div class="row">
				<div class="row">
					<div class="col m4 s12">
						@php 
						$star = Config::get('global.starMark');
						$lbl='नामांकन (Enrollment) XXXXXXXXXX'; $fld='enrollment'; @endphp 
						<span class="extraCss">@php echo $lbl . $star; @endphp </span>
						<div class="input-field">
							{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl ,'maxlength'=> 11, 'minLength'=> 11,'required'=>'required']); !!}
							@include('elements.field_error')    
						</div>
					</div>
			
					<div class="col m4 s12">
						@php $lbl='जन्म तिथि(Date of Birth) (Month DD,YYYY)'; $placeholder = "Select ". $lbl; $fld='dob'; @endphp
						<span class="extraCss">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field"> 
							{!!Form::text($fld,null,['class'=>'dob form-control datepicker','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,'required'=>'required']); !!}
							@include('elements.field_error')	
						</div>
					</div>
			
					<div class="col m2 s12">
						@php $lbl='कैप्चा कोड(Captcha Code)'; $placeholder = "Select ". $lbl; $fld='captcha'; @endphp
						<span class="extraCss">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
						<div class="input-field"> 
							<span id="captchaImg"><?php echo $captchaImage; ?></span>
							<img src="{{asset('public/refresh-icon.png')}}" id="captchaRefresh" alt="materialize logo" width="28px" height="30px" style="float:right;cursor:pointer"/> 
						</div>
					</div>
					<div class="col m2 s12">
						@php $lbl='केप्चा भरे ( Enter captcha )'; $placeholder = "Select ". $lbl; $fld='captcha'; @endphp
						<span class="small_lable"></span>
						<div class="input-field"> 
							{!!Form::text($fld,null,['class'=>'captcha form-control num','minLength'=>1,'maxLength'=>6,'autocomplete'=>'off','id'=>'captcha','placeholder' => $lbl]); !!}
							@include('elements.field_error')	
						</div>
					</div>
				</div><br>
			
				<div class="row">
					<div class="right">
						<button class=" btn-large gradient-45deg-light-blue-cyan gradient-shadow" type="submit" name="action"> Search</button>
						&nbsp;&nbsp;&nbsp;
						<a href="{{route('result')}}" class="btn-large gradient-45deg-amber-amber" style="color: rgba(255, 255, 255, 0.901961);">Reset </a>
					</div>
				</div> 
			{{ Form::close() }}
		</div>
	</div>
</div>
</div>
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/result_details.js') !!}"></script> 
@endsection 
      

