<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Component\PracticalCustomComponent;
use App\Exports\ExamcenterMappingExlExport;
use App\Exports\PracticalExaminerListExport;
use App\Helper\CustomHelper;
use App\Models\Course;
use App\Models\ExamcenterDetail;
use App\Models\ModelHasRole;
use App\Models\StudentAllotmentMark;
use App\Models\Subject;
use App\Models\User;
use App\Models\UserExaminerMap;
use App\Models\UserPracticalExaminer;
use Auth;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Response;
use Session;
use Validator;


class PracticalExaminerController extends Controller
{
    function __construct()
    {
        // $this->middleware('permission:examiner_mapping_delete', ['only' => ['practicalexaminerdestory']]);
        // $this->middleware('permission:examcenter_downloadExamCenterExl', ['only' => ['downloadExamCenterMappingExl']]);
    }

    public function practicalexaminerdestory($id)
    {
        $id = Crypt::decrypt($id);
        $updateUserExaminerMap = UserExaminerMap::where('id', $id)->first();
        $fld = "id";
        if (!@$updateUserExaminerMap->$fld) {
            return redirect()->route('examiner_mapping_list')->with('error', 'Failed! Examiner Mapping detials not found.');
        }
        $studentMarksCount = StudentAllotmentMark::where('user_examiner_map_id', $id)->count();


        if ($studentMarksCount > 0) {
            $studentMarks = StudentAllotmentMark::where('user_examiner_map_id', $id)->pluck('id', 'id');
            $dataUpdateCounter = 0;
            foreach ($studentMarks as $k => $innerId) {
                $saveData = array();
                $saveData['practical_absent'] = 0;
                $saveData['user_examiner_map_id'] = 0;
                $saveData['user_deo_id'] = 0;
                $saveData['deo_district_id'] = 0;
                $saveData['practical_min_marks'] = 0;
                $saveData['practical_max_marks'] = 0;
                $saveData['final_practical_marks'] = null;
                $saveData['practical_absent'] = 0;
                $saveData['practical_examiner_id'] = 0;
                $saveData['practical_examiner_district_id'] = 0;
                $saveData['user_practical_examiner_id'] = 0;
                $saveData['updated_at'] = date("Y-m-d H:i:s");
                $saveData['is_update_practical_marks_admin'] = null;
                $result = StudentAllotmentMark::where('id', $innerId)->update($saveData);
                $dataUpdateCounter++;
            }
        }
        $deleteUserExaminerMap = UserExaminerMap::where('id', $id)->delete();
        if (@$deleteUserExaminerMap) {
            return redirect()->route('examiner_mapping_list')->with('message', 'Examiner Mapping successfully deleted.');
        } else {
            return redirect()->route('examiner_mapping_list')->with('error', 'Failed! Student not Deactive');
        }
    }

    public function index(Request $request)
    {
        $combo_name = 'exam_session';
        $exam_month_session = $this->master_details($combo_name);
        $combo_name = 'admission_sessions';
        $exam_year_session = $this->master_details($combo_name);
        $roles = $this->_getRoles();
        $conditions = array();
        $practicalexaminerrole = config("global.practicalexaminer");
        $role_id = Session::get('role_id');
        if ($role_id != config("global.secrecy_admin")) {
            $conditions["model_has_roles.role_id"] = $practicalexaminerrole;
        }
        $title = "Practical Examiner Details";
        $table_id = "Practical_Examiner_Details";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadPracticalExaminerListExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadPracticalExaminerListPdf',
                'status' => false
            )
        );
        $filters = array(
            array(
                "lbl" => "SSoid",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => "SSOID",
                'dbtbl' => 'users',
            ),
            // array(
            // 	"lbl" => "Exam Year Type",
            // 	'fld' => 'exam_year',
            // 	'input_type' => 'select',
            // 	'options' => $exam_year_session,
            // 	'placeholder' => 'Exam Year',
            // 	'dbtbl' => 'users',
            // ),
            // array(
            // 	"lbl" => "Exam Month Type",
            // 	'fld' => 'exam_month',
            // 	'input_type' => 'select',
            // 	'options' => $exam_month_session,
            // 	'placeholder' => 'Exam Month',
            // 	'dbtbl' => 'users',
            // ),
            array(
                "lbl" => "School Name",
                'fld' => 'college_name',
                'input_type' => 'text',
                'placeholder' => "School Name",
                'dbtbl' => 'users',
            )
        );
        if ($request->all()) {
            $inputs = $request->all();
            foreach ($filters as $ik => $iv) {
                if (!empty($iv['dbtbl']) && isset($inputs[$iv['fld']]) && !empty($inputs[$iv['fld']])) {
                    $conditions[$iv['dbtbl'] . "." . $iv['fld']] = $inputs[$iv['fld']];
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);


        // $custom_component_obj = new CustomComponent;
        // $data = $custom_component_obj->getUsersData($formId,true);

        $practical_component_obj = new PracticalCustomComponent;
        $data = $practical_component_obj->getDeoExaminerListOrCount($formId);
        return view('practicalexaminers.index', compact('data', 'breadcrumbs', 'exportBtn', 'title', 'filters', 'exam_year_session', 'exam_month_session'));
    }

    public function view(Request $request, $id)
    {
        $title = "Practical Examiner View";
        $e_id = $id;
        $id = Crypt::decrypt($e_id);
        $master = User::find($id);
        return view('practicalexaminers.view', compact('title', 'master'));
    }

    public function edit(Request $request, $id)
    {
        $e_id = $id;
        $id = Crypt::decrypt($e_id);

        $title = "Practical Examiner Details";
        $table_id = "Practical_Examiner_Details";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $role_id = Session::get('role_id');
        $routeDashboard = "";
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $user = User::find($id);
        $district_list = $this->districtsByState(6);
        $practical_custom_component_obj = new PracticalCustomComponent();
        $deo_list = $practical_custom_component_obj->getWithoutAjaxDeoListByDistrictId($user->district_id);
        $custom_component_obj = new CustomComponent();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        $selected_deo_arr = User::Join('user_practical_examiners', 'users.id', '=', 'user_practical_examiners.user_id')
            ->where('user_practical_examiners.user_id', '=', $user->id)
            ->get(['user_practical_examiners.user_deo_id']);
        $selected_deo = 0;
        if (isset($selected_deo_arr[0]->user_deo_id) && !empty($selected_deo_arr[0]->user_deo_id)) {
            $selected_deo = $selected_deo_arr[0]->user_deo_id;
        }

        if (count($request->all()) > 0) {
            if ($isAdminStatus == true) {
                $user_deo_id = @$request->deo_id;
                $examiner_district_id = @$request->district_id;
            } else {
                $user_deo_id = @Auth::user()->id;
                $examiner_district_id = @Auth::user()->district_id;
            }

            if ($isAdminStatus == true && empty($request->district_id)) {
                return redirect()->back()->with('error', 'District is required field')->withInput($request->all());
            }

            if ($isAdminStatus == true && empty($request->deo_id)) {
                return redirect()->back()->with('error', 'DEO Name is required field')->withInput($request->all());
            }

            $User = new User; /// create model object
            $validator = Validator::make($request->all(), $User->updaterules);
            //here we check this ssoid is already with this session or not other then current user id.
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $userCount = User::where('ssoid', '=', $request->ssoid)
                ->where('id', '!=', $user->id)
                // ->where('exam_year', '=', $user->exam_year)
                // ->where('exam_month', '=', $user->exam_month)
                ->count();
            if ($userCount > 0) {
                $fld = "ssoid";
                $errMsg = "SSOID already used by somewhere.";
                $validator->getMessageBag()->add($fld, $errMsg);
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $input = $request->all();
            $roles = Config::get("global.practicalexaminer");
            $password = Hash::make('123456789');
            $current_admission_session_id = Config::get("global.current_admission_session_id");
            $current_exam_month_id = Config::get("global.current_exam_month_id");
            $userarray = [
                'college_name' => $request->college_name, 'mobile' => $request->mobile,
                'ssoid' => $request->ssoid, 'email' => $request->email,
                'password' => $password, 'name' => $request->name,
                // 'exam_year' => $current_admission_session_id,
                // 'exam_month' => $current_exam_month_id,
                'district_id' => $examiner_district_id
            ];
            $user = User::where('id', $id)->update($userarray);


            $user_practical_examiners_arr = [
                'user_deo_id' => $user_deo_id
            ];
            $user_practical_examiners = UserPracticalExaminer::where('user_id', $id)->update($user_practical_examiners_arr);

            if ($user) {
                return redirect()->route('practicalexaminer')->with('message', 'Practical Examiner successfully updated');
            } else {
                return redirect()->route('practicalexaminer')->with('error', 'Failed! Practical Examiner not updated');
            }
        }
        return view('practicalexaminers.edit', compact('selected_deo', 'title', 'user', 'breadcrumbs', 'district_list', 'deo_list', 'isAdminStatus'));
    }

    public function add(Request $request)
    {
        $title = "Practical Examiner Add";
        $routeDashboard = "";
        $role_id = Session::get('role_id');
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $district_list = $this->districtsByState(6);
        $deo_list = array();
        $custom_component_obj = new CustomComponent();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();

        if (count($request->all()) > 0) {
            if ($isAdminStatus == true) {
                $user_deo_id = @$request->deo_id;
                $examiner_district_id = @$request->district_id;
            } else {
                $user_deo_id = @Auth::user()->id;
                $examiner_district_id = @Auth::user()->district_id;
            }

            if ($isAdminStatus == true && empty($request->district_id)) {
                return redirect()->back()->with('error', 'District is required field')->withInput($request->all());
            }

            if ($isAdminStatus == true && empty($request->deo_id)) {
                return redirect()->back()->with('error', 'DEO Name is required field')->withInput($request->all());
            }

            $current_admission_session_id = Config::get("global.current_admission_session_id");
            $current_exam_month_id = Config::get("global.current_exam_month_id");
            $current_stream_id = Config::get("defaultStreamId");
            $practicalexaminer = Config::get('global.practicalexaminer');

            $ssoAlreadyWithSameDistrictExist = DB::table('users')
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->join('user_practical_examiners', 'user_practical_examiners.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('users.ssoid', $request->ssoid)
                ->where('user_practical_examiners.user_deo_id', $user_deo_id)
                ->where('model_has_roles.role_id', $practicalexaminer)
                ->where('user_practical_examiners.exam_year', $current_admission_session_id)
                ->where('user_practical_examiners.exam_month', $current_exam_month_id)
                ->whereNull('user_practical_examiners.deleted_at')
                ->whereNull('users.deleted_at')
                ->count();
            if (@$ssoAlreadyWithSameDistrictExist > 0) {
                return redirect()->back()->with('error', 'SSO ID already exist with practical examiner role with same district.')->withInput($request->all());
            }

            $objAjaxController = new AjaxController();

            $ssoAlreadyPracticalRoleExist = DB::table('users')
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('users.ssoid', $request->ssoid)
                ->where('model_has_roles.role_id', $practicalexaminer)
                // ->where('users.exam_year',$current_admission_session_id)
                // ->where('users.exam_month',$current_exam_month_id)
                ->whereNull('users.deleted_at')
                ->get(['users.id']);
            if (isset($ssoAlreadyPracticalRoleExist[0]->id) && !empty($ssoAlreadyPracticalRoleExist[0]->id)) {
                $userpracticalexaminer = UserPracticalExaminer::create(['user_id' => $ssoAlreadyPracticalRoleExist[0]->id, 'user_deo_id' => $user_deo_id, 'exam_year' => $current_admission_session_id, 'exam_month' => $current_exam_month_id, 'stream' => $current_stream_id]);

                $userarray = [
                    'college_name' => $request->college_name, 'email' => $request->email, 'name' => $request->name, 'mobile' => $request->mobile
                ];
                $user = User::where('id', $ssoAlreadyPracticalRoleExist[0]->id)->update($userarray);

                if ($userpracticalexaminer) {
                    return redirect()->route('practicalexaminer')->with('message', 'Practical Examiner successfully added.');
                }
            }

            $ssoAlreadyExistWithAnyRole = DB::table('users')
                //->join('model_has_roles','model_has_roles.model_id', '=','users.id')
                //->join('roles','roles.id', '=','model_has_roles.role_id')
                ->where('users.ssoid', $request->ssoid)
                //->where('model_has_roles.role_id','!=',$practicalexaminer)
                // ->where('users.exam_year',$current_admission_session_id)
                // ->where('users.exam_month',$current_exam_month_id)
                ->whereNull('users.deleted_at')
                ->get(['users.id']);
            if (isset($ssoAlreadyExistWithAnyRole[0]->id) && !empty($ssoAlreadyExistWithAnyRole[0]->id)) {
                $userpracticalexaminer = UserPracticalExaminer::create(['user_id' => $ssoAlreadyExistWithAnyRole[0]->id, 'user_deo_id' => $user_deo_id, 'exam_year' => $current_admission_session_id, 'exam_month' => $current_exam_month_id, 'stream' => $current_stream_id]);

                $userarray = [
                    'college_name' => $request->college_name, 'email' => $request->email, 'name' => $request->name, 'mobile' => $request->mobile
                ];
                $user = User::where('id', $ssoAlreadyExistWithAnyRole[0]->id)->update($userarray);

                $model_has_role_data = array();
                $model_has_role_data['role_id'] = $practicalexaminer;
                $model_has_role_data['model_type'] = 'App\Models\User';
                $model_has_role_data['model_id'] = $ssoAlreadyExistWithAnyRole[0]->id;
                $ModelHasRoleObj = ModelHasRole::insert($model_has_role_data);

                if ($userpracticalexaminer) {
                    return redirect()->route('practicalexaminer')->with('message', 'Practical Examiner successfully created');
                }
            } else { // if users ssoid does not exists new user create [users,userparticalexaminer,maodel has role]
                $password = Hash::make('123456789');
                $userarray = ['college_name' => $request->college_name, 'mobile' => $request->mobile, 'ssoid' => $request->ssoid, 'email' => $request->email, 'password' => $password, 'name' => $request->name, 'exam_year' => $current_admission_session_id, 'exam_month' => $current_exam_month_id, 'district_id' => $examiner_district_id];
                $user = User::create($userarray);

                // $user->assignRole(['roles' => $practicalexaminer]);
                $model_has_role_data = array();
                $model_has_role_data['role_id'] = $practicalexaminer;
                $model_has_role_data['model_type'] = 'App\Models\User';
                $model_has_role_data['model_id'] = $user->id;
                $ModelHasRoleObj = ModelHasRole::insert($model_has_role_data);
                $userpracticalexaminer = UserPracticalExaminer::create(['user_id' => $user->id, 'user_deo_id' => $user_deo_id, 'exam_year' => $current_admission_session_id, 'exam_month' => $current_exam_month_id, 'stream' => $current_stream_id]);
                if ($userpracticalexaminer) {
                    return redirect()->route('practicalexaminer')->with('message', 'Practical Examiner successfully created');
                }
            }

        }
        return view('practicalexaminers.add', compact('title', 'breadcrumbs', 'district_list', 'deo_list', 'isAdminStatus'));
    }

    public function examiner_mapping(Request $request, $user_id)
    {
        $current_exam_year = Config::get("global.admission_academicyear_id");
        $current_exam_month = Config::get("global.current_exam_month_id");

        $title = "Examiner Mapping";
        $page_title = $title;
        $e_user_id = $user_id;
        $user_id = Crypt::decrypt($e_user_id);
        // @dd($user_id);
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ''
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $conditions1 = ['combo_name' => 'admission_sessions', 'option_id' => Config::get('global.current_admission_session_id')];
        $conditions2 = ['combo_name' => 'exam_session', 'option_id' => Config::get('global.current_exam_month_id')];
        $exam_year = DB::table('masters')->where($conditions1)->pluck('option_val', 'option_id');
        $exam_sessions = DB::table('masters')->where($conditions2)->pluck('option_val', 'option_id');
        $practicalCustomComponent = new practicalCustomComponent();

        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);
        $examcenter_datails_dropdown = array();
        $subjects_dropdown = array();

        $practical_examiner_data = DB::table('users')->where('id', $user_id)->first();
        if (empty($practical_examiner_data)) {
            return redirect()->back()->with('error', 'Failed! SSO ID does not mappped.');
        }

        $routeUrl = "UserExaminerMap";
        if (count($request->all()) > 0) {
            $inputs = $request->all();

            if (!empty($inputs['course'])) {
                $examcenter_datails_dropdown = $practicalCustomComponent->getCourseExamCenters($inputs['course']);
            }
            $modelObj = new UserExaminerMap;
            $validator = Validator::make($inputs, $modelObj->practicalExaminerValidation);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->with($examcenter_datails_dropdown)->withInput($request->all());
            }

            $practicalCustomComponent = new practicalCustomComponent();
            $student_exist = $practicalCustomComponent->getPracticalStudentList($request->examcenter_detail, $request->subject);
            $tempArr = $student_exist->toArray();
            if (isset($tempArr['total'])) {
                if ($tempArr['total'] <= 0) {
                    return redirect()->back()->with('error', 'Failed! Student not found in given combination.');
                }
            }

            $practical_custom_component_obj = new PracticalCustomComponent;
            // $alreadyMappingExist  = $practical_custom_component_obj->getAlreadyExaminerMapping($practical_examiner_data->id,$request->examcenter_detail,$request->course,$request->subject);
            $alreadyMappingExist = $practical_custom_component_obj->getAlreadyExaminerMapping($request->examcenter_detail, $request->course, $request->subject);
            if (isset($alreadyMappingExist) && @$alreadyMappingExist > 0) {
                return redirect()->back()->with('error', 'Same subject already mapped on same exam center.')->withInput($request->all());
            }

            $custom_component_obj = new CustomComponent;
            $sso_data = $custom_component_obj->getSSOIDDetials($request->ssoid);
            $sso_data = json_decode($sso_data);
            $stream = Config::get('global.defaultStreamId');

            $svdata = array();
            $svdata['course'] = $request->course;
            $svdata['examcenter_detail_id'] = $request->examcenter_detail;
            $svdata['subject_id'] = $request->subject;
            $svdata['user_deo_id'] = Auth::User()->id;
            // $svdata['practical_examiner_id']= $user_id;
            $svdata['user_practical_examiner_id'] = $user_id;
            $svdata['exam_year'] = $current_exam_year;
            $svdata['exam_month'] = $current_exam_month;
            $svdata['is_lock_submit'] = 0;
            $svdata['stream'] = $stream;
            $svdata['created_at'] = date("Y-m-d H:i:s");
            $svdata['updated_at'] = date("Y-m-d H:i:s");
            $saveUserExaminerMap = UserExaminerMap::insertGetId($svdata);

            $saveUserPracticalExaminer = array();
            $saveUserPracticalExaminer['status'] = 1;
            $saveUserPracticalExaminer['is_mapped'] = 1;
            UserPracticalExaminer::where('id', '=', $practical_examiner_data->id)->update($saveUserPracticalExaminer);

            // data save in student_allotment_marks table
            // deo id : Auth::User()->id
            $studentAllotmentMarksIdArray = $practicalCustomComponent->getPracticalStudentList($inputs['examcenter_detail'], $inputs['subject'], false);
            foreach ($studentAllotmentMarksIdArray as $each) {
                $deo_data = $practicalCustomComponent->getDistrictIdByUserId(Auth::User()->id);
                $practical_user_data = $practicalCustomComponent->getDistrictIdByUserId($practical_examiner_data->id);

                $saveStudentAllotmentMarkData = array();
                $saveStudentAllotmentMarkData['user_examiner_map_id'] = $saveUserExaminerMap;
                $saveStudentAllotmentMarkData['practical_examiner_id'] = $practical_examiner_data->id;
                $saveStudentAllotmentMarkData['user_practical_examiner_id'] = $practical_examiner_data->id;
                if (!empty(@$practical_user_data->district_rid)) {
                    $saveStudentAllotmentMarkData['practical_examiner_district_id'] = $practical_user_data->district_id;
                }
                $saveStudentAllotmentMarkData['user_deo_id'] = Auth::User()->id;
                if (!empty(@$deo_data->district_id)) {
                    $saveStudentAllotmentMarkData['deo_district_id'] = $deo_data->district_id;
                }
                StudentAllotmentMark::where('id', '=', $each['id'])->update($saveStudentAllotmentMarkData);
            }
            // data save in student_allotment_marks table

            return redirect()->back()->with('message', 'Data successfully saved');
        }
        return view('practicalexaminers.examiner_mapping', compact('title', 'course_dropdown', 'examcenter_datails_dropdown', 'subjects_dropdown', 'user_id', 'e_user_id', 'breadcrumbs', 'practical_examiner_data'));
    }

    public function examiner_mapping_practical_list(Request $request, $practical_user_id = null)
    {
        $practical_user_id = Crypt::decrypt($practical_user_id);
        $current_exam_year = Config::get("global.admission_academicyear_id");
        $current_exam_month = Config::get("global.current_exam_month_id");
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
        $practicalCustomComponent = new PracticalCustomComponent;
        $conditions = array();
        $admission_sessions = CustomHelper::_get_admission_sessions();
        $title = "Examiners Mapped List";
        $table_id = "Examiner_map_List";
        //$e_sso_id = $sso_id;
        //$sso_id = Crypt::decrypt($e_sso_id);
        $formId = ucfirst(str_replace(" ", "_", $table_id));


        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);

        $subjects_dropdown = $subject_list = $this->getSubjectByCoursePracticalTheory(null, 1);

        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);

        $custom_component_obj = new CustomComponent;
        $examcenter_list = $examcenter_datails_dropdown = collect($custom_component_obj->getExamCenterWithBothCourseCode());

        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => route('deodashboard')
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadExamCenterMappingExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadExamCenterMappingPdf',
                'status' => false
            )
        );

        $filters = array(
            array(
                "lbl" => "SSO ID",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => 'SSO ID',
                'dbtbl' => 'users',
                'status' => true
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'placeholder' => 'Course',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examcenter_datails_dropdown,
                'placeholder' => 'Exam Center',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subjects_dropdown,
                'placeholder' => 'Subject',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                'lbl' => 'Is Lock & Submit',
                'fld' => 'is_lock_submit',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Is Lock & Submit',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                'lbl' => 'Is Signed PDF',
                'fld' => 'is_signed_pdf',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Is Signed PDF',
                'dbtbl' => 'user_examiner_maps',
            )
        );

        $conditions = array();
        $role_id = @Session::get('role_id');
        $deo = Config::get('global.deo');
        if ($role_id == $deo) {
            $conditions["user_examiner_maps.user_deo_id"] = @Auth::user()->id;
        }
        if (!empty($practical_user_id)) {
            $conditions["user_examiner_maps.user_practical_examiner_id"] = $practical_user_id;
        }


        if ($request->all()) {
            $inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else if (@$k && @$k == 'is_lock_submit' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = "0";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else if (@$k && @$k == 'is_signed_pdf' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else if (@$k && @$k == 'is_lock_submit' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = "0";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else if (@$k && @$k == 'is_signed_pdf' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $practicalCustomComponent->getExaminerMappingList($formId);

        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);

        $showEditMarksBtn = $practicalCustomComponent->isAllowUnlockPracticalMarks();

        return view('practicalexaminers.examiner_mapping_practical_list', compact('showEditMarksBtn', 'yesno', 'master', 'filters', 'exportBtn', 'formId', 'table_id', 'title', 'breadcrumbs', 'examcenter_list', 'current_exam_year', 'current_exam_month', 'exam_year_arr', 'exam_month_arr', 'subject_list', 'course_dropdown', 'subjects_dropdown', 'examcenter_datails_dropdown', 'practical_user_id'))->withInput($request->all());
    }


    public function examiner_mapping_list(Request $request)
    {
        $current_exam_year = Config::get("global.admission_academicyear_id");
        $current_exam_month = Config::get("global.current_exam_month_id");
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
        $practicalCustomComponent = new PracticalCustomComponent;
        $conditions = array();
        $admission_sessions = CustomHelper::_get_admission_sessions();
        $title = "Examiners Mapped List";
        $table_id = "Examiner_map_List";
        //$e_sso_id = $sso_id;
        //$sso_id = Crypt::decrypt($e_sso_id);
        $formId = ucfirst(str_replace(" ", "_", $table_id));

        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $subjects_dropdown = $subject_list = $this->getSubjectByCoursePracticalTheory(null, 1);
        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);

        $custom_component_obj = new CustomComponent;
        $examcenter_list = $examcenter_datails_dropdown = collect($custom_component_obj->getExamCenterWithBothCourseCode());
        $routeDashboard = "";
        $role_id = Session::get('role_id');
        if ($role_id == config("global.deo")) {
            $routeDashboard = route("deodashboard");
        }
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => $routeDashboard
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );

        $exportBtn = array(
            array(
                "label" => "Export Excel",
                'url' => 'downloadExamCenterMappingExl',
                'status' => true,
            ),
            array(
                "label" => "Export PDF",
                'url' => 'downloadExamCenterMappingPdf',
                'status' => false
            )
        );

        $filters = array(
            array(
                "lbl" => "SSO",
                'fld' => 'ssoid',
                'input_type' => 'text',
                'placeholder' => 'SSO',
                'dbtbl' => 'users',
                'status' => true
            ),
            array(
                "lbl" => "Course",
                'fld' => 'course',
                'input_type' => 'select',
                'options' => $course_dropdown,
                'placeholder' => 'Course',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Exam Center",
                'fld' => 'examcenter_detail_id',
                'input_type' => 'select',
                'options' => $examcenter_datails_dropdown,
                'placeholder' => 'Exam Center',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Subject",
                'fld' => 'subject_id',
                'input_type' => 'select',
                'options' => $subjects_dropdown,
                'placeholder' => 'Subject',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                "lbl" => "Stream",
                'fld' => 'stream',
                'input_type' => 'select',
                'options' => $stream_id,
                'placeholder' => 'Stream',
                'dbtbl' => 'user_examiner_maps',
                'status' => true
            ),
            array(
                'lbl' => 'Is Lock & Submit',
                'fld' => 'is_lock_submit',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Is Lock & Submit',
                'dbtbl' => 'user_examiner_maps',
            ),
            array(
                'lbl' => 'Is Signed PDF',
                'fld' => 'is_signed_pdf',
                'input_type' => 'select',
                'options' => $yesno,
                'placeholder' => 'Is Signed PDF',
                'dbtbl' => 'user_examiner_maps',
            )
        );

        $conditions = array();
        $role_id = @Session::get('role_id');
        $deo = Config::get('global.deo');
        if ($role_id == $deo) {
            $conditions["user_examiner_maps.user_deo_id"] = @Auth::user()->id;
        }
        if (!empty($practical_user_id)) {
            $conditions["user_examiner_maps.user_practical_examiner_id"] = $practical_user_id;
        }

        if ($request->all()) {
            @$inputs = $request->all();
            foreach ($inputs as $k => $v) {
                if ($k != 'page' && $v != "") {
                    foreach ($filters as $ik => $iv) {
                        if ($iv['fld'] == $k) {
                            if (!empty($iv['dbtbl'])) {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " like %" . $v . "% ";
                                } else if (@$k && @$k == 'is_lock_submit' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = "0";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else if (@$k && @$k == 'is_signed_pdf' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else {
                                    $conditions[$iv['dbtbl'] . "." . $k] = $v;
                                }
                            } else {
                                if (@$iv['search_type'] && $iv['search_type'] == 'like') {
                                    $conditions[$k] = " like %" . $v . "% ";
                                } else if (@$k && @$k == 'is_lock_submit' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = "0";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else if (@$k && @$k == 'is_signed_pdf' && $v == 0) {
                                    $conditions[$iv['dbtbl'] . "." . $k] = " is null";
                                    // $conditions[ $iv['dbtbl'] . "." . $k] = " 0 || `rs_".$iv['dbtbl']. "`.". $k ." is null ";
                                } else {
                                    $conditions[$k] = $v;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
        Session::put($formId . '_conditions', $conditions);

        $master = $practicalCustomComponent->getExaminerMappingList($formId);


        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);

        $showEditMarksBtn = $practicalCustomComponent->isAllowUnlockPracticalMarks();

        return view('practicalexaminers.examiner_mapping_lists', compact('showEditMarksBtn', 'yesno', 'master', 'filters', 'exportBtn', 'formId', 'table_id', 'title', 'breadcrumbs', 'examcenter_list', 'current_exam_year', 'current_exam_month', 'exam_year_arr', 'exam_month_arr', 'subject_list', 'course_dropdown', 'subjects_dropdown', 'examcenter_datails_dropdown'))->withInput($request->all());
    }

    public function downloadPracticalExaminerListExl(Request $request, $type = "xlsx")
    {
        $examcenterlist_mapping_exl_data = new PracticalExaminerListExport;
        $filename = 'practical-examcenter-data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenterlist_mapping_exl_data, $filename);
    }

    public function downloadExamCenterMappingExl(Request $request, $type = "xlsx")
    {
        $examcenter_mapping_exl_data = new ExamcenterMappingExlExport;
        $filename = 'examcenter_mapping_data' . date('d-m-Y H:i:s') . '.' . $type;
        return Excel::download($examcenter_mapping_exl_data, $filename);
    }

    public function downloadExamCenterMappingPdf()
    {
        $title = "Examiner_map_List";
        $formId = ucfirst(str_replace(" ", "_", $title));
        $examcenter_list = ExamcenterDetail::pluck('cent_name', 'id');
        $subject_list = Subject::pluck('name', 'id');
        $combo_name = 'course';
        $course_dropdown = $this->master_details($combo_name);
        $current_exam_year = Config::get("global.admission_academicyear_id");
        $current_exam_month = Config::get("global.current_exam_month_id");
        $combo_name = 'admission_sessions';
        $exam_year_arr = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_month_arr = $this->master_details($combo_name);

        $practicalCustomComponent = new practicalCustomComponent;
        $master = $practicalCustomComponent->getExaminerMappingList($formId);
        if (empty($master)) {
            return redirect()->route('/')->with('error', 'Failed! Details not found');
        }
        // return view('practicalexaminers.examcenter_mapping_list_pdf', compact('formId','master','examcenter_list','subject_list','course_dropdown','current_exam_year','current_exam_month','exam_year_arr','exam_month_arr'));

        $pdf = PDF::loadView('practicalexaminers.examcenter_mapping_list_pdf', compact('formId', 'master', 'examcenter_list', 'subject_list', 'course_dropdown', 'current_exam_year', 'current_exam_month', 'exam_year_arr', 'exam_month_arr'));
        $path = public_path('practical_examiners\Pdf-Practical-' . $formId . '-' . date('d-m-Y-H-i-s') . '.pdf');
        $pdf->save($path, $pdf, true);
        return (Response::download($path));
    }

    public function practicalMarksUnlock($user_examiner_map_id = null, $type = null)
    {
        if (!empty($user_examiner_map_id) && $user_examiner_map_id != null) {
            $user_examiner_map_id = Crypt::decrypt($user_examiner_map_id);

            $unlockUserExaminerMap = UserExaminerMap::where('id', $user_examiner_map_id)->where('is_lock_submit', 1)->first();
            if (empty(@$unlockUserExaminerMap->id)) {
                $user_practical_examiner_id = @$unlockUserExaminerMap->user_practical_examiner_id;
                return redirect()->route('examiner_mapping_practical_list', Crypt::encrypt(@$user_practical_examiner_id))->with('error', 'Failed! Examiner Mapping detials not found.');
            } else {
                $user_practical_examiner_id = @$unlockUserExaminerMap->user_practical_examiner_id;

                $saveData = array();
                $saveData['is_lock_submit'] = 0;
                $saveData['lock_submit_created'] = null;
                $saveData['lock_submit_remark'] = '';
                $saveData['document'] = null;
                $saveData['is_signed_pdf'] = 0;
                $saveData['practical_lastpage_submitted_date'] = null;
                $saveData['practical_lock_submit_remark'] = '';
                $saveData['practical_lock_submit_user_id'] = null;
                $saveData['unlock_by'] = Auth::user()->id;
                $saveData['is_unlock'] = 1;
                if ($type == '1') {
                    $saveData['update_marks_entry'] = 1;
                } else {
                    $saveData['update_marks_entry'] = 0;
                }
                $saveData['unlock_date'] = date("Y-m-d H:i:s");
                $saveData['unlock_remark'] = '';
                $saveData['updated_at'] = date("Y-m-d H:i:s");
                UserExaminerMap::where('id', $user_examiner_map_id)->update($saveData);
                $saveUserPracticalExaminerData = array();
                $saveUserPracticalExaminerData['is_complete_lock_submit'] = 0;
                UserPracticalExaminer::where('user_id', '=', @$user_practical_examiner_id)->update($saveUserPracticalExaminerData);

                $studentAllotmentData = StudentAllotmentMark::where('user_examiner_map_id', $user_examiner_map_id)->get();
                if (!empty(@$studentAllotmentData)) {
                    foreach ($studentAllotmentData as $studentAllotment) {
                        $saveMarksData = array();
                        $saveMarksData['is_practical_lock_submit'] = 0;
                        //$saveMarksData['is_update_practical_marks_practical_examiner'] = 0;
                        $saveMarksData['updated_at'] = date("Y-m-d H:i:s");
                        StudentAllotmentMark::where('id', '=', @$studentAllotment->id)->update($saveMarksData);
                    }
                }

                return redirect()->route('examiner_mapping_practical_list', Crypt::encrypt($user_practical_examiner_id))->with('message', 'Examiner Mapping successfully unlocked.');
            }
        } else {
            return redirect()->back()->with('error', 'Failed! You are not authorized for this action.');
        }
    }

}