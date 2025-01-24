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
            <a href="{{ route('landing')}}"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="materialize logo" width="40px" height="35px"/></a>
        </span>
        <span style="color:#00bcd4;font-size:20px;padding-top:0px;">
            @php echo Config::get('global.siteTitle'); @endphp
        </span>
    </center><br>
    <span class="red-text">
		<b>
		   Note : 
			<br>1.प्रवेश पत्र(Admit Card) को संदर्भ केन्द्र से प्रमाणित करने की आवश्यकता नहीं है।
			<br>2. For download the Admit Card please enter the mandatory fields.(प्रवेश पत्र डाउनलोड करने के लिए कृपया अनिवार्य फ़ील्ड दर्ज करें।)
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
						$lbl='नामांकन (Enrollment) XXXXXXXXXXX'; $fld='enrollment'; @endphp 
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
							{!!Form::text($fld,null,['class'=>'dob form-control datepicker2','autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,'required'=>'required','readonly'=>'readonly']); !!}
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
					<div class="step-actions right">
						<div class="col s6 l4 m4 ">
							<button class=" btn-small gradient-45deg-light-blue-cyan gradient-shadow" type="submit" name="action"> Search</button>
						</div>
						<div class="col s6 l4 m4 mb-3">
							<a href="{{route('result')}}" class="btn-small gradient-45deg-amber-amber" style="color: rgba(255, 255, 255, 0.901961);">Reset </a>
						</div>
						<div class="col s3 l4">
							<a href="{{route('landing')}}" class="waves-effect waves-teal btn btn-small gradient-45deg-deep-orange-orange white-text secondary-con" style="color: rgba(255, 255, 255, 0.901961);">Back </a>
						</div> 
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