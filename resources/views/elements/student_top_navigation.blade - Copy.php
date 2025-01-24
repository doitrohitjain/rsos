<link rel="stylesheet" type="text/css" href="{!! asset('public/app-assets/css/layouts/style-horizontal.min.css') !!}">
<!-- BEGIN: Horizontal nav start-->
<div id="main">
     <div class="row">
<nav class="white" id="horizontal-nav">
	<div class="nav-wrapper">
		<ul id="ul-horizontal-nav" data-menu="menu-navigation">
			<li><a class="{{ request()->routeIs('persoanl_details') ? 'active' : '' }}" href="{{ route('persoanl_details',Crypt::encrypt($student_id)) }}"><i class="material-icons">groups</i><span><span class="dropdown-title" data-i18n="Dashboard">Persoanl Details</span></span></a>
				<ul class="dropdown-content dropdown-horizontal-list" id="DashboardDropdown"></ul>
			</li>
			<li><a class="{{ request()->routeIs('address_details') ? 'active' : '' }}" href="{{ route('address_details',Crypt::encrypt($student_id)) }}"><i class="material-icons">dvr</i><span><span class="dropdown-title" data-i18n="Templates">Address Details</span></span></a>
				<ul class="dropdown-content dropdown-horizontal-list" id="TemplatesDropdown"></ul>
			</li>
			<li><a class="{{ request()->routeIs('document_details') ? 'active' : '' }}" href="{{ route('document_details',Crypt::encrypt($student_id)) }}"><i class="material-icons">mail_outline</i><span><span class="dropdown-title" data-i18n="Apps">Document</span></span></a>
				<ul class="dropdown-content dropdown-horizontal-list" id="AppsDropdown"></ul>
			</li>
			<li><a class="dropdown-menu" href="Javascript:void(0)" data-target="DashboardDropdown"><i class="material-icons">dashboard</i><span><span class="dropdown-title" data-i18n="Dashboard">Subjects</span></span></a>
				<ul class="dropdown-content dropdown-horizontal-list" id="DashboardDropdown"></ul>
			</li>
			<li><a class="dropdown-menu" href="Javascript:void(0)" data-target="TemplatesDropdown"><i class="material-icons">dvr</i><span><span class="dropdown-title" data-i18n="Templates">Fees</span></span></a>
				<ul class="dropdown-content dropdown-horizontal-list" id="TemplatesDropdown"></ul>
			</li>
			<li><a class="dropdown-menu" href="Javascript:void(0)" data-target="AppsDropdown"><i class="material-icons">mail_outline</i><span><span class="dropdown-title" data-i18n="Apps">Preview</span></span></a>
				<ul class="dropdown-content dropdown-horizontal-list" id="AppsDropdown"></ul>
			</li>
		</ul>
	</div>
</nav>
</div>
</div>
 


	


