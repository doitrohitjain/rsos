@php 
	use App\Helper\CustomHelper;
	use App\Http\Controllers\Controller;
	$permissions = CustomHelper::roleandpermission();
	$getdatamaster1 = CustomHelper::getdatamaster();
	$getdatamastersexam = CustomHelper::getdatamastersexam();
	$selected_session = CustomHelper::_get_selected_sessions();
	$role_id = Session::get('role_id');
	$ai_code = Auth::user()->ai_code;
	$changerole = CustomHelper::_changerole();
	$custom_controller_obj = new Controller;
	$showSuppStatus = $custom_controller_obj->_getCheckAllowToCheckSupp();
	$resultCount = 0;
	if(@$changerole){
		$resultCount = count($changerole); 
	}
@endphp
<aside class="sidenav-main nav-expanded nav-lock nav-collapsible sidenav-dark sidenav-active-rounded">
	<div class="brand-sidebar">
		<h1 class="logo-wrapper"><a class="brand-logo darken-1" href="javascript:(0)">
		<img class="hide-on-med-and-down " src="{{asset('public/app-assets/images/favicon/administrator.png')}}" alt="materializeo"/>
		<span class="logo-text hide-on-med-and-down">Rsos</span></a>
		<a class="navbar-toggler" href="javascript:(0)"><i class="material-icons">radio_button_checked</i></a></h1><BR><BR>
	</div>
	
	<ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="slide-out" data-menu="menu-navigation" data-collapsible="accordion">
		@php
			$menus =  array(
				'persoanl_details',
				'address_details',
				'bank_details',
				'document_details',
				'admission_subject_details',
				'toc_subject_details',
				'exam_subject_details',
				'fee_details',
				'view_details',
				'preview_details',
				'registration'
			);
		@endphp
		@if($resultCount > 1)
			<li>
				<span>@include('elements.changerole')</span></a>
			</li>
		@endif
		
		<br>

		<li>
			<span>@include('elements.appsessionyear')</span>
		</a>
		</li>
		<br>
		
	
		@if(in_array("update_my_profile",$permissions))
			<li class="bold">
				<a class="{{ Request::routeIs('update_my_profile') ? 'active' : '' ? 'active' : ''}}" href="{{route('update_my_profile')}}"><i class="material-icons"> local_library</i><span class="menu-title" data-i18n="Mail">My Profile</span></a>
			</li>
		@endif


		@if(in_array("all_dashboard",$permissions))
			<li class="{{ Request::routeIs('deodashboard') ? 'active' : '' || Request::routeIs('applicationdashboard') ? 'active' : '' || Request::routeIs('admindashboard') ? 'active' : '' || Request::routeIs('studentdashboard') ? 'active' : '' || Request::routeIs('collegedashboard') ? 'active' : '' ||
				Request::routeIs('printerdashboard') ? 'active' : '' ||
				Request::routeIs('sessionaldashboard') ? 'active' : '' ||
				Request::routeIs('aicenterdashboard') ? 'active' : '' ||
				Request::routeIs('examcenterashboard') ? 'active' : '' ||
				Request::routeIs('practicalexaminerdashboard') ? 'active' : '' ||
				Request::routeIs('examination_department') ? 'active' : '' ||
				Request::routeIs('secrecydashboard') ? 'active' : '' ||
				Request::routeIs('evaluationdashboard') ? 'active' : '' || Request::routeIs('users.*') ? 'active' : '' || Request::routeIs('theroyexaminerdashboard') ? 'active' : '' || Request::routeIs('colleges.*') ? 'active' : '' || Request::routeIs('deodashboard') ? 'active' : '' || Request::routeIs('permissions.*') ? 'active' : ''
				
				}}">
				<a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Mail">My Dashboard </span></a>
					<div class="collapsible-body">
						<ul class="collapsible collapsible-sub" data-collapsible="accordion">
								@if(in_array("application_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('applicationdashboard') ? 'active' : '' }}" href="{{route('applicationdashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Super Admin Dashboard </span></a>
							</li>
							@endif
							
							@if(in_array("admin_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('admindashboard') ? 'active' : '' }}" href="{{route('admindashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Admin Dashboard</span></a>
								</li>
							@endif
							
							@if(in_array("student_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('studentdashboard') ? 'active' : '' }}" href="{{route('studentdashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard"> Student Dashboard</span></a>
							</li>
							@endif
							
							@if(in_array("college_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('collegedashboard') ? 'active' : '' }}" href="{{route('collegedashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">College Dashboard</span></a>
							</li>
							@endif
							
							@if(in_array("printerdashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('printerdashboard') ? 'active' : '' }}" href="{{route('printerdashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Printer Dashboard</span></a>
							</li>
						@endif

							@if(in_array("sessional_student_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('sessionaldashboard') ? 'active' : '' }}" href="{{route('sessionaldashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Sessional Dashboard</span></a>
							</li>
							@endif

							@if(in_array("aicenter_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('aicenterdashboard') ? 'active' : '' }}" href="{{route('aicenterdashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Ai Center Dashboard</span></a>
							</li>
							@endif

							@if(in_array("examcenter_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('examcenterashboard') ? 'active' : '' }}" href="{{route('examcenterashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Exam Center Dashboard</span></a>
							</li>
							@endif
							
							@if(in_array("practical_examiner_dashboard",$permissions))
								<li class="active bold"><a class="{{ Request::routeIs('practicalexaminerdashboard') ? 'active' : '' }}" href="{{route('practicalexaminerdashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard" style="font-size:12px;">
									Practical Exam Dashboard
								</span></a>
								</li>
							@endif

							@if(in_array("examination_department_dashboard",$permissions))
								<li class="active bold"><a class="{{ Request::routeIs('examination_department') ? 'active' : '' }}" href="{{route('examination_department')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard" style="font-size:14px;">
									Exam. Dept. Dashboard
								</span></a>
								</li>
							@endif

							@if(in_array("Secrecy_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('secrecydashboard') ? 'active' : '' }}" href="{{route('secrecydashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Secrecy Dashboard</span></a>
							</li>
							@endif
							
							@if(in_array("evaluation_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('evaluationdashboard') ? 'active' : '' }}" href="{{route('evaluationdashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Evaluation Dashboard</span></a>
							</li>
							@endif


							@if(in_array("theroyexaminer_dashboard",$permissions))
								<li class="active bold"><a class="{{ Request::routeIs('theroyexaminerdashboard') ? 'active' : '' }}" href="{{route('theroyexaminerdashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard" style="font-size:12px;">
									Theory Exam Dashboard
								</span></a>
								</li>
							@endif
							

							@if(in_array("deo_dashboard",$permissions))
							<li class="active bold"><a class="{{ Request::routeIs('deodashboard') ? 'active' : '' }}" href="{{route('deodashboard')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">DEO Dashboard</span></a>
							</li>
							@endif 
						</ul>
					</div>
			</li>
		@endif
			
			
		








		
		@if(in_array("reg_premission",$permissions) && (in_array("student_registration",$permissions) || in_array("supp_find_enrollment",$permissions)))
		<li class="{{  Request::routeIs('supp_find_enrollment') ? 'active' : '' || request()->routeIs(@$menus) ? 'active' : ''}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">contacts</i><span class="menu-title" data-i18n="Pages">Registrations</span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
				
		@if(@$selected_session == Config::get('global.form_current_admission_session_id') || $role_id == Config::get('global.super_admin_id'))

			@if(in_array("student_registration",$permissions))
			<li class="bold"><a class="{{ request()->routeIs(@$menus) ? 'active' : '' }}" href="{{route('registration')}}"><i class="material-icons">group_add</i><span class="menu-title" data-i18n="Mail">Student Registration</span></a>
			</li>
			@endif
		@endif 
		
		
		@if(@$selected_session == Config::get('global.form_supp_current_admission_session_id') || $role_id == Config::get('global.super_admin_id'))
			@if($showSuppStatus == true)
				@if(in_array("supp_find_enrollment",$permissions))
					<li class="bold">
					<a class="{{ Request::routeIs('supp_find_enrollment') ? 'active' : ''|| Request::routeIs('supp_preview_details') ? 'active' : ''|| Request::routeIs('supp_preview_details') ? 'active' : ''}}" href="{{route('supp_find_enrollment')}}">
						<i class="material-icons">assignment_ind</i>
						<span class="menu-title" data-i18n="Dashboard">
							Supplementary Reg.
						</span>
					</a>
				</li>
				@endif
			@endif
		@endif 
		
			</ul>
		</div>
		</li>
		@endif
		
		@if(in_array("sessional_find_enrollment",$permissions))
		<li class="bold"><a class="{{ Request::routeIs('find_enrollment') ? 'active' : ''|| Request::routeIs('marks_details') ? 'active' : ''|| Request::routeIs('marks_preview_details') ? 'active' : ''}}" href="{{route('find_enrollment')}}"><i class="material-icons"> local_library</i><span class="menu-title" data-i18n="Mail">Sessional Marks</span></a>
		</li>
		@endif
		
		@if(in_array("deolisting",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('deo') ? 'active' : '' }} || {{ request()->routeIs('deocreate') ? 'active' : '' }} || {{ request()->routeIs('deoedit') ? 'active' : '' }} || {{ request()->routeIs('deoshow') ? 'active' : '' }}" href="{{route('deo')}}"><i class="material-icons">import_contacts</i><span class="menu-title" data-i18n="Mail">DEO Details</span></a>
		</li>
	@endif
		
	

	@if(in_array("all_master",$permissions))
	

	<li class="{{ Request::routeIs('reports.*') ? 'active' : '' || Request::routeIs('examdateindex') ? 'active' : '' || Request::routeIs('districts.*') ? 'active' : '' || Request::routeIs('logslisting') ? 'active' : '' || Request::routeIs('tabletableslisting') ? 'active' : '' ||
	Request::routeIs('all_listing') ? 'active' : '' ||
	Request::routeIs('queryediter') ? 'active' : '' ||
	Request::routeIs('querysqldump') ? 'active' : '' ||
	Request::routeIs('schoollisting') ? 'active' : '' ||
	Request::routeIs('backupdblisting') ? 'active' : '' ||
	Request::routeIs('getpublishaicentermaterial') ? 'active' : '' ||
	Request::routeIs('alldocumentlist') ? 'active' : '' ||
	Request::routeIs('aicenterusers.*') ? 'active' : '' || Request::routeIs('students.*') ? 'active' : '' || Request::routeIs('colleges.*') ? 'active' : '' || Request::routeIs('roles.*') ? 'active' : '' || Request::routeIs('permissions.*') ? 'active' : '' ||
	Request::routeIs('users.*') ? 'active' : ''||Request::routeIs('subjects.*') ? 'active' : ''
	
	
	
	}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">accessibility_new</i><span class="menu-title" data-i18n="Mail">Masters</span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
			@if(in_array("Aicenter-list",$permissions))
		<li class="bold">
			<a class="{{ Request::routeIs('aicenterusers.*') ? 'active' : ''}}" href="{{route('aicenterusers.index')}}">
				<i class="material-icons">person_outline</i>
				<span class="menu-title" data-i18n="Dashboard">
					Aicenter
				</span>
			</a>
		</li>
		@endif
		@if(in_array("role_and_premission",$permissions))
		<li class="{{ Request::routeIs('users.*') ? 'active' : '' || Request::routeIs('students.*') ? 'active' : '' || Request::routeIs('colleges.*') ? 'active' : '' || Request::routeIs('roles.*') ? 'active' : '' || Request::routeIs('permissions.*') ? 'active' : '' || Request::routeIs('districts.*') ? 'active' : '' || Request::routeIs('subjects.*') ? 'active' : ''
		}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">contacts</i><span class="menu-title" data-i18n="Pages">Role and permission </span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
			@if(in_array("user-list",$permissions))
			<li class=""><a href="{{route('users.index')}}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">Users</span></a>
			</li>
			@endif
		@if(in_array("role-list",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{route('roles.index')}}"><i class="material-icons">person_add</i><span class="menu-title" data-i18n="Mail">Roles</span></a>
		</li>
			@endif
			@if(in_array("permission-list",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{route('permissions.index')}}"><i class="material-icons">lock_open</i><span class="menu-title" data-i18n="Mail">Permissions</span></a>
		</li>
		@endif
			@if(in_array("college-list",$permissions))
			<li><a href="{{route('colleges.index')}}" class="{{ request()->routeIs('colleges.*') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search">Colleges</span></a>
			</li>
			@endif
			</ul>
		</div>
		</li>
		@endif
			@if(in_array("query-editer",$permissions))
			<li class="bold"><a class="{{ request()->routeIs('queryediter') ? 'active' : '' }}" href="{{route('queryediter')}}"><i class="material-icons">edit_calendar</i><span class="menu-title" data-i18n="Mail">Query Editor</span></a>
			</li>
			<li class="bold"><a class="{{ request()->routeIs('querysqldump') ? 'active' : '' }}" href="{{route('querysqldump')}}"><i class="material-icons">edit_calendar</i><span class="menu-title" data-i18n="Mail">Query SQL Dump</span></a>
			</li>
			
			@endif

			
		@if(in_array("report-backupdb",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('backupdblisting') ? 'active' : '' }}" href="{{route('backupdblisting')}}"><i class="material-icons">filter</i><span class="menu-title" data-i18n="Mail">Mysql Backup</span></a>
		</li>
		@endif
		
		@if(in_array("report-list",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{route('reports.index')}}"><i class="material-icons">add_box</i><span class="menu-title" data-i18n="Mail">Master Queries</span></a>
		</li>
		@endif

		@if(in_array("examdate_index",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('examdateindex') ? 'active' : '' }}" href="{{route('examdateindex')}}"><i class="material-icons">date_range</i><span class="menu-title" data-i18n="Mail">Exam Dates Details</span></a>
		</li>
		@endif
		
		@if(in_array("district-list",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('districts.*') ? 'active' : '' }}" href="{{route('districts.index')}}"><i class="material-icons">location_city</i><span class="menu-title" data-i18n="Mail">Districts</span></a>
		</li>
		@endif 
		@if(in_array("subjects-list",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{route('subjects.index')}}"><i class="material-icons">location_city</i><span class="menu-title" data-i18n="Mail">Subjects</span></a>
		</li>
		@endif 
		@if(in_array("logslisting",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('logslisting') ? 'active' : '' }}" href="{{route('logslisting')}}"><i class="material-icons">import_contacts</i><span class="menu-title" data-i18n="Mail">Logs Details</span></a>
		</li>
		@endif 
		@if(in_array("timetables-list",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('tabletableslisting') ? 'active' : '' }}" href="{{route('tabletableslisting')}}"><i class="material-icons">schedule</i><span class="menu-title" data-i18n="Mail">Time Tables</span></a>
		</li>
		@endif

		@if(in_array("school-list",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('schoollisting') ? 'active' : '' }}" href="{{route('schoollisting')}}"><i class="material-icons">layers</i><span class="menu-title" data-i18n="Mail">School Details</span></a>
		</li>
		@endif 
			@if(in_array("publish_material",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('getpublishaicentermaterial') ? 'active' : '' }}" href="{{route('getpublishaicentermaterial')}}"><i class="material-icons">layers</i><span class="menu-title" data-i18n="Mail">Publish Exam Material </span></a>
		</li>
		@endif
			@if(in_array("examcenter_listing",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('all_listing') ? 'active' : '' }}" href="{{route('all_listing')}}"><i class="material-icons">content_paste</i><span class="menu-title" data-i18n="Mail" title="Exam Center Allotment">Center Allotment</span></a>
	</li>
	@endif
	@if(in_array("logdebug",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('logdebug') ? 'active' : '' }}" href="{{route('logdebug')}}"><i class="material-icons">layers</i><span class="menu-title" data-i18n="Mail">Log Debug</span></a>
		</li>
		@endif 
		@if(in_array("alldocumentlist",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('alldocumentlist') ? 'active' : '' }}" href="{{route('alldocumentlist')}}"><i class="material-icons">control_point_duplicate</i><span class="menu-title" data-i18n="Mail">All document Upload</span></a>
		</li>
		@endif
			</ul>
		</div>
		</li>
		@endif
		
		@if(in_array("updatetions_premission",$permissions))
		<li class="{{ Request::routeIs('searchstudentdetail') ? 'active' : '' || Request::routeIs('Serach_Enrollment') ? 'active' : '' || Request::routeIs('resultupdate') ? 'active' : ''}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">account_box</i><span class="menu-title" data-i18n="Pages">Updations </span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
			@if(in_array("result_update",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('resultupdate') ? 'active' : ''  || Request::routeIs('editresult') ? 'active' : ''  }}" href="{{route('resultupdate')}}"><i class="material-icons">assignment return</i><span class="menu-title" data-i18n="Mail">Result Update</span></a>
		</li>
		@endif
		@if(in_array("searchstudentdata",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('searchstudentdetail') ? 'active' : '' ||  request()->routeIs('printupdatestudentdetalis') ? 'active' : '' }}" href="{{route('searchstudentdetail')}}"><i class="material-icons">assignment return</i><span class="menu-title" data-i18n="Mail">Update Student Details</span></a>
		</li>
		@endif
		@if(in_array("pastdataupdate",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('Serach_Enrollment') ? 'active' : '' ||  request()->routeIs('pastdataupdate') ? 'active' : '' }}" href="{{route('Serach_Enrollment')}}"><i class="material-icons">assignment return</i><span class="menu-title" data-i18n="Mail">Update Pastdata</span></a>
		</li>
		@endif
			</ul>
		</div>
		</li>
		@endif
		
		
		@if(in_array("all_Material",$permissions))
		<li class="{{ Request::routeIs('nominalnrgenerateview') ? 'active' : '' || Request::routeIs('hallticketbulkviews') ? 'active' : '' || Request::routeIs('hallticketbulkenrollmentviews') ? 'active' : '' || Request::routeIs('single_exam_center_nominal_roll_pdf_view') ? 'active' : '' || Request::routeIs('single_exam_center_attendance_roll_pdf_view') ? 'active' : ''|| Request::routeIs('single_exam_center_theorynominal_roll_pdf_view') ? 'active' : ''|| Request::routeIs('single_exam_center_practicalnominal_roll_pdf_view') ? 'active' : ''
		|| Request::routeIs('single_exam_center_theorysignaturenominal_roll_pdf_view') ? 'active' : ''
		|| Request::routeIs('single_exam_center_practicalsignaturenominal_roll_pdf_view') ? 'active' : ''
	}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">assignment_ind</i><span class="menu-title" data-i18n="Mail">Materials</span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
		@if(in_array("aicentermateriel",$permissions))
		<li class="{{ Request::routeIs('nominalnrgenerateview') ? 'active' : ''|| Request::routeIs('hallticketbulkviews') ? 'active' : '' ||  Request::routeIs('hallticketbulkenrollmentviews') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">assignment_returned</i><span class="menu-title" data-i18n="Pages">AI Center Material </span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
			@if(in_array("aicodenominalnr",$permissions))
			<li class=""><a href="{{route('nominalnrgenerateview')}}" class="{{ request()->routeIs('nominalnrgenerateview') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">AI Center Nominal Roll </span></a>
			</li>
			@endif
			@if(in_array("aicodehallticket",$permissions))
			<li><a href="{{route('hallticketbulkviews')}}" class="{{ request()->routeIs('hallticketbulkviews') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search">AI Center Hall Ticket</span></a>
			</li>
			@endif

			<!--   @if(in_array("aicodehallticket",$permissions))
			<li><a href="{{route('hallticketbulkenrollmentviews')}}" class="{{ request()->routeIs('hallticketbulkenrollmentviews') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search"> Single Ai Center Hall Ticket</span></a>
			</li>
			@endif -->
			</ul>
		</div>
		</li>
		@endif
	@if(in_array("examcentermateriel",$permissions))
		<li class="{{ Request::routeIs('single_exam_center_nominal_roll_pdf_view') ? 'active' : '' || Request::routeIs('single_exam_center_attendance_roll_pdf_view') ? 'active' : '' || Request::routeIs('single_exam_center_theorynominal_roll_pdf_view') ? 'active' : '' || Request::routeIs('single_exam_center_practicalnominal_roll_pdf_view') ? 'active' : '' || Request::routeIs('single_exam_center_theorysignaturenominal_roll_pdf_view') ? 'active' : '' || Request::routeIs('single_exam_center_practicalsignaturenominal_roll_pdf_view') ? 'active' : ''}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">move_to_inbox</i><span class="menu-title" data-i18n="Pages">Exam Center Materiel </span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
			@if(in_array("aicodenominalnr",$permissions))
			<li class=""><a href="{{route('single_exam_center_nominal_roll_pdf_view')}}" class="{{ request()->routeIs('single_exam_center_nominal_roll_pdf_view') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact"> Nominal Roll </span></a>
			</li>
			@endif
			@if(in_array("aicodenominalnr",$permissions))
			<li class=""><a href="{{route('single_exam_center_attendance_roll_pdf_view')}}" class="{{ request()->routeIs('single_exam_center_attendance_roll_pdf_view') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact"> Attendance Roll  </span></a>
			</li>
			@endif
			@if(in_array("aicodenominalnr",$permissions))
			<li class=""><a href="{{route('single_exam_center_theorynominal_roll_pdf_view')}}" class="{{ request()->routeIs('single_exam_center_theorynominal_roll_pdf_view') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact"> Theory Roll </span></a>
			</li>
			@endif
			@if(in_array("aicodenominalnr",$permissions))
			<li class=""><a href="{{route('single_exam_center_practicalnominal_roll_pdf_view')}}" class="{{ request()->routeIs('single_exam_center_practicalnominal_roll_pdf_view') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">practical Roll </span></a>
			</li>
			@endif
			@if(in_array("aicodenominalnr",$permissions))
			<li class=""><a href="{{route('single_exam_center_practicalsignaturenominal_roll_pdf_view')}}" class="{{ request()->routeIs('single_exam_center_practicalsignaturenominal_roll_pdf_view') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">Practical Signature Roll </span></a>
			</li>
			@endif
			@if(in_array("aicodenominalnr",$permissions))
			<li class=""><a href="{{route('single_exam_center_theorysignaturenominal_roll_pdf_view')}}" class="{{ request()->routeIs('single_exam_center_theorysignaturenominal_roll_pdf_view') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">Theory Signature Roll </span></a>
			</li>
			@endif
			
			</ul>
		</div>
		</li>
		@endif
		
			</ul>
		</div>
		</li>
		@endif


		<!-- 
	@if(in_array("examcenter_listing",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('enrollment_fixcode_views') ? 'active' : '' }}" href="{{route('enrollment_fixcode_views')}}"><i class="material-icons">content_paste</i><span class="menu-title" data-i18n="Mail" title="Exam Center Allotment">Fix code Right</span></a>
	</li>
	@endif -->
	
	


	
	

		@if(in_array("deo_listing",$permissions) || in_array("practicals",$permissions) || in_array("practicalexaminercreate",$permissions)  || in_array("practical_examiner_list",$permissions))
			<li class="{{ Request::routeIs('practicals') ? 'active' : '' || Request::routeIs('deo') ? 'active' : '' || Request::routeIs('practicalexaminer') ? 'active' : '' || Request::routeIs('practicalexaminercreate') ? 'active' : ''}}">
				
			<a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">list</i><span class="menu-title" data-i18n="Pages">Practical Section</span></a>

			<div class="collapsible-body">
				<ul class="collapsible collapsible-sub" data-collapsible="accordion">
					@if(in_array("deo_listing",$permissions))
						<li class="">
							<a href="{{route('deo')}}" class="{{ request()->routeIs('deo') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
								<span data-i18n="Contact">
									DEO
								</span>
							</a>
						</li>
					@endif  
					@if(in_array("practical_examiner_list",$permissions))
						<li class="">
							<a href="{{route('practicalexaminer')}}" class="{{ request()->routeIs('practicalexaminer') ? 'active' : '' }} || {{ request()->routeIs('practicalexaminercreate') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
								<span data-i18n="Contact">
									Practical Examiner
								</span>
							</a>
						</li>
					@endif  

					@if(in_array("practicals",$permissions))
						<li class="">
							<a href="{{route('practicals')}}" class="{{ request()->routeIs('practicals') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
								<span data-i18n="Contact">
									Practical Marks
								</span>
							</a>
						</li>
					@endif  
				</ul>
			</div>
		</li>
	@endif

	@if(in_array("theory_examiner_map_listing",$permissions))
		<li class="{{ Request::routeIs('mapping_examiners') ? 'active' : '' || Request::routeIs('mapping_examiners.add') ? 'active' : ''    || Request::routeIs('alloting_copies_examiners') ? 'active' : '' || Request::routeIs('alloting_copies_examiners.add') ? 'active' : '' || request()->routeIs('marking_absents') ? 'active' : '' || request()->routeIs('marking_absents.edit') ? 'active' : '' || request()->routeIs('marking_absents.add') ? 'active' : ''  || request()->routeIs('alloting_copies_examiners' ) ? 'active' : '' || request()->routeIs('alloting_copies_examiners.add' ) ? 'active' : '' || request()->routeIs('alloting_copies_examiners.edit' ) ? 'active' : '' || request()->routeIs('marking_absents.view') ? 'active' : '' || 
		request()->routeIs('therory_mark_submissions') ? 'active' : ''}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">business</i><span class="menu-title" data-i18n="Pages">Theory Section</span></a>
		<div class="collapsible-body">
			<ul class="collapsible collapsible-sub" data-collapsible="accordion">
			@if(in_array("theory_examiner_add",$permissions))
			<li class=""><a href="{{route('mapping_examiners')}}" class="{{ request()->routeIs('mapping_examiners') ? 'active' : '' || request()->routeIs('mapping_examiners.add') ? 'active' : '' || request()->routeIs('mapping_examiners.edit') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">Theory Examiner</span></a>
			</li>
			@endif
			@if(in_array("marking_absent",$permissions))
			<li><a href="{{route('marking_absents')}}" class="{{ request()->routeIs('marking_absents') ? 'active' : '' || request()->routeIs('marking_absents.edit') ? 'active' : '' || request()->routeIs('marking_absents.add') ? 'active' : '' || request()->routeIs('marking_absents.view') ? 'active' : ''  }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search">Marking Absent</span></a>
			</li>
			@endif
			@if(in_array("alloting_copies",$permissions))
			<li><a href="{{route('alloting_copies_examiners')}}" class="{{ request()->routeIs('alloting_copies_examiners' ) ? 'active' : '' || request()->routeIs('alloting_copies_examiners.add' ) ? 'active' : '' || request()->routeIs('alloting_copies_examiners.edit' ) ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search">Allotting Copies Examiner</span></a>
			</li>
			@endif
			@if(in_array("therory_mark_submission",$permissions))
			<li><a href="{{route('therory_mark_submissions')}}" class="{{ request()->routeIs('therory_mark_submissions') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search">Add Theory Marks</span></a>
			</li>
			@endif
			</ul>
		</div>
		</li>
		@endif



	
			@if(in_array("examcenter_material",$permissions))
				@if($role_id == Config::get('global.Examcenter') && $getdatamastersexam->option_val == "true")
		<li class="bold"><a class="{{ request()->routeIs('examcenter_material') ? 'active' : '' }}" href="{{route('examcenter_material')}}"><i class="material-icons">import_contacts</i><span class="menu-title" data-i18n="Mail">Exam Center material </span></a>
		</li>
		@endif
	@endif

	@include('elements.dom.appleft_reports')








	
	</ul>
	<div class="navigation-background"></div><a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
</aside>
	 