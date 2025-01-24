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

@extends('layouts.logindefault')
  
  @section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction"> 
					

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
						<h2><span style="color:#4361ee;">Disadvantage Student</span></h2>
                        <div class="login-form-content">
                            
                            <form action="{{route('dgs_login')}}" class="signin-form" method="post">
							@csrf
								@if(@$auth_users)
									<a href="{{route('logout')}}" class="btn btn-style mt-3" style="background-color:#03a9f4;padding: 14px 25px;border: none;text-align:center;display: inline-block;font-size:18px;font-weight:700">
										Logout
									</a>
								@else
									<div class="one-frm">
										<input type="text" name="username" autocomplete="off" style="font-size:16pt"; placeholder="Username" required="true">
									</div>
									
									<div class="one-frm">
										<input type="password" name="password" autocomplete="off" style="font-size:16pt"; placeholder="Password" required="true">
									</div> 
									<button type="submit" class="btn btn-style mt-3">Sign In </button>
								@endif 
                     
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		 
        <!-- copyright-->
        <div class="copyright text-center">
            <div class="wrapper">
                
            </div>
        </div>
    </section>
</body> 

</div>
				</div>
			</div>
		</div>
	</div></br></br></br>
@endsection 
</html>