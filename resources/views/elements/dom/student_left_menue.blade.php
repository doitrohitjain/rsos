@php 
$ssoid = Auth::guard('student')->user()->ssoid;
$student_id = Auth::guard('student')->user()->id;
use App\Helper\CustomHelper; 
$student_multi_enrollments = CustomHelper::_getEnrollmentListLabeleMappedWithSSOId(@$ssoid);
$routeName = CustomHelper::getWhichRouteRedirect(@$student_id);
$resCount = count($student_multi_enrollments);
$studentallowornot=session::get('studentallowornot');
@endphp
@if($role_id == $student_role && $resCount > 0)
    <li>
        <span>@include('elements.student_multi_enrollment_selection')</span>
    </li>
@endif

@if(@$studentallowornot)
@if(in_array("student_myprofile",$permissions))
<li class="active bold"><a class="{{ (Request::routeIs('student_history_details') ? 'active' : '') }}" 
href="{{ route('student_history_details',Crypt::encrypt(@$student_id)) }}"><i class="material-icons">group_add</i><span class="menu-title" data-i18n="Dashboard"> My Profile</span></a>
</li>
@endif
@endif

<li class="active bold">
    <a class="{{ (Request::routeIs('studentsdashboards') ? 'active' : '') }}" href="{{route('studentsdashboards')}}"><i class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Dashboard">Dashboard</span>
    </a>
</li>


@if(in_array("student_self_Registration",$permissions))
@if(@$routeName)
    <li class="active bold">
        <a class="{{ (Request::routeIs('persoanl_details') ? 'active' : '') || (Request::routeIs('address_details') ? 'active' : '') || (Request::routeIs('bank_details') ? 'active' : '') || (Request::routeIs('document_details') ? 'active' : '') || (Request::routeIs('admission_subject_details') ? 'active' : '') || (Request::routeIs('toc_subject_details') ? 'active' : '') ||(Request::routeIs('exam_subject_details') ? 'active' : '') || (Request::routeIs('fee_details') ? 'active' : '') || (Request::routeIs('preview_details') ? 'active' : '') || (Request::routeIs('view_details') ? 'active' : '')}}" href="{{ @$routeName }}"><i class="material-icons">group_add</i><span class="menu-title" data-i18n="Dashboard">  Registration 
        </span>
        </a>
    </li>
@endif 
@endif

@can('student_self_Supplementary')
{{-- <li class="active bold"><a class="{{ (Request::routeIs('supp_subjects_details') ? 'active' : '') || (Request::routeIs('supp_fees_details') ? 'active' : '') || (Request::routeIs('supp_preview_details') ? 'active' : '')}}" href="{{ route('supp_subjects_details',Crypt::encrypt(@$student_id)) }}"><i class="material-icons">group_add</i><span class="menu-title" data-i18n="Dashboard"> Supplementary</span></a>
</li> --}}
@endcan


	