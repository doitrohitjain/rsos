@extends('layouts.defaultwithguest')
@section('content')
<div id="">
          <div class="container">
            <div class="seaction">

  <div class="row">
    <!-- Form with icon prefixes -->
	
	@php 
	 
		$isOnlySingle = @$isOnlySingle[1];
		$Onesize = 14;
		$Twosize = 12;
		$showStatus = "";
		 
	@endphp
	@if(@$isOnlySingle && $isOnlySingle == 1) 
		@php $Twosize = 8; @endphp 
	@endif

	@php 
		if($role_id == Config::get('global.student')){
			$showStatus = " hide ";
			$Twosize = 12;
		}
	@endphp

	 
	@if(@$isOnlySingle && $isOnlySingle == 1) 
	<div class="col s12 m4 l{{ $Onesize }} border-radius-6 {{ @$showStatus}}">
		<div id="validation" class="card card card-default scrollspy">
			<div class="card-content">
				<center>
				<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="materialize logo" width="60px" height="60px"/>
				
				<h5 class="card-title"> 
					Welcome
				</h5>
				<img src="{{asset('public/app-assets/images/user.png')}}" alt="materialize logo" width="120px" height="120px"/>
				
				<h5 class="card-title"> 
					Student Registration
				</h5>
				</center>
				<span style="font-size:17px;">
					Would you like to register as a Student please click on the 'New Registration' button.<br>(क्या आप एक छात्र के रूप में पंजीकरण करना चाहेंगे, कृपया 'नया पंजीकरण' बटन पर क्लिक करें।) 
				</span>
			   <form method="post" action="{{ route('new_term_conditions') }}">
				<div class="row">
				@if(!empty($egetssoid))
				<input type="hidden" name="ssoid" value="{{@$egetssoid}}"/>
				<input type="hidden" name="role_id" id="role_id" value="{{@$role_id}}"/>
				@endif
				  <div class="row">
					<div class="input-field col s12">
					  <center><button class="btn cyan waves-effect waves-light " type="submit" name="action"> New Registration
						</button>
					
						
						
						<a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text " style="">Cancel</a></center>
					</div>
				  </div>
				</div>
			  </form>
			</div>
		</div>
    </div>
	@endif
	
	<div class="col s12 m8 l{{ $Twosize }} border-radius-6">
		<div id="prefixes" class="card card card-default scrollspy">
			<div class="card-content">
			  <h4 class="card-title" style="    line-height: 32px;display: block;    margin-bottom: 8px;    font-size: 22px;   font-family: math;    font-weight: 500;">
				Already registered student (पहले से ही पंजीकृत)
			  	
			  </h4>
			  {{ Form::open(['route' => [request()->route()->getAction()['as'], $egetssoid], 'id' => 'allreadystudent']) }}
				<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
				@if(!empty($egetssoid))
				<input type="hidden" name="ssoid" value="{{@$egetssoid}}"/>
				<input type="hidden" name="dob" id="dob" value="{{@$dob}}"/>
				<input type="hidden" name="role_id" id="role_id" value="{{@$role_id}}"/>
				@endif
				<div class="col s12">
							@php 
							$star = Config::get('global.starMark');
							$lbl='Enrollment(नामांकन) XXXXXXXXXXX'; $fld='enrollment'; @endphp 
							<span class="extraCss">@php echo $lbl . $star; @endphp </span>
							@php
								$modelContent = '<center><img src="../../public/app-assets/images/sample_enrolment.png"/></center>';
							@endphp
							<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span> 
							<div class="input-field">
								{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num ' . 'cls_' . $fld,'autocomplete'=>'off','placeholder' => $lbl ,'maxlength'=> 11]); !!}
								@include('elements.field_error')    
							</div>
						</div>
				<div class="row">
				  <div class="input-field col s12">
					@php $lbl='Date of Birth(जन्म तिथि) (Month DD,YYYY)'; $placeholder = "Select ". $lbl; $fld='dob'; @endphp
					<span class="extraCss">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
					@php
						$modelContent = '<center><h4>Date of Birth as you filled in your application form.<br>(जन्म तिथि जैसा कि आपने अपना आवेदन पत्र भरा था।)</h4></center>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span> 
					<div class="input-field"> 
						{!!Form::text($fld,null,['class'=>'dob form-control datepicker2  ' .'cls_' . $fld,'autocomplete'=>'off','id'=>'my_date_picker','placeholder' => $lbl,'required'=>'required','readonly'=>'readonly']); !!}
						@include('elements.field_error')	
					</div>
				  </div>
				</div>
					  <div class="col m4 s12">
							@php $lbl='Captcha Code(कैप्चा कोड)'; $placeholder = "Select ". $lbl; $fld='captcha'; @endphp
							<span class="extraCss">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
							<div class="input-field"> 
								<span id="captchaImg"><?php echo $captchaImage; ?></span>
								<img src="{{asset('public/refresh-icon.png')}}" id="captchaRefresh" alt="materialize logo" width="28px" height="30px" style="float:right;cursor:pointer;margin-left:10px;"/> 
							</div>
						</div>
						<div class="col m8 s12">
							@php $lbl='केप्चा भरे ( Enter captcha )'; $placeholder = "Select ". $lbl; $fld='captcha'; @endphp
							<div class="input-field"> 
								{!!Form::text($fld,null,['class'=>'captcha form-control num  ' .'cls_' . $fld,'minLength'=>1,'maxLength'=>6,'autocomplete'=>'off','id'=>'captcha','placeholder' => $lbl]); !!}
								@include('elements.field_error')	
							</div>
						</div>
				 <!--<div class="row">
				  <div class="row">
					<div class="col m10 s12">
					  <button class="btn cyan waves-effect waves-light" type="submit" name="action">Submit
						<i class="material-icons right">send</i>
					  </button>
					  <a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text " style="">Cancel</a>
					    <button class="btn cyan waves-effect waves-light" type="reset" name="action">Reset
						<i class="material-icons right">send</i>
					  </button>
					</div>
				  </div>
				</div>-->
				 <div class="row"></br></br></br></br>
				           <div class="step-actions right">
										  <div class="row">
											<div class="col m6 s12 mb-1">
											  <button class="green btn submitBtnCls submitconfirms" type="submit" name="action">
											  Continue & <i class="material-icons right">send</i>Next
											  </button>
											</div>
											@php
											$route="";
											@$data=Auth::guard('student')->user();
											if(@$data){
												$route=route('studentsdashboards');
											}else{
												$route=route('landing');
											}
											@endphp
											<div class="col m3 s12 mb-3">
											  <a href="{{$route}}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text " style="">Cancel</a> 
											</div>
											<div class="col m1 s12 mb-3">
											  <button class="waves-effect waves dark btn btn-primary next-step" type="reset">
												Reset
											 </button>
											</div>
										  </div>
										</div>
				                   </div>
				
				{!! Form::close() !!}
			</div>
      </div>
    </div>
    <!-- Form with validation -->
    
</div>
</div>
</div>
</div>
</div>
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/allreadystudent_details.js') !!}"></script> 
@endsection 