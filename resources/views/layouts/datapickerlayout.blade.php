<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
 <head> 
 @php
 header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
@endphp
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="ThemeSelect">
    <title> @php echo Config::get('global.siteTitle'); @endphp </title>
    <link rel="apple-touch-icon" href="public/app-assets/images/favicon/apple-touch-icon-152x152.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('public/app-assets/images/favicon/mono.jpg')}}">
    <link href="{!! asset('public/app-assets/css/icon.css') !!}">
    <!-- BEGIN: VENDOR CSS-->
	<link href="{!! asset('public/app-assets/vendors/vendors.min.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/vendors/flag-icon/css/flag-icon.min.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('public/app-assets/vendors/data-tables/css/jquery.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />
	 <link href="{!! asset('public/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />
     <link href="{!! asset('public/app-assets/vendors/data-tables/css/select.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />
     
  <link href="{!! asset('public/app-assets/css/pages/form-select2.min.css') !!}">
	<link rel="stylesheet" type="text/css" href="{!! asset('public/app-assets/css/pages/login.css') !!}">
	<link rel="stylesheet" href="{!! asset('public/app-assets/vendors/select2/select2.min.css') !!}"  type="text/css">
	<link rel="stylesheet" href="{!! asset('public/app-assets/vendors/select2/select2-materialize.css') !!}" type="text/css">
	
    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
	 <link href="{!! asset('public/app-assets/css/themes/vertical-dark-menu-template/materialize.min.css') !!}" rel="stylesheet" type="text/css" />
	  <link href="{!! asset('public/app-assets/css/themes/vertical-dark-menu-template/style.min.css') !!}" rel="stylesheet" type="text/css" />
	   <link href="{!! asset('public/app-assets/css/pages/data-tables.min.css') !!}" rel="stylesheet" type="text/css" />
    <!-- END: Page Level CSS-->
    <!-- BEGIN: Custom CSS-->
	<!-- <link href="{!! asset('public/app-assets/css/custom/custom.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/buttons.dataTables.min.css') !!}" rel="stylesheet" type="text/css" /> 
  <link href="{!! asset('public/app-assets/css/dataTables.dateTime.min.css') !!}" rel="stylesheet" type="text/css" /> 
  <link href="{!! asset('public/app-assets/css/toastr.min.css') !!}" rel="stylesheet" type="text/css" /> 
	<link href="{!! asset('public/app-assets/vendors/data-tables/css/dataTables.checkboxes.css') !!}" rel="stylesheet" type="text/css" />
	 -->
  
   
  <!-- END: Custom CSS-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> 
    <!-- <script src="{!! asset('public/app-assets/js/jquery-3.5.1.min.js') !!}"></script> -->
	<script>
		var phpJsVarSet = {
			routes: {
				ajaxBaseUrl : "@php echo Config::get('global.ajaxBaseUrl'); @endphp" 
			}
		};
	</script>
		<script>
		/*
				$(document).keydown(function(event){
				if(event.keyCode==123){
					return false;
				}
				else if (event.ctrlKey && event.shiftKey && event.keyCode==73){        
						 return false;
				}
			  });

			  $(document).on("contextmenu",function(e){        
			   e.preventDefault();
			});
			*/

		</script> 
    
  </head>
  <!-- END: Head-->
  <body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   " data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">
    
    <!-- BEGIN: Header-->
    <header class="page-topbar" id="header" >
		<div class="navbar navbar-fixed"> 
        <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-light">
          <div class="nav-wrapper">
           <div class="header-search-wrapper hide-on-med-and-down"><i class="material-icons"></i>
              <span style="color:#00bcd4;font-size:20px;margin-left:30%;"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="materialize logo" width="50px" height="35px"/> @php echo Config::get('global.siteTitle');  @endphp</span>
              <ul class="search-list collection display-none"></ul>
            </div>
			<ul class="navbar-list right">
			<li class="hide-on-med-and-down"><a class="waves-effect waves-block waves-light toggle-fullscreen" href="javascript:void(0);"><i class="material-icons">settings_overscan</i></a></li>
              <li class="hide-on-large-only search-input-wrapper">
				<span style="color:#00bcd4;font-size:11px;padding-top:20px;">
					<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="materialize logo" width="40px" height="35px"/> 
				</span>
				<span style="color:#00bcd4;font-size:11px;padding-top:0px;">
					@php echo Config::get('global.siteTitle'); @endphp
				</span>
				</li>
				@if(!empty(@$id = Auth::user()->id))
				<li><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown"><span class="avatar-status avatar-online"><img src="	{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="avatar"><i></i></span></a></li>
				@endif
            </ul>
			
            <!-- profile-dropdown-->
            <ul class="dropdown-content" id="profile-dropdown">
				@php  
					$ssoid = Auth::user('ssoid');
					$role_id = Session::get('role_id');
				@endphp
				@if(@$ssoid)
					<li><a class="grey-text text-darken-1" href="{{ url('clear-cache') }}"><i class="material-icons">pie_chart_outlined</i>Cache</a></li>
					<li>
						@php $url = "javascript:void(none);"; @endphp
						@if($role_id == config("global.aicenter_id"))
							@php $url = route('update_my_profile'); @endphp
						@endif
						<a class="grey-text text-darken-1" href="{{ $url }}">
							<i class="material-icons">person2</i>
							{{ @$ssoid->ssoid }}
						</a>
					</li>

					<li style="font-size:12px;">
						<a class="grey-text text-darken-1" href="{{ url('back_to_sso') }}">
							<i class="material-icons">pie_chart_outlined</i>Back To SSO
						</a>
					</li>
					<!--
					@can('backup_database')
						<li><a class="grey-text text-darken-1" href="{{ url('backupdb') }}"><i class="material-icons">keyboard_tab</i> Backup</a></li>
					@endcan
					-->
					<li class="divider"></li>
					<li><a class="grey-text text-darken-1" href="{{ route('logout') }}"><i class="material-icons">keyboard_tab</i>Logout</a></li>
			  @endif
            </ul>
          </div>
        </nav>
      </div>
    </header>
    <div>
   

    </div>
    <div class="maindom"> 
  @yield('content')
</div>
	
	
	

<style>
  .dataTable > tbody > tr > td{
      white-space:normal !important;
      max-width:225px;
  }
.toast-top-full-width {
  font-size: 10px;
  width: 25%;
}
</style>
@include('elements.apploader')
	   <footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow">
      <div class="footer-copyright">
        <div class="container"><span>&copy; {{ now()->year }} <a href="https://rsosadmission.rajasthan.gov.in/" target="_blank">RSOS</a> All rights reserved.</span><span class="right hide-on-small-only">Design and Developed by <a href="#">DOIT&C</a></span></div>
      </div>

      @php 
  $ip = NULL;
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ip = $_SERVER['REMOTE_ADDR'];
  } 
@endphp

@include('elements.dom.appga')

    </footer>

    <!-- END: Footer-->
    <!-- BEGIN VENDOR JS-->
     <!-- add by lokendar  -->
    <!-- <script src="{!! asset('public/app-assets/js/vendors.min.js') !!}"></script> -->
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <!-- add by lokendar  -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
    <script src="{!! asset('public/app-assets/vendors/data-tables/js/jquery.dataTables.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/vendors/data-tables/js/dataTables.select.min.js') !!}"></script>


  <script src="{!! asset('public/app-assets//js/scripts/form-select2.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets//vendors/select2/select2.full.min.js') !!}"></script>



    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN THEME  JS-->
	<script src="{!! asset('public/app-assets/js/plugins.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/search.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/custom/custom-script.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/scripts/customizer.min.js') !!}"></script>
    <!-- END THEME  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    
    <script src="{!! asset('public/app-assets/js/scripts/data-tables.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/dataTables.buttons.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/jszip.min.js') !!}"></script>
	<!--<script src="{!! asset('public/app-assets/js/pdfmake.min.js') !!}"></script>-->
	<script src="{!! asset('public/app-assets/buttons.html5.min.js') !!}"></script>
	<!--<script src="{!! asset('public/app-assets/datatable/dataTables.rowsGroup.js') !!}"></script>-->

	<script src="{!! asset('public/app-assets/js/toastr.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/sweetalert.min.js') !!}"></script>
	<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>-->
	

	<script src="{!! asset('public/app-assets/js/scripts/advance-ui-modals.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/dataTables.dateTime.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/jquery.validate.min.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/custom/custom.js') !!}"></script>


<script> 

	$( document ).ready(function() {
		$( ".txtOnly" ).keypress(function(e) {
			var key = e.keyCode;
			if (key >= 48 && key <= 57) {
				e.preventDefault();
			}
		});
	});
	
	
	/*
	$(document).ready(function() {  
		var ip = "@php echo $ip;  @endphp";
		if(ip != "10.68.181.236" || ip != "10.68.181.213" || ip != "10.68.181.229"){
			window.history.forward();
			window.onload = function(){
				window.history.forward();
			}; 
			window.onunload = function() {
				null;
			};
		}
  });
  */
  
</script>

    <!-- END PAGE LEVEL JS-->
  </body>

<!-- Mirrored from pixinvent.com/materialize-material-design-admin-template/html/ltr/vertical-dark-menu-template/table-data-table.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 26 Aug 2021 06:31:53 GMT -->
</html>

@include("elements.php_var_js_set")


   

  

  
  
   
  
  

