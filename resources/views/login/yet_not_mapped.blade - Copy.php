<!DOCTYPE html>

<html class="loading" lang="en" data-textdirection="ltr">
  <!-- BEGIN: Head-->
  
<!-- Mirrored from pixinvent.com/materialize-material-design-admin-template/html/ltr/vertical-dark-menu-template/page-404.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 26 Aug 2021 06:31:48 GMT -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Materialize is a Material Design Admin Template,It's modern, responsive and based on Material Design by Google.">
    <meta name="keywords" content="materialize, admin template, dashboard template, flat admin template, responsive admin template, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>राजस्थान स्टेट ओपन स्कूल जयपुर</title>
    <link rel="apple-touch-icon" href="public/app-assets/images/favicon/apple-touch-icon-152x152.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../../app-assets/images/favicon/favicon-32x32.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- BEGIN: VENDOR CSS-->
    <link href="{!! asset('public/app-assets/vendors/vendors.min.css') !!}" rel="stylesheet" type="text/css" />
    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
	<link href="{!! asset('public/app-assets/css/themes/vertical-dark-menu-template/materialize.min.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/css/themes/vertical-dark-menu-template/style.min.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/css/pages/page-404.min.css') !!}" rel="stylesheet" type="text/css" />
 
    <!-- END: Page Level CSS-->
    <!-- BEGIN: Custom CSS-->
    <!-- END: Custom CSS-->
  </head>
  <!-- END: Head-->
  <body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 1-column  bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-dark-menu" data-col="1-column">
    <div class="row">
      <div class="col s12">
        <div class="container"><div class="section section-404 p-0 m-0 height-100vh">
  <div class="row">
    <!-- 404 -->
    <div class="col s12 center-align white">
      <img src="{!! asset('public/app-assets/images/gallery/error-2.png') !!}" class="bg-image-404" alt="">
      <h4 class="mb-1">  Would you like to register as `Student` please click on the button </h4>
      
	  <form method="post" action="{{ route('select_aicenter') }}">
	    @if(!empty($getssoid))
	    <input type="hidden" name="ssoid" value="{{$getssoid}}"/>
	    @endif
        <button class="btn cyan waves-effect waves-light " type="submit" name="action"> Student Registration
            </button>
	  </form>
    <h5 class="mb-2"> Your Ssoid Yet Not Mapped With Us.</h5>
	  <br>
      <a class="btn waves-effect waves-light gradient-45deg-deep-purple-blue gradient-shadow mb-4" href="{{route('landing')}}">
       HOME
      </a>
    </div>
  </div>
</div>
        </div>
        <div class="content-overlay"></div>
      </div>
    </div>

    <!-- BEGIN VENDOR JS-->
	<script src="{!! asset('public/app-assets/js/vendors.min.js') !!}"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN THEME  JS-->
	<script src="{!! asset('public/app-assets/js/custom/custom-script.min.js') !!}"></script>
    <!-- END THEME  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <!-- END PAGE LEVEL JS-->
  </body>

<!-- Mirrored from pixinvent.com/materialize-material-design-admin-template/html/ltr/vertical-dark-menu-template/page-404.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 26 Aug 2021 06:31:48 GMT -->
</html>

