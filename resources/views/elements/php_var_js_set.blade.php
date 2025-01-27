<?php 
$APP_URL = Config::get("global.APP_URL"); 
?>
<script>
var APP_URL = "{{ $APP_URL }}";
  var config = {
      data: {
          formId: "{{ @$model  }}"
      },
      routes: {
        district_by_state_id: "{{ route('district_by_state_id'); }}",
        set_current_session: "{{ route('set_current_session'); }}",
        set_student_multi_enrollment: "{{ route('set_student_multi_enrollment'); }}",
        set_current_role: "{{ route('set_current_role'); }}",
        get_tehsil_by_district_id: "{{ route('get_tehsil_by_district_id'); }}",
        get_block_by_district_id: "{{ route('get_block_by_district_id'); }}",
        practicalexaminer_destroy: "{{ route('practicalexaminer_destroy'); }}",
        checkAddressValidation: "{{ route('checkAddressValidation'); }}",
        ajaxTocValidation: "{{ route('ajaxTocValidation'); }}",
        ajaxVerificationTrailValidation: "{{ route('ajaxVerificationTrailValidation'); }}",
        ajaxviewenrollments: "{{ route('ajaxviewenrollments'); }}",
        ajaxSubjectValidation: "{{ route('ajaxSubjectValidation'); }}",
        ajaxshowPassFieldToc: "{{ route('ajaxshowPassFieldToc'); }}",
        ajaxExamSubjectValidation: "{{ route('ajaxExamSubjectValidation'); }}",
        applications_report: "{{ route('applications'); }}",
        application_ai_center_wise_count: "{{ route('application_ai_center_wise_count'); }}",
        ajaxSessinalMarksValidation: "{{ route('ajaxSessinalMarksValidation'); }}",
        getStudentsCount: "{{ route('getStudentsCount'); }}",
        getStudentsCountForExamcenter: "{{ route('getStudentsCountForExamcenter'); }}",
        ajaxPersonalDetilasValidation: "{{ route('ajaxPersonalDetilasValidation'); }}",
        getmonthlabel:"{{ route('getmonthlabel'); }}",
        previousqualificationget:"{{ route('previousqualificationget'); }}",
        getdisadvantagegroup:"{{ route('getdisadvantagegroup'); }}",
        ajaxRevalRTISubmission:"{{ route('ajaxRevalRTISubmission'); }}",
        ajaxPersonaladmtype:"{{ route('ajaxPersonaladmtype'); }}",
        ajaxPersonalborad:"{{ route('ajaxPersonalborad'); }}",
        ajaxRsosFailYearsList: "{{ route('ajaxRsosFailYearsList'); }}",
        ajaxIsPracticalSubject: "{{ route('ajaxIsPracticalSubject'); }}",
        ajaxIsVerifyTocEnrollemnt: "{{ route('ajaxIsVerifyTocEnrollemnt'); }}",
        get_states: "{{ route('get_states'); }}",
        get_subject_faculty_wise: "{{ route('get_subject_faculty_wise'); }}",
        ajaxRegistrationDetilasValidation:"{{ route('ajaxRegistrationDetilasValidation'); }}",
        ajaxUserDetilasValidation:"{{ route('ajaxUserDetilasValidation'); }}",
        ajaxSuppFindEnrollmentValidation :"{{ route('ajaxSuppFindEnrollmentValidation'); }}",
        ajaxSuppSubjectValidation :"{{ route('ajaxSuppSubjectValidation'); }}",
        ajaxSuppSubjectdocumentValidation :"{{ route('ajaxSuppSubjectdocumentValidation'); }}",
        ajaxCoursesubjects :"{{ route('ajaxCoursesubjects'); }}",
        ajaxexamcentercode :"{{ route('ajaxexamcentercode'); }}",
        ajaxGetSSOIDDetials :"{{ route('ajaxGetSSOIDDetials'); }}",
        getsubjects :"{{ route('getsubjects'); }}",
        ajaxMappingExaminerValidation :"{{ route('ajaxMappingExaminerValidation'); }}",
        ajaxCheckSsoAlreadyExamCenter :"{{ route('ajaxCheckSsoAlreadyExamCenter'); }}",
        get_appearing_student_count :"{{ route('get_appearing_student_count'); }}",
        getAppearingStudent :"{{ route('getAppearingStudent'); }}",
        getSSOIDDetialsByMappingExaminerTbl :"{{ route('getSSOIDDetialsByMappingExaminerTbl'); }}",
        getDataMarkingAbsentStudent :"{{ route('getDataMarkingAbsentStudent'); }}",
        ajaxAllotingCopiesExaminerValidation :"{{ route('ajaxAllotingCopiesExaminerValidation'); }}",
        ajaxCourseExamcenters :"{{ route('ajaxCourseExamcenters'); }}", 
        ajaxPracticalExaminerValidation :"{{ route('ajaxPracticalExaminerValidation'); }}", 
        ajaxMarkingAbsentValidation :"{{ route('ajaxMarkingAbsentValidation'); }}", 
        ajaxPracticalValidation :"{{ route('ajaxPracticalValidation'); }}", 
        getTheoryExaminer :"{{ route('getTheoryExaminer'); }}",
        ajaxMarkSubmmisionsValidation :"{{ route('ajaxMarkSubmmisionsValidation'); }}",
        ajaxAddPracticalValidation :"{{ route('ajaxAddPracticalValidation'); }}",
        ajaxEditPracticalValidation :"{{ route('ajaxEditPracticalValidation'); }}",
        ajaxAddDeoValidation :"{{ route('ajaxAddDeoValidation'); }}",
        ajaxEditDeoValidation :"{{ route('ajaxEditDeoValidation'); }}",
        getDeoListByDistrictId :"{{ route('getDeoListByDistrictId'); }}",
        ajaxCoursesubjectsfixcode :"{{ route('ajaxCoursesubjectsfixcode'); }}",
        ajaxCourseExamcentersfixcode :"{{ route('ajaxCourseExamcentersfixcode'); }}",
        getallotssoid:"{{ route('getallotssoid'); }}",
        checkMarkingAbsentdata:"{{ route('checkMarkingAbsentdata'); }}",
        checkresultstudent:"{{ route('checkresultstudent'); }}",
        checkresultstudentold:"{{ route('checkresultstudentold'); }}",
        ajaxRevalObtainedMarksSubjectValidationAndSubmission:"{{ route('ajaxRevalObtainedMarksSubjectValidationAndSubmission'); }}",
        ajaxGenerateCaptcha:"{{ route('ajaxGenerateCaptcha'); }}",
        pastDataUpdataValidation:"{{ route('pastDataUpdataValidation'); }}",
        checksessionaltudent:"{{ route('checksessionaltudent'); }}",
        updateStudentDetailsPrintValidation:"{{ route('updateStudentDetailsPrintValidation'); }}",
        checkstudentpastdata:"{{ route('checkstudentpastdata'); }}",
        searchstudentdata:"{{ route('searchstudentdata'); }}",
        getstudentexamsubjectdata:"{{route('getstudentexamsubjectdata'); }}",
        finalresultupdatevalidation:"{{route('finalresultupdatevalidation');}}",
        updateStudentSubjectsDataValidation:"{{route('updateStudentSubjectsDataValidation');}}",
        AddsubjectValidation:"{{route('AddsubjectValidation');}}",
        getstudentsubjectdata:"{{route('getstudentsubjectdata');}}",
        ajaxAicenterDetilasValidation:"{{route('ajaxAicenterDetilasValidation');}}",
        ajaxMyProfileAicenterDetilasValidation:"{{route('ajaxMyProfileAicenterDetilasValidation');}}",
        livetableupdate:"{{route('livetableupdate');}}",
        ajaxqueryValidation:"{{route('ajaxqueryValidation');}}",
        pastSubjectDataUpdataValidation:"{{route('pastSubjectDataUpdataValidation');}}",
        ajaxsubjectsDetilasValidation:"{{route('ajaxsubjectsDetilasValidation');}}",
        grpahicalGetApplicationdata:"{{route('grpahicalGetApplicationdata');}}",
        htmlGrpahicalData:"{{route('htmlGrpahicalData');}}",
        ajaxBooksRequrementDetilasValidation:"{{route('ajaxBooksRequrementDetilasValidation');}}",
        checkPublishBookdata:"{{route('checkPublishBookdata');}}",
        expectedstudentcountdata:"{{ route('expectedstudentcountdata'); }}",
        setPagntorValue:"{{ route('setPagntorValue'); }}",
        ajaxFacultySubjectValidation: "{{ route('ajaxFacultySubjectValidation'); }}",
        getblock :"{{ route('getblock'); }}",
        getTempblock :"{{ route('getTempblock'); }}",
        getaicode :"{{ route('getaicode'); }}",
        gettempaicode :"{{ route('gettempaicode'); }}",
        AjaxSelfRegistrationValidation :"{{ route('AjaxSelfRegistrationValidation'); }}",
        blockgetaicode :"{{ route('blockgetaicode'); }}",
        getdistrictaicenter:"{{ route('getdistrictaicenter'); }}",
        getTempdistrictaicenter:"{{ route('getTempdistrictaicenter'); }}",
        ajaxallreadystudentDetilasValidation:"{{ route('ajaxallreadystudentDetilasValidation'); }}",
        ajaxUpdateSsoDetilasValidations:"{{ route('ajaxUpdateSsoDetilasValidations'); }}",
        ajaxchecktopdetail:"{{ route('ajaxchecktopdetail'); }}",
        setCountDownTimerDetails:"{{ route('setCountDownTimerDetails'); }}",
        checkcaptchastudent:"{{ route('checkcaptchastudent'); }}",
        ajaxViewCreateDate:"{{ route('ajaxViewCreateDate'); }}", 
        send_otp_to_student:"{{ route('send_otp_to_student'); }}", 
        _verify_only_mobile_student_otp:"{{ route('_verify_only_mobile_student_otp'); }}",
        getListBanksIfscCode:"{{ route('getListBanksIfscCode'); }}",
        getBankBranchDetails:"{{ route('getBankBranchDetails'); }}",
        ajaxDocumentVerificationValidation:"{{route('ajaxDocumentVerificationValidation');}}",
        ajaxRevalSubjectValidation:"{{route('ajaxRevalSubjectValidation');}}",
        ajaxcreatepracticalexaminerslotValidation:"{{route('ajaxcreatepracticalexaminerslotValidation');}}",
        updatePraticalMarks:"{{route('updatePraticalMarks');}}",
		ajaxrejecteddocumentDetilasValidation:"{{route('ajaxrejecteddocumentDetilasValidation');}}",
		get_volume_by_subject:"{{route('get_volume_by_subject');}}",
		ajaxCorrectionMarksheetValidation:"{{route('ajaxCorrectionMarksheetValidation')}}",
		ajaxDgsAddStudensValidation:"{{route('ajaxDgsAddStudensValidation')}}",
		getSubModuleList:"{{route('getSubModuleList')}}",
		update_ajax_single_screen_details:"{{route('update_ajax_single_screen_details')}}",
		sdloginOTP:"{{route('sdloginOTP')}}",
      }	
  }
  
  @if (Session::has('message'))
  toastr.options =
  {
  "debug": false,
  "onclick": null,
  "progressBar" : true,
  "fadeIn": 500,
  "fadeOut": 1000,
  "timeOut": 1000,
  "extendedTimeOut": 500
  }
  toastr.success("{{ session('message') }}");
  @endif

  @if(Session::has('error'))
  
  toastr.options =
  {
    "closeButton" : false,
    "progressBar" : true,
    "positionClass": "toast-top-full-width",
  }
    toastr.error("{{ session('error') }}");
  @endif

  @if(Session::has('info'))
  toastr.options =
  {
    "closeButton" : false,
    "progressBar" : true
  }
        toastr.info("{{ session('info') }}");
  @endif

  @if(Session::has('warning'))
  toastr.options =
  {
    "closeButton" : false,
    "progressBar" : true
  }
  toastr.warning("{{ session('warning') }}");
  @endif 

</script>