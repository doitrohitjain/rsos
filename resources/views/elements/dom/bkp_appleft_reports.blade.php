@php
$menus =  array(
	'students.index',
	'student_applications',
	'studentfeeorgsummary',
	'studentfeesummary',
	'student_fees',
	'subject_wise_student_count',
	'student_application_ai_center_wise_count',
	'deletestudent',
	'listing_payment_issues',
	'supp_masterstudent',
	'supplementary_subject_wise_student_count',
	'supp_listing_payment_issues',
	'supplementarieaicenterwisecount',
	'supplementariefeesreport',
	'supplementary_student_applications',
	'studentchecklists',
	'tocChecklists',
	'SupplementaryChecklists',
	'allcenterwisetocchecklist',
	'boardnrstudentenrollmentview',
	'nominalnr',
	'reportexamcenterwise',
	'aicodewisesubjectsdatastudents',
	'aicodewisesubjectsdatastudentsmediumtype',
	'aicodewisesubjectsdataallotmentstudentsdata',
	'excenterlisting',
	'sessional_report_h',
	'theory_report_examiner_mapping',
	'theory_report_student_wise',
	'practical_report_examiner_mapping',
	'practical_report_student_wise',
	'aicodewisesubjectsdatastudentsall',
	'aicodewisesubjectsdatastudentsAndSupp',
	'administrative_custom_report',
	'administrative_custom_document_report',
	'downloadBulkDocument',
	'resultsprocess',
	'printupdatestudentdetalis',
	'get_toppers',
	'serachnerollment',
	'printduplicatemarksheetcertificate',
	'subject_wise_student_count_steams',
	'direct_provisional_results'
	
);
@endphp


<!-- Testing pending Start -->
	<!-- @if(in_array("all_Material",$permissions))
		<li class="bold">
			<a class="" href="{{route('administrative_material_download')}}"> 
				<i class="material-icons"> account_box</i><span class="menu-title" data-i18n="Mail">Materials Download</span>
			</a>
		</li>
	@endif  -->
	@if(in_array("developer_all_reports",$permissions))
		<li class="bold">
			<a class="" href="{{route('listings')}}"> 
				<i class="material-icons"> account_box</i><span class="menu-title" data-i18n="Mail">Developer Reports</span>
			</a>
		</li>
	@endif  
	@if(false && in_array("all_reports",$permissions)  || in_array("examination_reportexamcenterwise",$permissions))
		<li class="bold">
			<a class="" href="{{route('front_view')}}"> 
				<i class="material-icons"> account_box</i><span class="menu-title" data-i18n="Mail">All Reports</span>
			</a>
		</li>
	@endif
<!-- Testing pending End  -->

    @if(in_array("all_reports",$permissions)  || in_array("examination_reportexamcenterwise",$permissions))
 
     <li class="{{ request()->routeIs(@$menus) ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">photo_filter</i><span class="menu-title" data-i18n="Basic UI">Reports</span></a>
          <div class="collapsible-body">
            <ul class="collapsible collapsible-sub" data-collapsible="accordion">
			@if(in_array("all_student_reports",$permissions))
              <li class="{{ Request::routeIs('subject_wise_student_count_steams') ? 'active' : ''  || Request::routeIs('student_applications') ? 'active' : '' || Request::routeIs('subject_wise_student_count') ? 'active' : '' || Request::routeIs('direct_provisional_results') ? 'active' : ''  || Request::routeIs('student_application_ai_center_wise_count') ? 'active' : '' || Request::routeIs('deletestudent') ? 'active' : '' || Request::routeIs('listing_payment_issues') ? 'active' : '' || Request::routeIs('studentfeeorgsummary') ? 'active' : ''  || Request::routeIs('studentfeesummary') ? 'active' : '' || Request::routeIs('student_fees') ? 'active' : ''  || Request::routeIs('aicodewisesubjectsdatastudentsall') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">face</i>
			  <span class="menu-title" data-i18n="face">Students</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
				@if(in_array("student-list",$permissions))
				<li><a href="{{route('students.index')}}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}"><i class="material-icons">accessibility</i><span data-i18n="Blog"> Master Students</span></a>
				</li>
				@endif
				@if(in_array("admission_report_student_applications",$permissions))
				<li class=""><a href="{{route('student_applications')}}" class="{{ request()->routeIs('student_applications') ? 'active' : '' }}"><i class="material-icons">group_add</i>
				<span data-i18n="Contact">Student Applications</span></a>
				</li>
			     @endif
				 @if(in_array("aicodewisesubjectsdatastudentsall",$permissions))
				<li class="bold"><a href="{{route('aicodewisesubjectsdatastudentsall')}}" class="{{ request()->routeIs('aicodewisesubjectsdatastudentsall') ? 'active' : '' }}"><i class="material-icons">person_outline</i>
				<span data-i18n="Contact">Student Wise Subjects</span></a>
				</li>
				@endif
				 @if(in_array("all_admissionfees_reports",$permissions))
				 <li class="{{ Request::routeIs('studentfeeorgsummary') ? 'active' : ''  || Request::routeIs('studentfeesummary') ? 'active' : '' || Request::routeIs('student_fees') ? 'active' : ''}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">image_aspect_ratio</i><span data-i18n="Buttons">Admission Fees</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("examination_report_student_fees_Org_Summary",$permissions))
					<li><a href="{{route('studentfeeorgsummary')}}" class="{{ request()->routeIs('studentfeeorgsummary') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Search">Student Fees Org </span></a>
					</li>
					@endif
					@if(in_array("examination_report_student_fees_Summary",$permissions))
					<li><a href="{{route('studentfeesummary')}}" class="{{ request()->routeIs('studentfeesummary') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Search">Student Fees Summary</span></a>
					</li>
					@endif
					@if(in_array("examination_report_student_fees",$permissions))
					<li><a href="{{route('student_fees')}}" class="{{ request()->routeIs('student_fees') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Search">Student Fees </span></a>
					</li>
					@endif
                  </ul>
                </div>
              </li>
			  @endif
				@if(in_array("allotmentsubject_wise_student_count",$permissions))
				<li><a href="{{route('subject_wise_student_count')}}" class="{{ request()->routeIs('subject_wise_student_count') ? 'active' : '' }}"><i class="material-icons">person_outline</i>
				<span data-i18n="Blog">Subject Wise Student</span></a>
				</li>
				@endif
				@if(in_array("subject_wise_student_count",$permissions))
				<li><a href="{{route('subject_wise_student_count_steams')}}" class="{{ request()->routeIs('subject_wise_student_count_steams') ? 'active' : '' }}"><i class="material-icons">person_outline</i>
				<span data-i18n="Blog">Subject Wise Student</span></a>
				</li>
				@endif
				@if(in_array("admission_report_student_application_ai_center_wise_count",$permissions))
				<li><a href="{{route('student_application_ai_center_wise_count')}}" class="{{ request()->routeIs('student_application_ai_center_wise_count') ? 'active' : '' }}"><i class="material-icons">cast</i>
				<span data-i18n="Blog">AI Center Wise Counts</span></a>
				</li>
				@endif
				@if(in_array("deactive_students",$permissions))
				<li><a href="{{route('deletestudent')}}" class="{{ request()->routeIs('deletestudent') ? 'active' : '' }}"><i class="material-icons">filter_tilt_shift</i>
				<span data-i18n="Blog">Deactive Students</span></a>
				</li>
				@endif
				@if(in_array("all_Payment_reports",$permissions))
				<li  class="{{ Request::routeIs('listing_payment_issues') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">settings_brightness</i><span class="menu-title" data-i18n="Buttons">Payments</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("listing_payment_issues",$permissions))
					<li class=""><a href="{{route('listing_payment_issues')}}" class="{{ request()->routeIs('listing_payment_issues') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Contact">Payment Issues</span></a>
					</li>
					@endif
                  </ul>
                </div>
                 </li>
                  </ul>
                </div>
              </li>
			  @endif
			  @endif
			  @if(in_array("all_Supplementary_reports",$permissions))
			  <li class="{{ Request::routeIs('supp_masterstudent') ? 'active' : '' || Request::routeIs('supplementary_subject_wise_student_count') ? 'active' : '' || Request::routeIs('supp_listing_payment_issues') ? 'active' : '' || Request::routeIs('supplementarieaicenterwisecount') ? 'active' : '' || Request::routeIs('supplementariefeesreport') ? 'active' : '' || Request::routeIs('supplementary_student_applications') ? 'active' : '' }}" ><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">photo_filter</i><span data-i18n="Buttons">Supplementary</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("student-list",$permissions))
					<li><a href="{{route('supp_masterstudent')}}" class="{{ request()->routeIs('supp_masterstudent') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Blog"> Supp Master Students</span></a>
					</li>
					@endif
					@if(in_array("supplementary_subject_wise_student_count",$permissions))
					<li><a href="{{route('supplementary_subject_wise_student_count')}}" class="{{ request()->routeIs('supplementary_subject_wise_student_count') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Blog">Supp.Subject Wise count</span></a>
					</li>
					@endif
					@if(in_array("supp_listing_payment_issues",$permissions))
					<li class="bold"><a href="{{route('supp_listing_payment_issues')}}" class="{{ request()->routeIs('supp_listing_payment_issues') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Contact">Supp. Payment Issues</span></a>
					</li>
					@endif
					@if(in_array("examination_reportsupplementarieaicenterwisecount",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('supplementarieaicenterwisecount') ? 'active' : '' }}" href="{{route('supplementarieaicenterwisecount')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Supp. Aicenter wise count </span></a>
					</li>
					@endif
					@if(in_array("examination_reportsupplementariefeesreport",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('supplementariefeesreport') ? 'active' : '' }}" href="{{route('supplementariefeesreport')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Supplementary Fees </span></a>
					</li>
					@endif
					@if(in_array("supplementary_student_applications",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('supplementary_student_applications') ? 'active' : '' }}" href="{{route('supplementary_student_applications')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Supplementary Report</span></a>
					</li>
					@endif
                  </ul>
                </div>
              </li>
			  @endif
			  @if(in_array("all_results",$permissions))
		<li class="{{ Request::routeIs('downloadBulkDocument') ? 'active' : '' ||   Request::routeIs('direct_provisional_results') ? 'active' : '' ||   Request::routeIs('resultsprocess') ? 'active' : '' || Request::routeIs('printupdatestudentdetalis') ? 'active' : '' ||  Request::routeIs('get_toppers') ? 'active' : '' ||   Request::routeIs('serachnerollment') ? 'active' : '' || Request::routeIs('printduplicatemarksheetcertificate') ? 'active' : ''  }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">star</i><span class="menu-title" data-i18n="Mail">All Results </span></a>
          <div class="collapsible-body">
            <ul class="collapsible collapsible-sub" data-collapsible="accordion">
			
		
		@if(in_array("serachenrollment",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('serachnerollment') ? 'active' : '' ||  request()->routeIs('printduplicatemarksheetcertificate') ? 'active' : '' }}" href="{{route('serachnerollment')}}"><i class="material-icons">assignment return</i><span class="menu-title" data-i18n="Mail">Download Marksheets</span></a>
        </li>
		@endif
		
		@if(in_array("direct_provisional_results",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('direct_provisional_results') ? 'active' : '' ||  request()->routeIs('direct_provisional_results') ? 'active' : '' }}" href="{{route('direct_provisional_results')}}"><i class="material-icons">assignment return</i><span class="menu-title" data-i18n="Mail">Provisional Marksheets</span></a>
        </li>
		@endif

		@if(in_array("result_process_steps",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('resultsprocess') ? 'active' : '' ||  request()->routeIs('resultsprocess') ? 'active' : '' }}" href="{{route('resultsprocess')}}"><i class="material-icons">assignment return</i><span class="menu-title" data-i18n="Mail">Result Process</span></a>
        </li>
		@endif

		@if(in_array("gettoppers",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('get_toppers') ? 'active' : '' ||  request()->routeIs('get_toppers') ? 'active' : '' }}" href="{{route('get_toppers')}}"><i class="material-icons">assignment return</i><span class="menu-title" data-i18n="Mail">Get Toppers</span></a>
        </li>
		@endif
		
		@if(in_array("bulk_download",$permissions))
		<li class="bold"><a class="{{ request()->routeIs('downloadBulkDocument') ? 'active' : '' ||  request()->routeIs('downloadBulkDocument') ? 'active' : '' }}" href="{{route('downloadBulkDocument')}}"><i class="material-icons">AI center wise marksheets</i><span class="menu-title" data-i18n="Mail">Bulk Document</span></a>
        </li>
		@endif

            </ul>
          </div>
        </li>
		@endif
			  @if(in_array("all_checklist_reports",$permissions))
               <li class="{{ Request::routeIs('studentchecklists') ? 'active' : '' || Request::routeIs('tocChecklists') ? 'active' : '' || Request::routeIs('SupplementaryChecklists') ? 'active' : '' || Request::routeIs('allcenterwisetocchecklist') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">crop_original</i><span data-i18n="Buttons">Checklists</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("examination_report_studentchecklists",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('studentchecklists') ? 'active' : '' }}" href="{{route('studentchecklists')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Student checklist</span></a>
					</li>
					@endif
					@if(in_array("examination_report_tocchecklists",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('tocChecklists') ? 'active' : '' }}" href="{{route('tocChecklists')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Toc checklist</span></a>
					</li>
					@endif
					@if(in_array("examination_report_supplementarychecklists",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('SupplementaryChecklists') ? 'active' : '' }}" href="{{route('SupplementaryChecklists')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Supp checklist</span></a>
					</li>
					@endif
					@if(in_array("examination_report_allcenterwisetocchecklist",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('allcenterwisetocchecklist') ? 'active' : '' }}" href="{{route('allcenterwisetocchecklist')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">ALL Toc checklist</span></a>
					</li>
					@endif
                  </ul>
                </div>
              </li>
			  @endif
			 
			  
			  
			  @if(in_array("administrativeall",$permissions))
			  <li class="{{ Request::routeIs('administrative_custom_report') ? 'active' : '' || Request::routeIs('administrative_custom_document_report') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">photo_filter</i><span data-i18n="Buttons">Administrative Reports </span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
				 @if(in_array("administrative_custom_report",$permissions))
              <li class=""><a href="{{route('administrative_custom_report')}}" class="{{ request()->routeIs('administrative_custom_report') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Contact">Administrative Report</span></a>
              </li>
			   @endif
				 @if(in_array("administrative_custom_document_report",$permissions))
              <li><a href="{{route('administrative_custom_document_report')}}" class="{{ request()->routeIs('administrative_custom_document_report') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i><span data-i18n="Search">Administrative Document</span></a>
              </li>
			  @endif
                  </ul>
                </div>
              </li>
			  @endif
			  @if(in_array("all_NR_reports",$permissions))
			  <li class="{{ Request::routeIs('boardnrstudentenrollmentview') ? 'active' : '' || Request::routeIs('nominalnr') ? 'active' : '' || Request::routeIs('reportexamcenterwise') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">photo_filter</i><span data-i18n="Buttons">NR Reports</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
				@if(in_array("examination_reportboardnrstudentenrollmentview",$permissions))
				<li class="bold"><a class="{{ request()->routeIs('boardnrstudentenrollmentview') ? 'active' : '' }}" href="{{route('boardnrstudentenrollmentview')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Board NR Reports </span></a>
				</li>
				@endif
				@if(in_array("examination_reportnominalnr",$permissions))
				<li class="bold"><a class="{{ request()->routeIs('nominalnr') ? 'active' : '' }}" href="{{route('nominalnr')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail">Report Nominal NR</span></a>
				</li>
				@endif
					@if(in_array("examination_reportexamcenterwise",$permissions))
					<li class="bold"><a class="{{ request()->routeIs('reportexamcenterwise') ? 'active' : '' }}" href="{{route('reportexamcenterwise')}}"><i class="material-icons">radio_button_unchecked</i><span class="menu-title" data-i18n="Mail" style="" title="BIG Strength Nr/center allotment report">BIG Strength NR </span></a>
					</li>
					@endif
                  </ul>
                </div>
              </li>
			  @endif
			  @if(in_array("all_aicenter_reports",$permissions))
			  <li class="{{ Request::routeIs('aicodewisesubjectsdatastudents') ? 'active' : '' || Request::routeIs('aicodewisesubjectsdatastudentsmediumtype') ? 'active' : '' || Request::routeIs('aicodewisesubjectsdataallotmentstudentsdata') ? 'active' : '' || Request::routeIs('aicodewisesubjectsdatastudentsAndSupp') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">dvr</i><span data-i18n="Buttons">Aicenter Wise Count</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
				@if(in_array("examination_reportsupplementarieaicenterwisecount",$permissions))
				<li><a href="{{route('aicodewisesubjectsdatastudents')}}" class="{{ request()->routeIs('aicodewisesubjectsdatastudents') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
				<span>Ai-Sub Fresh</span></a>
				</li>
				@endif
				@if(in_array("examination_reportsupplementarieaicenterwisecount",$permissions))
				<li><a href="{{route('aicodewisesubjectsdatastudentsmediumtype')}}" class="{{ request()->routeIs('aicodewisesubjectsdatastudentsmediumtype') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
				<span >Ai-Sub-Medium Fresh</span></a>
				</li>
				@endif
				@if(in_array("examination_reportsupplementarieaicenterwisecount",$permissions))
				<li ><a href="{{route('aicodewisesubjectsdataallotmentstudentsdata')}}" class="{{ request()->routeIs('aicodewisesubjectsdataallotmentstudentsdata') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
				<span>Ai-Sub Alloted Stud.</span></a>
				</li>
				@endif
				@if(in_array("examination_reportsupplementarieaicenterwisecount",$permissions))
				<li><a href="{{route('aicodewisesubjectsdatastudentsAndSupp')}}" class="{{ request()->routeIs('aicodewisesubjectsdatastudentsAndSupp') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
				<span>AI-Sub Eligible Stud.</span></a>
				</li>
				@endif
                  </ul>
                </div>
              </li>
			  @endif
			  @if(in_array("all_examcenters_reports",$permissions))
			  <li class="{{ Request::routeIs('excenterlisting') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">format_list_bulleted</i><span data-i18n="Buttons">Examcenters</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("examcenter_listing",$permissions))
					<li><a href="{{route('excenterlisting')}}" class="{{ request()->routeIs('excenterlisting') ? 'active' : '' }}">
					<i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Search">Examcenters Lists</span></a>
					</li>
					@endif
                  </ul>
                </div>
              </li>
			  @endif
			  @if(in_array("all_Sessional_reports",$permissions))
			  <li class="{{ Request::routeIs('sessional_report_h') ? 'active' : '' }}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">chrome_reader_mode</i><span data-i18n="Buttons">Sessional</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("examination_report_sessional_report_h",$permissions))
					<li><a href="{{route('sessional_report_h',['stream' => Config::get('global.current_exam_month_id')])}}" class="{{ request()->routeIs('sessional_report_h') ? 'active' : '' }}">
					<i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Search">Sessional Report</span></a>
					</li>
					@endif
                  </ul>
                </div>
              </li>
               @endif
			  @if(in_array("all_Theory_reports",$permissions))
			  <li class="{{ Request::routeIs('theory_report_examiner_mapping') ? 'active' : ''  || Request::routeIs('theory_report_student_wise') ? 'active' : ''}}"><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">import_contacts</i><span data-i18n="Buttons">Theory</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("theory_examiner_report",$permissions))
					<li><a href="{{route('theory_report_examiner_mapping')}}" class="{{ request()->routeIs('theory_report_examiner_mapping') ? 'active' : '' }}">
					<i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Search">Theory Examiners</span></a>
					</li>
					@endif
					@if(in_array("theory_student_wise_report",$permissions))
					<li><a href="{{route('theory_report_student_wise')}}" class="{{ request()->routeIs('theory_report_student_wise') ? 'active' : '' }}">
					<i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Search">Theory Students</span></a>
					</li>
					@endif
                  </ul>
                </div>
              </li>
			  @endif
			  @if(in_array("all_practical_reports",$permissions))
			  <li><a class="collapsible-header waves-effect waves-cyan" href="JavaScript:void(0)"><i class="material-icons">receipt</i><span data-i18n="Buttons">Practical</span></a>
                <div class="collapsible-body">
                  <ul class="collapsible" data-collapsible="accordion">
					@if(in_array("practical_examiner_report",$permissions))
					<li class=""><a href="{{route('practical_report_examiner_mapping')}}" class="{{ request()->routeIs('practical_report_examiner_mapping') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Contact">Practical Examiners</span></a>
					</li>
					@endif
					@if(in_array("practical_student_wise_report",$permissions))
					<li class=""><a href="{{route('practical_report_student_wise')}}" class="{{ request()->routeIs('practical_report_student_wise') ? 'active' : '' }}"><i class="material-icons">radio_button_unchecked</i>
					<span data-i18n="Contact">Practical Students</span></a>
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