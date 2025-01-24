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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>

	 <!-- form section start -->
	 <section class="w3l-workinghny-form">
        <!-- /form -->
        <div class="workinghny-form-grid">
            <div class="wrapper">
                <div class="logo">
                    <h1><span>RSOS</span></h1>
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
                            <form action="{{route('dlrarl')}}" class="signin-form" method="post">
							@csrf
								<div class="one-frm">
								<label>Username</label>
								 @if($showStatus == true)
									<input type="text" name="ssoid" autocomplete="off" style="font-size:16pt";  value="123" placeholder="SSOID" required="">
								 @else
									<input type="text" name="ssoid" autocomplete="off" style="font-size:16pt"; placeholder="SSOID" required="">
								 @endif
								</div>
								
                                <button class="btn btn-style mt-3">Sign In </button>
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
<script>
	$('.signin-form').submit();
</script>
</html>