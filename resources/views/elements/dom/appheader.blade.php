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
    <link href="{!! asset('public/app-assets/css/pages/intro.min.css') !!}" rel="stylesheet" type="text/css" />

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
	<link href="{!! asset('public/app-assets/css/custom/custom.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/buttons.dataTables.min.css') !!}" rel="stylesheet" type="text/css" /> 
	<link href="{!! asset('public/app-assets/css/dataTables.dateTime.min.css') !!}" rel="stylesheet" type="text/css" /> 
	<link href="{!! asset('public/app-assets/css/toastr.min.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/vendors/data-tables/css/dataTables.checkboxes.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/css/quill.snow.css') !!}" rel="stylesheet" type="text/css"/>
	   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" rel="stylesheet"/>
	<!-- END: Custom CSS-->
	<script src="{!! asset('public/app-assets/js/jquery-3.5.1.min.js') !!}"></script>
  <!-- public/app-assets/js/scripts/intro.min.js -->
	@php  
		$developer_admin = Config::get("global.developer_admin");
		$allowedBackButtonForRoles = Config::get("global.allowedBackButtonForRoles");
		$current_login_role_id = Session::get("role_id");
		$isBackAllowedForTheCurrentRole = false;
		if(@$allowedBackButtonForRoles){
			if(in_array(@$current_login_role_id,@$allowedBackButtonForRoles)){
				$isBackAllowedForTheCurrentRole = true;
			}
		}
		use App\Helper\CustomHelper;
		$permissions = CustomHelper::roleandpermission();
		
	@endphp
	<script> 
		var phpJsVarSet = {
			routes: { 
				logoutUrl : "@php echo Config::get('global.logoutUrl'); @endphp",
				ajaxBaseUrl : "@php echo Config::get('global.ajaxBaseUrl'); @endphp" 
			},
			extra: {
				siteTitle: "@php echo Config::get('global.siteTitle'); @endphp",
				whiteListMasterIps: '@php echo json_encode(Config::get("global.whiteListMasterIps")) @endphp',
				current_ip: '@php echo json_encode(Config::get("global.CURRENT_IP")) @endphp',
				current_login_role_id: '@php echo Session::get("role_id") @endphp',
				isBackAllowedForTheCurrentRole: '@php echo $isBackAllowedForTheCurrentRole @endphp',
				allowedBackButtonForRoles: '@php echo json_encode(Config::get("global.allowedBackButtonForRoles")) @endphp'
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
				<span style="color:#00bcd4;font-size:20px;margin-left:30%;"><img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="materialize logo" width="50px" height="35px"/> 
					{{Config::get('global.siteTitle')}}
				</span>
              <ul class="search-list collection display-none"></ul>
            </div>
			@php  $role_id = Session::get('role_id'); @endphp
			
			
			<ul class="navbar-list right">
				<li class="hide-on-med-and-down"><a class="waves-effect waves-block waves-light toggle-fullscreen" href="javascript:void(0);"><i class="material-icons">settings_overscan</i></a></li>
				
				@if($role_id == $developer_admin)  
			
					<li class="dropdown-language"><a class="waves-effect waves-block waves-light translation-button" href="#" data-target="translation-dropdown"><i class="material-icons">link</i></a>
						<ul class="dropdown-content" id="translation-dropdown" tabindex="0" style="display: none; width: 168.812px; left: 939.172px; top: 64px; height: 200px; transform-origin: 100% 0px; opacity: 1; transform: scaleX(1) scaleY(1);">
							<li class="dropdown-item" tabindex="0">
								<a class="grey-text text-darken-1" href="{{route('roles.index')}}">
									<i class="material-icons"></i>
									Roles
								</a>
							</li>
							<li class="divider"></li>
							<li class="dropdown-item" tabindex="1">
								<a class="grey-text text-darken-1" href="{{ route('users.index') }}">
									<i class="material-icons"></i>
									Users
								</a>
							</li>
							<li class="divider"></li>
							<li class="dropdown-item" tabindex="2">
								<a class="grey-text text-darken-1" href="{{ route('permissions.index') }}">
									<i class="material-icons"></i>
									Permissions
								</a>
							</li>
							<li class="divider"></li>
							<li class="dropdown-item" tabindex="3">
								<a class="grey-text text-darken-1" href="{{route('verfication_aicodes_details','ao')}}">
									<i class="material-icons"></i>
									AO Aicodes
								</a>
							</li>
							<li class="divider"></li>
							<li class="dropdown-item" tabindex="4">
								<a class="grey-text text-darken-1" href="{{route('verfication_aicodes_details','verifier')}}">
									<i class="material-icons"></i>
									Verifier Aicodes
								</a>
							</li>
							<li class="divider"></li>
							<li class="dropdown-item" tabindex="5">
								<a class="grey-text text-darken-1" href="{{ route('single_screen_dates') }}">
									<i class="material-icons"></i>
									Single Screen Dates
								</a>
							</li>
						</ul>
					</li>
				
				
				@endif
				<li class="hide-on-large-only search-input-wrapper">
					<span style="color:#00bcd4;font-size:12px;padding-top:20px;">
						<span class="avatar-status avatar-online"><img src="	{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="avatar"></span>
					</span>
					<span style="color:#00bcd4;font-size:12px;padding-top:0px;">
						@php echo Config::get('global.siteTitle');
						 $student = Config::get('global.student');
						@endphp
					</span>
				</li>
					
					@php
						$showProfileLogo = false;
					@endphp

					@if($role_id == $student)
						
						@php $ssoid = Auth::guard('student')->user()->ssoid; @endphp						@if(!empty(@$id = Auth::guard('student')->user()->id))
							@php $showProfileLogo = true; @endphp
						@endif
					@else
						@php $ssoid = @Auth::user()->ssoid; @endphp
						@if(!empty(@$id = Auth::user()->id))
							@php $showProfileLogo = true; @endphp
						@endif
					@endif
					

					@if(@$showProfileLogo)
						<li><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown"><span class="avatar-status avatar-online"><img src="	{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="avatar"><i></i></span></a></li>
					@endif


				</ul>
				
				<!-- profile-dropdown-->
				<ul class="dropdown-content" id="profile-dropdown">
				
					@if(@$ssoid)
						@if($role_id != $student)
							<li><a class="grey-text text-darken-1" href="{{ url('clear-cache') }}"><i class="material-icons"></i>Cache</a></li>
							</li>
                        @endif 
							@php $url = "javascript:void(none);"; @endphp
							@if($role_id == config("global.aicenter_id"))
								@php $url = route('update_my_profile'); @endphp
							@endif
                        <li>
							<a class="grey-text text-darken-1" href="{{ $url }}">
								<i class="material-icons"></i>{{@$ssoid}}</a>
						</li>
						
						<li style="font-size:12px;">
							<a class="grey-text text-darken-1" href="{{ route('landing') }}">
								<i class="material-icons"></i>Back To Home
							</a>
						</li>

						<li style="font-size:12px;">
							<a class="grey-text text-darken-1" href="{{ url('back_to_sso') }}">
								<i class="material-icons"></i>Back To SSO
							</a>
						</li> 
						@if(in_array("help_desk",$permissions))
							<li style="font-size:12px;">
								<a class="grey-text text-darken-1" href="{{ url('help_desk') }}">
									<i class="material-icons"></i>Help
								</a>
							</li>
						@endif						
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
	
	@php  
		$ssoid = Auth::user('ssoid');
		$role_id = Session::get('role_id');
	@endphp
	@if(@$ssoid) 
	<div id="">
		<div class=""> 
			<div class="row"> 
				<div class="col s12 m6 l12 right-align-md">
					@include('elements.countdowndeatils')
				</div>
			</div> 
		</div>
	</div> 
	@endif
	

	
	
   

  

  
  
   
  
  
