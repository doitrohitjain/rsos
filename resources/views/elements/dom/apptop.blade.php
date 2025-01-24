<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Materialize is a Material Design Admin Template,It's modern, responsive and based on Material Design by Google.">
    <meta name="keywords" content="materialize, admin template, dashboard template, flat admin template, responsive admin template, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title>@php echo Config::get('global.siteTitle'); @endphp</title>
    <link rel="apple-touch-icon" href="public/app-assets/images/favicon/apple-touch-icon-152x152.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('public/app-assets/images/favicon/mono.jpg')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- BEGIN: VENDOR CSS-->
	<link href="{!! asset('public/app-assets/vendors/vendors.min.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/vendors/flag-icon/css/flag-icon.min.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('public/app-assets/vendors/data-tables/css/jquery.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />
	 <link href="{!! asset('public/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />
     <link href="{!! asset('public/app-assets/vendors/data-tables/css/select.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />

    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
	 <link href="{!! asset('public/app-assets/css/themes/vertical-dark-menu-template/materialize.min.css') !!}" rel="stylesheet" type="text/css" />
	  <link href="{!! asset('public/app-assets/css/themes/vertical-dark-menu-template/style.min.css') !!}" rel="stylesheet" type="text/css" />
	   <link href="{!! asset('public/app-assets/css/pages/data-tables.min.css') !!}" rel="stylesheet" type="text/css" />
    <!-- END: Page Level CSS-->
    <!-- BEGIN: Custom CSS-->
	<link href="{!! asset('public/app-assets/css/custom/custom.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/buttons.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css">
    <!-- END: Custom CSS-->
  </head>
  <!-- END: Head-->
  <body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   " data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">
    <header class="page-topbar" id="header">
      <div class="navbar navbar-fixed"> 
        <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-light">
          <div class="nav-wrapper" style="color:black;">

			<div class="navbar-list left">
				<a class="brand-logo darken-1" href="index-2.html" style="color:black;">
				<img class="hide-on-med-and-down" src="public/app-assets/images/logo/logo.jpg" alt="materialize logo" height="50px;" width="50px;"><span class="logo-text hide-on-med-and-down"></span>
				</a>
			</div>
			<span style="font-size:20px;text-align:center;">
				&nbsp;&nbsp;&nbsp;
				@php echo Config::get('global.siteTitle'); @endphp
			</span>
			<ul class="navbar-list right">
                <li class="" style="">
                  <a class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12 mb-1 show_confirm2 show_confirm2" href="{{ url('clear-cache') }}" style="background-color:#ff4081;color:white;font-size:20px;">
                    Clear Cache</a>
				</li> 
				<li class="hide-on-med-and-down"><a class="waves-effect waves-block waves-light toggle-fullscreen" href="javascript:void(0);"><i class="material-icons">settings_overscan</i></a></li> 
            </ul>
            <!-- profile-dropdown-->
          </div>
        </nav>
      </div>
    </header>
    <!-- END: Header-->
	
	<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns" data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">
  <!-- BEGIN: Page Main-->
			<div id="main">
			  <div class="row">
				<div class="col s12">
					<div class="container">
						<div class="section">


						