<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>Working Signin form Responsive Widget Template :: W3layouts</title>
	<!-- Meta tag Keywords -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8" />
	<meta name="keywords"
		content="Working Signin form Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
	<!-- //Meta tag Keywords -->
	<link href="//fonts.googleapis.com/css2?family=Karla:wght@400;700&display=swap" rel="stylesheet">
	<!--/Style-CSS -->
	<link rel="stylesheet" href="public/app-assets/css/style.css" type="text/css" media="all" />
	<!--//Style-CSS -->
</head>

<body>

	 <!-- form section start -->
	 <section class="w3l-workinghny-form">
        <!-- /form -->
        <div class="workinghny-form-grid">
            <div class="wrapper">
                <div class="logo">
                    <h1><a class="brand-logo" href="index.html"><span>Working</span> Sign In</a></h1>
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
                            <form action="{{route('sdlogin')}}" class="signin-form" method="post">
							@csrf
								<div class="one-frm">
								<label>SSOID</label>
								 @if($showStatus == true)
									<input type="text" name="ssoid" autocomplete="off" value="lokeshprojects" placeholder="SSOID" required="">
								 @else
									<input type="text" name="ssoid" autocomplete="off" placeholder="SSOID" required="">
								 @endif
								</div>
								
                                <button class="btn btn-style mt-3">Sign In </button>
                                <p class="already">Don't have an account? </p>
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

</html>