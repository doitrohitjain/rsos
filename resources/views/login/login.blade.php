<!DOCTYPE html>
<html lang="zxx">

<head>
	<title> राजस्थान स्टेट ओपन स्कूल, जयपुर {{ now()->year }}-{{ now()->year+1 }}</title>
	 <link rel="shortcut icon" type="image/x-icon" href="{{asset('public/app-assets/images/favicon/mono.jpg')}}">
	<!-- Meta tag Keywords -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8" />
	<meta name="keywords"
		content="राजस्थान स्टेट ओपन स्कूल, जयपुर {{ now()->year }}-{{ now()->year+1 }}" />
	<!-- //Meta tag Keywords -->
	<link href="//fonts.googleapis.com/css2?family=Karla:wght@400;700&display=swap" rel="stylesheet">
	<!--/Style-CSS -->
	<link rel="stylesheet" href="public/app-assets/css/style.css" type="text/css" media="all" />
	<!--//Style-CSS -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

	 <!-- form section start -->
	 <section class="w3l-workinghny-form">
        <!-- /form -->
        <div class="workinghny-form-grid">
            <div class="wrapper">
                <div class="logo">
					@php 
						$auth_users=Auth::user();
						if($auth_users==null){
							$auth_users=Auth::guard('student')->user();
						}					
					@endphp
				   <h2><span style="color:Black;">RSOS</span></h2>								
				</div>
                    <!-- if logo is image enable this   
                        <a class="brand-logo" href="#index.html">
                            <img src="image-path" alt="Your logo" title="Your logo" style="height:35px;" />
                        </a> -->
                </div>
                <div class="workinghny-block-grid">
                    <div class="workinghny-left-img align-end">
                        <img src="public/app-assets/images/2.png" class="img-responsive" alt="img" />
                    </div>
                    <div class="form-right-inf">
						
                        <div class="login-form-content">
                            <h2>Where to?</h2>
                            <form action="{{route('lrarl')}}" class="signin-form" method="post">
							@csrf
							@if(@$auth_users->id && $auth_users->id > 0) 
								@else
								<div class="one-frm">
									<div class="upperpart">
								 
									 @if($showStatus == true)
										<input type="text" name="ssoid" autocomplete="off" style="font-size:16pt";  value="123" placeholder="SSOID" required="true">
									 @else
										<input type="text" name="ssoid" autocomplete="off" style="font-size:16pt"; placeholder="SSOID" required="true">
									 @endif
									<div class="row"> 
										@if($showStatus == true)
											<input type="text" name="mobile" autocomplete="off" style="font-size:16pt"; placeholder="Mobile" id="mobile" maxlength="10" value="9876543210" required="true">
										@else
											<input type="text" name="mobile" autocomplete="off" style="font-size:16pt"; placeholder="Mobile" id="mobile" maxlength="10" required="true">
										@endif
										@if($showStatus == true)
										@else
										<button id="sendOtp" class="btn btn-style mt-1" style="background:#43eeb0;">
											Get OTP
										</button>
										@endif
									</div> 
									</div> 
									
									@if($showStatus == true)
										<input type="password" name="otp"  autocomplete="off" style="font-size:16pt"; placeholder="OTP" required="true"  maxlength="10" value="245425">
									@else
										<input type="password" name="otp"  autocomplete="off" style="font-size:16pt"; placeholder="OTP" required="true"  maxlength="10">
									@endif
								</div>
								
                                <button class="btn btn-style mt-3">Sign In </button>
<br><br>
@endif


								@if(@$auth_users->id && $auth_users->id > 0) 
									
									<div>
									 @php
										$auth_users=Auth::user();
										if($auth_users==null){
										$auth_users=Auth::guard('student')->user();
										}
									@endphp
									@if(@$auth_users)
										<a href="{{route('dashboard')}}"
										  class="btn btn-style mt-3" style="background-color:#03a9f4;padding: 14px 25px;border: none;text-align:center;display: inline-block;font-size:18px;font-weight:700">
											My Dashboard
										</a>
									@endif
									</div>
									<br><br>
									<a href="{{route('logout')}}" class="btn btn-style mt-3" style="background-color:rgb(203 14 46);padding: 14px 25px;border: none;text-align:center;display: inline-block;font-size:18px;font-weight:700">
										Logout
									</a>
                                   
								@else
									
								@endif
								@if ( $Hour >= 5 && $Hour <= 11 )
								 <p class="already">Good Morning  <img width="30" height="30"src="public/app-assets/images/download.png" class="img-responsive" alt="img" />&nbsp;&nbsp;{{ date('d-m-Y') }}</p>
								@elseif ( $Hour >= 12 && $Hour <= 18 )
								<p class="already">Good Afternoon  <img width="30" height="30" src="public/app-assets/images/downloads.png" class="img-responsive" alt="img" />&nbsp;&nbsp;{{ date('d-m-Y') }}</p>
								@elseif ( $Hour >= 19 || $Hour <= 4 ) 
								 <p class="already">Good Evening <img width="30" height="30" src="public/app-assets/images/good-evening-2.gif" class="img-responsive" alt="img" />&nbsp;&nbsp;{{ date('d-m-Y') }} </p>
								@elseif(hrs > 22)
								<p class="already">Go to bed!&nbsp;&nbsp;{{ date('d-m-Y') }}</p>								
								@endif
                     
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
        <!-- //form -->
        <!-- copyright-->
        <div class="copyright text-center">
            <div class="wrapper">
                
            </div>
        </div>
    </section>
</body>

<div class="custom-loader mainCls loader hide">
	<div class="cssload-loader"> 
		<div class="cssload-inner cssload-one"></div>
		<div class="cssload-inner cssload-two"></div>
		<div class="cssload-inner cssload-three"></div>
	</div>
</div> 
<style> 
.custom-loader {
	width: 120px;
	height: 120px;
	color: #03a9f4; 
	background:
	linear-gradient(currentColor 0 0) 100%  0,
	linear-gradient(currentColor 0 0) 0  100%;
	background-size: 50.1% 50.1%;
	background-repeat: no-repeat;
	animation:  f7-0 0.5s infinite linear;
	position: fixed;
	top: 45%;
	left: 45%;
	transform: translate(-50%, -50%);
}
.custom-loader::before,
.custom-loader::after {
  content:"";
  position: absolute;
  inset:0 50% 50% 0;
  background:currentColor;
  transform:scale(var(--s,1)) perspective(300px) rotateY(0deg);
  transform-origin: bottom right; 
  animation: f7-1 0.25s infinite linear alternate;
}
.custom-loader::after {
  --s:-1,-1;
} 
@keyframes  f7-0 {
  0%,49.99% {transform: scaleX(1)  rotate(0deg)}
  50%,100%  {transform: scaleX(-1) rotate(-90deg)}
} 
@keyframes  f7-1 {
  49.99% {transform:scale(var(--s,1)) perspective(300px) rotateX(-90deg) ;filter:grayscale(0)}
  50%    {transform:scale(var(--s,1)) perspective(300px) rotateX(-90deg) ;filter:grayscale(0.8)}
  100%   {transform:scale(var(--s,1)) perspective(300px) rotateX(-180deg);filter:grayscale(0.8)}
} 
.hide{
	display:none;
}
</style>

<script> 

	$(".signin-form").submit(function(){
		showLoading();
	});

	function showLoading() {  
		$('.mainCls').removeClass('hide');
	} 
	function hideLoading() {
		$('.mainCls').addClass('hide');
	}
	document.onreadystatechange = function () {
		var state = document.readyState;
		if (state == 'interactive') {
			showLoading();
		} else if (state == 'complete') {
			hideLoading();
		}
	}
	$(document).ready(function() {
	function showLoading() { 
		$('.mainCls').css('display', 'block');
	  } 
	function hideLoading() {
		$('.mainCls').css('display', 'none');
	}
	$('#sendOtp').on('click',function(){
		var mobile=$("#mobile").val();
		if(mobile == ''){
			alert('Enter Mobile Number.');
			return false;
		}
		
		$.ajax({
			url: "{{route('sdloginOTP')}}",
			type: "get",
			data: {'mobile': mobile},
			dataType : 'json',
			success: function (result){
				if(result.status ==  true){
					alert(result.message);
					$(".upperpart").addClass("hide");
				}else{
					alert(result.error);
				}
			},
		});
	});


	
  
	
});
	
	
</script>

</html>