<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="ThemeSelect">
    <title>राजस्थान स्टेट ओपन स्कूल जयपुर</title>
    <link rel="apple-touch-icon" href="public/app-assets/images/favicon/apple-touch-icon-152x152.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('public/app-assets/images/favicon/administrator.png')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- BEGIN: VENDOR CSS-->
	<link href="{!! asset('public/app-assets/vendors/vendors.min.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets/vendors/flag-icon/css/flag-icon.min.css') !!}" rel="stylesheet" type="text/css" />
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
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css">-->
	<link rel="stylesheet" type="text/css"href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<link href="{!! asset('public/app-assets/vendors/data-tables/css/dataTables.checkboxes.css') !!}" rel="stylesheet" type="text/css" />
	<link href="{!! asset('public/app-assets//css/pages/form-select2.min.css') !!}">
	<link rel="stylesheet" type="text/css" href="{!! asset('public/app-assets/css/pages/login.css') !!}">
	<link rel="stylesheet" href="{!! asset('public/app-assets/vendors/select2/select2.min.css') !!}"  type="text/css">
	<link rel="stylesheet" href="{!! asset('public/app-assets/vendors/select2/select2-materialize.css') !!}" type="text/css">
	<link rel="stylesheet" href="{!! asset('public/app-assets/css/custom/loader.css') !!}" type="text/css">
	
    <!-- END: Custom CSS-->
	 <script src="{!! asset('public/app-assets/js/jquery-3.5.1.min.js') !!}"></script>
	<script>
		var phpJsVarSet = {
			routes: {
				ajaxBaseUrl : "@php echo Config::get('global.ajaxBaseUrl'); @endphp"
				
			}
		};
		
	</script>
@php  
		$allowedBackButtonForRoles = Config::get("global.allowedBackButtonForRoles");
		$current_login_role_id = Session::get("role_id");
		$isBackAllowedForTheCurrentRole = false;
		if(@$allowedBackButtonForRoles){
			if(in_array(@$current_login_role_id,@$allowedBackButtonForRoles)){
				$isBackAllowedForTheCurrentRole = true;
			}
		}
		
	@endphp
	<script>
		
		var phpJsVarSet = {
			routes: { 
				logoutUrl : "@php echo Config::get('global.logoutUrl'); @endphp",
				ajaxBaseUrl : "@php echo Config::get('global.ajaxBaseUrl'); @endphp" 
			},
			extra: {
				whiteListMasterIps: '@php echo json_encode(Config::get("global.whiteListMasterIps")) @endphp',
				current_ip: '@php echo json_encode(Config::get("global.CURRENT_IP")) @endphp',
				current_login_role_id: '@php echo Session::get("role_id") @endphp',
				isBackAllowedForTheCurrentRole: '@php echo $isBackAllowedForTheCurrentRole @endphp',
				allowedBackButtonForRoles: '@php echo json_encode(Config::get("global.allowedBackButtonForRoles")) @endphp'
			}
		};
	</script>



  </head>
  <!-- END: Head-->
  <body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   " data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">