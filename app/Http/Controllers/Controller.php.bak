<?php

namespace App\Http\Controllers;

use App\Component\BookRequirementCustomComponent;
use App\Component\CustomComponent;
use App\Component\ThoeryCustomComponent;
use App\Helper\CustomHelper;
use App\Http\Traits\QRcode;
use App\Models\Address;
use App\Models\AdmissionSubject;
use App\Models\AicenterDetail;
use App\Models\AicenterSittingMapped;
use App\Models\Application;
use App\Models\BankDetail;
use App\Models\CenterAllotment;
use App\Models\ChangeRequertOldStudentFees;
use App\Models\ChangeRequeStstudent;
use App\Models\Document;
use App\Models\DocumentVerification;
use App\Models\ExamcenterDetail;
use App\Models\ExamLateFeeDate;
use App\Models\ExamResult;
use App\Models\ExamSubject;
use App\Models\MarksheetMigrationRequest;
use App\Models\ModelHasRole;
use App\Models\PageDetail;
use App\Models\Pastdata;
use App\Models\Permission;
use App\Models\PrepareExamSubject;
use App\Models\PublicationBook;
use App\Models\ReportMasterQuery;
use App\Models\RevalStudent;
use App\Models\SessionalExamSubject;
use App\Models\Student;
use App\Models\StudentAllotment;
use App\Models\StudentAllotmentMark;
use App\Models\StudentDocumentVerification;
use App\Models\StudentFee;
use App\Models\StudentLog;
use App\Models\StudentPracticalSlots;
use App\Models\StudentTheoryCopyCheckingMark;
use App\Models\Subject;
use App\Models\SuppChangeRequertOldStudentFees;
use App\Models\SuppChangeRequestStudents;
use App\Models\Supplementary;
use App\Models\SupplementarySubject;
use App\Models\Toc;
use App\Models\TocMark;
use App\Models\User;
use App\Models\VerificationLabel;
use Auth;
use Cache;
use Carbon\Carbon;
use Config;
use DB;
use File;
use Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Redirect;
use Response;
use Route;
use Session;
use Validator;
use ZipArchive;

//22092023


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

        // $defaultPageLimit = Session::get('defaultPageLimit');
        // if(@$defaultPageLimit){
        // 	Config::set('global.defaultPageLimit',$defaultPageLimit);
        // }
        // $defaultPageLimit = Config::get('global.defaultPageLimit');
        // echo "Test <br>";print_r($defaultPageLimit);


    }


    public function prd($request_arr)
    {
        echo "<pre>";
        print_r($request_arr);
        echo "</pre>";
        die;
    }

    public function pr($request_arr)
    {
        echo "<pre>";
        print_r($request_arr);
        echo "</pre>";
    }

    public function _callCurl($url, $data = false, $method = '')
    {
        $curl = curl_init();
        $url = $url;
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_USERPWD, "madarsa.test:Test@1234");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    public function getStudentPersoanlDetails($student_id = null)
    {
        $master = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject')->where('id', $student_id)->first();
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'supp_late_fees';
        $supp_late_fees = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;

        $output['personalDetails'] = array(
            "photograph" => array(
                "fld" => "photograph",
                "label" => "फोटोग्राफ (Photograph)",

            ),
            "signature" => array(
                "fld" => "signature",
                "label" => "हस्ताक्षर (Signature)",
            ),
            "ai_code" => array(
                "fld" => "ai_code",
                "label" => "एआई केंद्र( AI Code)",
                "value" => @$master->ai_code
            ),
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन संख्यार( Enrollment)",
                "value" => @$master->enrollment
            ),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            "Exam" => array(
                "fld" => "Exam",
                "label" => " परीक्षा (Exam)",
                "value" => @$exam_session[@$master->$stream]
            ),
            "gender_id" => array(
                "fld" => "gender_id",
                "label" => " लिंग (Gender)",
                "value" => @$gender_id[@$master->gender_id]
            ),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth)",
                "value" => date("d-m-Y", strtotime(@$master->dob)),
            ), "religion_id" => array(
                "fld" => "religion_id",
                "label" => "धर्म (Religion)",
                "value" => @$religion[@$master->application->religion_id]
            ), "nationality" => array(
                "fld" => "nationality",
                "label" => "राष्ट्रीयता (Nationality)",
                "value" => @$nationality[@$master->application->nationality]
            ), "category_a" => array(
                "fld" => "category_a",
                "label" => "श्रेणी ए (Category A)",
                "value" => @$categorya[@$master->application->category_a]
            ), "disability" => array(
                "fld" => "disability",
                "label" => "विकलांगता (Disability)",
                "value" => @$disability[@$master->application->disability]
            ), "disadvantage_group" => array(
                "fld" => "disadvantage_group",
                "label" => "वंचित वर्ग (Disadvantage Group)",
                "value" => @$dis_adv_group[@$master->application->disadvantage_group]
            ), "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$course[@$master->course]
            ), "midium" => array(
                "fld" => "midium",
                "label" => "अध्ययन का माध्यम (Medium of Study)",
                "value" => @$midium[@$master->application->medium]
            ), "rural_urban" => array(
                "fld" => "rural_urban",
                "label" => "शहरी /ग्रामीण (Rural/Urban)",
                "value" => @$rural_urban[@$master->application->rural_urban]
            ), "employment" => array(
                "fld" => "employment",
                "label" => "रोज़गार (Employment)",
                "value" => @$employment[@$master->application->employment]
            )
        );

        $photographimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $master->id . DIRECTORY_SEPARATOR . $master->document->photograph;

        $signaturemageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . $master->id . DIRECTORY_SEPARATOR . $master->document->signature;

        if (file_exists($photographimageurl) && !empty($master->document->photograph)) {
            $output['personalDetails']['photograph']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/' . $studentDocumentPath . '/' . @$master->document->photograph) . "' }} >" . "</a>";
        } else {
            $output['personalDetails']['photograph']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/app-assets/images/users1.png') . "'}} >" . "</a>";
        }
        if (file_exists($signaturemageurl) && !empty($master->document->signature)) {
            $output['personalDetails']['signature']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/' . $studentDocumentPath . '/' . @$master->document->signature) . "' }} >" . "</a>";
        } else {
            $output['personalDetails']['signature']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/app-assets/images/signature1.png') . "'}} >" . "</a>";
        }
        foreach (@$master->exam_subject as $k => $v) {
            $vMark = @$v['sessional_marks'];
            if (@$v['sessional_marks'] == '999') {
                $vMark = '<span style="color:red;">Absent</span>';
            }
            $output['examSubjectDetails'][] = array(
                "fld" => "subject_id",
                "label" => @$subject_list[$v['subject_id']],
                "value" => $vMark
            );
        }

        $output = array(
            "personalDetails" => array(
                "seciontLabel" => "Personal Details",
                "data" => @$output['personalDetails']
            ),
            "examSubjectDetails" => array(
                "seciontLabel" => " Sessional Marks Details",
                "data" => @$output['examSubjectDetails']
            )
        );

        return $output;
    }

    public function master_details($combo_name = null, $orderByRaw = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($combo_name)) {
            $condtions = ['status' => 1, 'combo_name' => $combo_name];
        }

        $mainTable = "masters";
        $cacheName = $mainTable . "_" . $combo_name;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            // echo "1";die;
            $result = Cache::get($cacheName);
        } else {
            // echo "2";die;
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable, $orderByRaw, $combo_name) {
                if (@$orderByRaw) {
                    $result = DB::table($mainTable)->where($condtions)->orderByRaw($orderByRaw)->get()->pluck('option_val', 'option_id');
                } else {
                    if ($combo_name == "dis_adv_group") {
                        $result = DB::table($mainTable)->where($condtions)->orderBy("option_val")->get()->pluck('option_val', 'option_id');
                    } else {
                        $result = DB::table($mainTable)->where($condtions)->get()->pluck('option_val', 'option_id');
                    }

                }
                return $result;
            });
        }
        return $result;
    }

    public function subjectList($course = null)
    {
        $condtions = null;
        $result = array();
        if ($course != null) {
            $condtions = ['course' => $course, 'deleted' => 0];
        } else {
            $condtions = ['deleted' => 0];
        }

        $mainTable = "subjects";
        $cacheName = "Subjects_U_" . $course;
        //Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function getPrepareSessionalStudentPersoanlDetails($student_id = null)
    {
        $master = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'prepare_exam_subject')->where('id', $student_id)->first();
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'supp_late_fees';
        $supp_late_fees = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;

        $output['personalDetails'] = array(
            "photograph" => array(
                "fld" => "photograph",
                "label" => "फोटोग्राफ (Photograph)",

            ),
            "signature" => array(
                "fld" => "signature",
                "label" => "हस्ताक्षर (Signature)",
            ),
            "ai_code" => array(
                "fld" => "ai_code",
                "label" => "एआई केंद्र( AI Code)",
                "value" => @$master->ai_code
            ),
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन संख्यार( Enrollment)",
                "value" => @$master->enrollment
            ),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            "Exam" => array(
                "fld" => "Exam",
                "label" => " परीक्षा (Exam)",
                "value" => @$exam_session[@$master->$stream]
            ),
            "gender_id" => array(
                "fld" => "gender_id",
                "label" => " लिंग (Gender)",
                "value" => @$gender_id[@$master->gender_id]
            ),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth)",
                "value" => date("d-m-Y", strtotime(@$master->dob)),
            ), "religion_id" => array(
                "fld" => "religion_id",
                "label" => "धर्म (Religion)",
                "value" => @$religion[@$master->application->religion_id]
            ), "nationality" => array(
                "fld" => "nationality",
                "label" => "राष्ट्रीयता (Nationality)",
                "value" => @$nationality[@$master->application->nationality]
            ), "category_a" => array(
                "fld" => "category_a",
                "label" => "श्रेणी ए (Category A)",
                "value" => @$categorya[@$master->application->category_a]
            ), "disability" => array(
                "fld" => "disability",
                "label" => "विकलांगता (Disability)",
                "value" => @$disability[@$master->application->disability]
            ), "disadvantage_group" => array(
                "fld" => "disadvantage_group",
                "label" => "वंचित वर्ग (Disadvantage Group)",
                "value" => @$dis_adv_group[@$master->application->disadvantage_group]
            ), "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$course[@$master->course]
            ), "midium" => array(
                "fld" => "midium",
                "label" => "अध्ययन का माध्यम (Medium of Study)",
                "value" => @$midium[@$master->application->medium]
            ), "rural_urban" => array(
                "fld" => "rural_urban",
                "label" => "शहरी /ग्रामीण (Rural/Urban)",
                "value" => @$rural_urban[@$master->application->rural_urban]
            ), "employment" => array(
                "fld" => "employment",
                "label" => "रोज़गार (Employment)",
                "value" => @$employment[@$master->application->employment]
            )
        );

        $photographimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . @$master->id . DIRECTORY_SEPARATOR . @$master->document->photograph;

        $signaturemageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . @$master->id . DIRECTORY_SEPARATOR . @$master->document->signature;

        if (file_exists($photographimageurl) && !empty($master->document->photograph)) {
            $output['personalDetails']['photograph']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/' . $studentDocumentPath . '/' . @$master->document->photograph) . "' }} >" . "</a>";
        } else {
            $output['personalDetails']['photograph']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/app-assets/images/users1.png') . "'}} >" . "</a>";
        }
        if (file_exists($signaturemageurl) && !empty($master->document->signature)) {
            $output['personalDetails']['signature']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/' . $studentDocumentPath . '/' . @$master->document->signature) . "' }} >" . "</a>";
        } else {
            $output['personalDetails']['signature']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/app-assets/images/signature1.png') . "'}} >" . "</a>";
        }

        foreach (@$master->prepare_exam_subject as $k => $v) {
            $vMark = @$v['sessional_marks'];
            if (@$v['sessional_marks'] == '999') {
                $vMark = '<span style="color:red;">Absent</span>';
            }
            $output['examSubjectDetails'][] = array(
                "fld" => "subject_id",
                "label" => @$subject_list[$v['subject_id']],
                "value" => $vMark
            );
        }

        $output = array(
            "personalDetails" => array(
                "seciontLabel" => "Personal Details",
                "data" => @$output['personalDetails']
            ),
            "examSubjectDetails" => array(
                "seciontLabel" => " Sessional Marks Details",
                "data" => @$output['examSubjectDetails']
            )
        );

        return $output;
    }

    public function getSessionalStudentPersoanlDetails($student_id = null)
    {
        $master = Student::
        with('application', 'document')
            ->where('id', $student_id)
            ->first();
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'supp_late_fees';
        $supp_late_fees = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;

        $output['personalDetails'] = array(
            "photograph" => array(
                "fld" => "photograph",
                "label" => "फोटोग्राफ (Photograph)",

            ),
            "signature" => array(
                "fld" => "signature",
                "label" => "हस्ताक्षर (Signature)",
            ),
            "ai_code" => array(
                "fld" => "ai_code",
                "label" => "एआई केंद्र ( AI Code)",
                "value" => @$master->ai_code
            ),
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन संख्या( Enrollment)",
                "value" => @$master->enrollment
            ),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            "Exam" => array(
                "fld" => "Exam",
                "label" => " परीक्षा (Exam)",
                "value" => @$exam_session[@$master->stream]
            ),
            "gender_id" => array(
                "fld" => "gender_id",
                "label" => " लिंग (Gender)",
                "value" => @$gender_id[@$master->gender_id]
            ),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth)",
                "value" => date("d-m-Y", strtotime(@$master->dob)),
            ), "religion_id" => array(
                "fld" => "religion_id",
                "label" => "धर्म (Religion)",
                "value" => @$religion[@$master->application->religion_id]
            ), "nationality" => array(
                "fld" => "nationality",
                "label" => "राष्ट्रीयता (Nationality)",
                "value" => @$nationality[@$master->application->nationality]
            ), "category_a" => array(
                "fld" => "category_a",
                "label" => "श्रेणी ए (Category A)",
                "value" => @$categorya[@$master->application->category_a]
            ), "disability" => array(
                "fld" => "disability",
                "label" => "विकलांगता (Disability)",
                "value" => @$disability[@$master->application->disability]
            ), "disadvantage_group" => array(
                "fld" => "disadvantage_group",
                "label" => "वंचित वर्ग (Disadvantage Group)",
                "value" => @$dis_adv_group[@$master->application->disadvantage_group]
            ), "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$course[@$master->course]
            ), "midium" => array(
                "fld" => "midium",
                "label" => "अध्ययन का माध्यम (Medium of Study)",
                "value" => @$midium[@$master->application->medium]
            ), "rural_urban" => array(
                "fld" => "rural_urban",
                "label" => "शहरी /ग्रामीण (Rural/Urban)",
                "value" => @$rural_urban[@$master->application->rural_urban]
            ), "employment" => array(
                "fld" => "employment",
                "label" => "रोज़गार (Employment)",
                "value" => @$employment[@$master->application->employment]
            )
        );

        $photographimageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . @$master->id . DIRECTORY_SEPARATOR . @$master->document->photograph;

        $signaturemageurl = public_path() . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR . @$master->id . DIRECTORY_SEPARATOR . @$master->document->signature;

        if (file_exists($photographimageurl) && !empty($master->document->photograph)) {
            $output['personalDetails']['photograph']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/' . $studentDocumentPath . '/' . @$master->document->photograph) . "' }} >" . "</a>";
        } else {
            $output['personalDetails']['photograph']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/app-assets/images/users1.png') . "'}} >" . "</a>";
        }
        if (file_exists($signaturemageurl) && !empty($master->document->signature)) {
            $output['personalDetails']['signature']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/' . $studentDocumentPath . '/' . @$master->document->signature) . "' }} >" . "</a>";
        } else {
            $output['personalDetails']['signature']['value'] = "<img height='100px' width='100px' target='_blank' src='" . url('public/app-assets/images/signature1.png') . "'}} >" . "</a>";
        }

        // foreach(@$master->exam_subject as $k => $v){
        // 	$output['examSubjectDetails'][] = array(
        // 		"fld" => "subject_id",
        // 		"label" => @$subject_list[$v['subject_id']],
        // 		"value" => @$v['sessional_marks']

        // 	);
        // }

        $output = array(
            "personalDetails" => array(
                "seciontLabel" => "Personal Details",
                "data" => @$output['personalDetails']
            ),
            // "examSubjectDetails" => array(
            // 	"seciontLabel" => "Exam Subjects Marks Details",
            // 	"data" => @$output['examSubjectDetails']
            // )
        );

        return $output;
    }

    public function getRevalStudentDetails($student_id = null, $reval_id = null)
    {

        $master = Student::where('students.id', $student_id)
            ->with('reval_students', function ($query) use ($reval_id) {
                $query->where('id', '=', $reval_id);
            })->with('reval_student_subjects', function ($query) use ($reval_id) {
                $query->where('reval_id', '=', $reval_id);
            })
            ->with('Address')
            ->first();

        $dobs = null;


        if (@$master->dob) {
            $dobs = date('d-m-Y', strtotime($master->dob));
        }
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $combo_name = 'reval_types';
        $reval_types = $this->master_details($combo_name);
        $combo_name = 'reval_per_subject_fee';
        $reval_per_subject_fee = $this->master_details($combo_name);

        $subject_list = $this->subjectList();
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $photograph = @$master->document->photograph;
        $signature = @$master->document->signature;
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);

        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master->mobile = $custom_component_obj->_replaceTheStringWithX(@$master->mobile);
        if (@$master->application->jan_aadhar_number) {
            $master->application->jan_aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->jan_aadhar_number);
        }
        if (@$master->application->aadhar_number) {
            $master->application->aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->aadhar_number);
        }
        if (@$master->bankdetils->account_number) {
            $master->bankdetils->account_number = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->account_number);
        }
        if (@$master->bankdetils->linked_mobile) {
            $master->bankdetils->linked_mobile = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->linked_mobile);
        }
        if (@$master->bankdetils->ifsc_code) {
            $master->bankdetils->ifsc_code = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->ifsc_code);
        }
        if (@$master->challan_tid) {
            $master->challan_tid = $custom_component_obj->_replaceTheStringWithX(@$master->challan_tid);
        }
        /* Replace string with X end */


        $details = $this->getDefaultImgAndSign($photograph, $signature, $studentDocumentPath);
        $output['personalDetails'] = array(
            "photograph" => array(
                "fld" => "photograph",
                "label" => "फोटोग्राफ (photograph)",
                "value" => "<img height='100px' width='100px' target='_blank' src='" . $details['photographimageurl'] . "'/>"
            ),
            "signature" => array(
                "fld" => "signature",
                "label" => "हस्ताक्षर (Signature)",
                "value" => "<img height='100px' width='100px' target='_blank' src='" . $details['signatureimageurl'] . "'/>"
            ),
            // "ai_code" => array(
            // 	"fld" => "ai_code",
            // 	"label" => "एआई कोड(AI Code)",
            // 	"value" => @$master->ai_code
            // ),
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन (Enrollment)",
                "value" => @$master->enrollment
            ),
            // "is_self_filled" => array(
            // 	"fld" => "is_self_filled",
            // 	"label" => "छात्र द्वारा स्वयं भरा गया है? (Is it filled by the student themselves?)",
            // 	"value" => @$yesno[@$master->is_self_filled]
            // ),
            // "is_otp_verified" => array(
            // 	"fld" => "is_otp_verified",
            // 	"label" => "क्या ओटीपी सत्यापित है?(Is OTP verified)",
            // 	"value" => @$yesno[@$master->is_otp_verified]
            // ),
            "ssoid" => array(
                "fld" => "ssoid",
                "label" => "एसएसओ (SSO)",
                "value" => @$master->ssoid
            ),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            // "Exam" => array(
            // 	"fld" => "Exam",
            // 	"label" => " परीक्षा (Exam)",
            // 	"value" => @$exam_session[@$master->exam_month]
            // ),
            "gender_id" => array(
                "fld" => "gender_id",
                "label" => " लिंग (Gender)",
                "value" => @$gender_id[@$master->gender_id]
            ),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth) (DD-MM-YYYY):",
                "value" => @$dobs
            ),
            // "religion_id" => array(
            // 	"fld" => "religion_id",
            // 	"label" => "धर्म (Religion)",
            // 	"value" => @$religion[@$master->application->religion_id]
            // ),"is_multiple_faculty" => array(
            // 	"fld" => "is_multiple_faculty",
            // 	"label" => " विभिन्न संकाय विषय(Multiple Faculty Subjects)",
            // 	"value" => @$yesno[@$master->application->is_multiple_faculty]
            // ), "selected_faculty" => array(
            // 	"fld" => "selected_faculty",
            // 	"label" => "मुख्य संकाय(Preferred Faculty)",
            // 	"value" => @$master->application->selected_faculty
            // ),"nationality" => array(
            // 	"fld" => "nationality",
            // 	"label" => "राष्ट्रीयता (Nationality)",
            // 	"value" => @$nationality[@$master->application->nationality]
            // ),"category_a" => array(
            // 	"fld" => "category_a",
            // 	"label" => "श्रेणी ए (Category A)",
            // 	"value" => @$categorya[@$master->application->category_a]
            // ),"disability" => array(
            // 	"fld" => "disability",
            // 	"label" => "विकलांगता (Disability)",
            // 	"value" => @$disability[@$master->application->disability]
            // ),"disadvantage_group" => array(
            // 	"fld" => "disadvantage_group",
            // 	"label" => "वंचित वर्ग (Disadvantage Group)",
            // 	"value" => @$dis_adv_group[@$master->application->disadvantage_group]
            // ),
            "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$course[@$master->course]
            ), "mobile" => array(
                "fld" => "mobile",
                "label" => "मोबाइल (Mobile)",
                "value" => @$master->mobile
            ),
            // "midium" => array(
            // 	"fld" => "midium",
            // 	"label" => "अध्ययन का माध्यम (Medium of Study)",
            // 	"value" => @$midium[@$master->application->medium]
            // ),
            "stream" => array(
                "fld" => "stream",
                "label" => "स्ट्रीम (Stream)",
                "value" => @$stream_id[@$master->stream]
            ),
            /* "jan_aadhar_number" => array(
				"fld" => "jan_aadhar_number",
				"label" => "जन आधार संख्या (Jan Aadhar Number)",
				"value" => @$master->application->jan_aadhar_number
			),
			  "aadhar_number" => array(
				"fld" => "aadhar_number",
				"label" => "आधार नंबर (Aadhar Number)",
				"value" => @$master->application->aadhar_number
			),"are_you_from_rajasthan	" => array(
				"fld" => "are_you_from_rajasthan	",
				"label" => "क्या आप राजस्थान के मूल निवासी हैं? (Are You domicile of Rajasthan)",
				"value" => @$are_you_from_rajasthan[@$master->are_you_from_rajasthan]
			),*/
            "email" => array(
                "fld" => "email",
                "label" => "ईमेल (Email)",
                "value" => @$master->email
            ),
            "reval_type" => array(
                "fld" => "reval_type",
                "label" => "पुनर्मूल्यांकन आवेदन का प्रकार (Reval Application Type)",
                "value" => @$reval_types[@$master->reval_students->reval_type]
            ),
            /*"rural_urban" => array(
				"fld" => "rural_urban",
				"label" => "शहरी /ग्रामीण (Rural/Urban)",
				"value" => @$rural_urban[@$master->application->rural_urban]
			),
			"employment" => array(
				"fld" => "employment",
				"label" => "रोज़गार (Employment)",
				"value" => @$employment[@$master->application->employment]
			),"pre_qualification" => array(
				"fld" => "pre_qualification",
				"label" => "पूर्व योग्यता (Pre Qualification)",
				"value" => @$pre_qualifi[@$master->application->pre_qualification]
			),*/
        );

        $output['addressDetails'] = array(
            "address1" => array(
                "fld" => "address1",
                "label" => " पता पंक्ति 1 (Address Line 1)",
                "value" => @$master->address->address1
            ),
            "address2" => array(
                "fld" => "address2",
                "label" => "पता पंक्ति 2 (Address Line 2)",
                "value" => @$master->address->address2
            ),
            "address3" => array(
                "fld" => "address3",
                "label" => "पता पंक्ति 3 (Address Line 3)",
                "value" => @$master->address->address3
            ),
            "state_name" => array(
                "fld" => "state_name",
                "label" => " राज्य (State)",
                "value" => @$master->address->state_name
            ),
            "district_name" => array(
                "fld" => "district_name",
                "label" => " जिला (District)",
                "value" => @$master->address->district_name
            ),
            "tehsil_name" => array(
                "fld" => "tehsil_name",
                "label" => "तहसील (Tehsil)",
                "value" => @$master->address->tehsil_name
            ),
            "block_name" => array(
                "fld" => "block_name",
                "label" => "ब्लॉक/(Block)",
                "value" => @$master->address->block_name
            ),
            "city_name" => array(
                "fld" => "city_name",
                "label" => " शहर / गाँव (City/Village)",
                "value" => @$master->address->city_name
            ),
            "pincode" => array(
                "fld" => "pincode",
                "label" => " पिन कोड (Pincode)",
                "value" => @$master->address->pincode
            )
        );

        if (@$master->address->is_both_same) {

            $output['currentAddressDetails'] = array(
                "is_both_same" => array(
                    "fld" => "is_both_same",
                    "label" => "यदि स्थायी और पत्राचार पता समान है तो (if the permanent and correspondence address are same)",
                    "value" => @$yesno[@$master->address->is_both_same]
                )
            );
        } else {
            $output['currentAddressDetails'] = array(

                "current_address1" => array(
                    "fld" => "address1",
                    "label" => " पता पंक्ति 1 (Address Line 1)",
                    "value" => @$master->address->current_address1
                ),
                "current_address2" => array(
                    "fld" => "address2",
                    "label" => "पता पंक्ति 2 (Address Line 2)",
                    "value" => @$master->address->current_address2
                ),
                "current_address3" => array(
                    "fld" => "address3",
                    "label" => "पता पंक्ति 3 (Address Line 3)",
                    "value" => @$master->address->current_address3
                ),
                "current_state_name" => array(
                    "fld" => "state_name",
                    "label" => " राज्य (State)",
                    "value" => @$master->address->current_state_name
                ),
                "current_district_name" => array(
                    "fld" => "district_name",
                    "label" => " जिला (District)",
                    "value" => @$master->address->current_district_name
                ),
                "current_tehsil_name" => array(
                    "fld" => "tehsil_name",
                    "label" => "तहसील (Tehsil)",
                    "value" => @$master->address->current_tehsil_name
                ),
                "current_block_name" => array(
                    "fld" => "block_name",
                    "label" => "ब्लॉक/(Block)",
                    "value" => @$master->address->current_block_name
                ),
                "current_city_name" => array(
                    "fld" => "city_name",
                    "label" => " शहर / गाँव (City/Village)",
                    "value" => @$master->address->current_city_name
                ),
                "current_pincode" => array(
                    "fld" => "pincode",
                    "label" => " पिन कोड (Pincode)",
                    "value" => @$master->address->current_pincode
                )
            );
        }


        if (@$master->reval_students->late_fees && $master->reval_students->late_fees > 0) {
            $output['studentfeesDetails'] = array(
                "late_fees" => array(
                    "fld" => "late_fees",
                    "label" => "विलम्ब शुल्क (Late Fees)",
                    "value" => @$master->reval_students->late_fees
                ),
                "total" => array(
                    "fld" => "total",
                    "label" => "कुल  शुल्क(	Total Fees)",
                    "value" => @$master->reval_students->total_fees
                ),
            );
        } else {
            $output['studentfeesDetails'] = array(
                "total" => array(
                    "fld" => "total",
                    "label" => "कुल  शुल्क(	Total Fees)",
                    "value" => @$master->reval_students->total_fees
                ),
            );
        }

        $output['TransactionDetails'] = array(
            "challan_tid " => array(
                "fld" => "challan_tid ",
                "label" => " चालान संख्या (Challan Number)",
                "value" => @$master->reval_students->challan_tid
            ),
            "submitted" => array(
                "fld" => "submitted",
                "label" => "शुल्क जमा तिथि( Fees Submitted Date)",
                "value" => @$master->reval_students->submitted
            ),
        );
        $super_admin_id = Config::get("global.super_admin_id");


        foreach (@$master->reval_student_subjects as $k => $v) {
            $extraFlag = null;
            $output['revalSubjectDetails'][] = array(
                "fld" => "subject_id",
                "label" => " Subject " . ($k + 1),
                "value" => @$subject_list[$v['subject_id']] . $extraFlag
            );
        }

        $output = array(
            "personalDetails" => array(
                "seciontLabel" => "Personal Details",
                "data" => @$output['personalDetails']
            ),
            // "addressDetails" => array(
            // 	"seciontLabel" => "Address Details",
            // 	"data" => @$output['addressDetails']
            // ),
            // "currentAddressDetails" => array(
            // 	"seciontLabel" => "Correspondence Address Details",
            // 	"data" => @$output['currentAddressDetails']
            // ),
            "bankDetails" => array(
                "seciontLabel" => "Bank Details",
                "data" => @$output['bankDetails']
            ),
            "revalSubjectDetails" => array(
                "seciontLabel" => "Reval Subjects Details",
                "data" => @$output['revalSubjectDetails']
            ),
            "documentDetails" => array(
                "seciontLabel" => "Document Details",
                "data" => @$output['documentDetails']
            ),
            "studentfeesDetails" => array(
                "seciontLabel" => "Student Fees Details",
                "data" => @$output['studentfeesDetails']
            ),
            "TransactionDetails" => array(
                "seciontLabel" => "Transaction Details",
                "data" => @$output['TransactionDetails']
            ),
        );
        return $output;

    }

    public function getDefaultImgAndSign($photograph = null, $signature = null, $studentDocumentPath = null)
    {
        $path = url('public/' . $studentDocumentPath . '/' . @$photograph);
        $destinationPath = public_path($studentDocumentPath . '/' . @$photograph);

        if (file_exists($destinationPath)) {
            $photographimageurl = $path;
        } else {
            $defaultUserImg = url('public/app-assets/images/users1.png');
            $photographimageurl = $defaultUserImg;
        }

        $path = url('public/' . $studentDocumentPath . '/' . @$signature);
        $destinationPath = public_path($studentDocumentPath . '/' . @$signature);
        if (file_exists($destinationPath)) {
            $signatureimageurl = $path;
        } else {
            $defaultUserImg = url('public/app-assets/images/signature1.png');
            $signatureimageurl = $defaultUserImg;
        }
        $master['photographimageurl'] = $photographimageurl;
        $master['signatureimageurl'] = $signatureimageurl;
        return $master;
    }

    public function getStudentDetails($student_id = null)
    {
        $master = Student::with('application', 'document', 'address', 'admission_subject', 'exam_subject', 'toc_subject', 'bankdetils')->with('studentfee', function ($query) {
            $query->whereNull('deleted_at');
        })
            ->with('changerequestoldfees', function ($query) {
                $query->orderBy('id', 'desc')->whereNull('deleted_at');
            })->where('id', $student_id)->first();

        $dobs = null;
        @$makepaymentchangerequerts = $this->changerequestcheckfees($student_id);
        if (@$master->dob) {
            $dobs = date('d-m-Y', strtotime($master->dob));
        }
        if (!empty(@$master->update_change_requests_challan_tid)) {
            @$labelchangerequest = "आपकी शेष फीस का भुगतान कर दिया गया है( Your Remaining fees Paid )";
        } else {
            @$labelchangerequest = "शेष फीस का भुगतान करना होगा(Remaining fees need to be pay )";
        }
        @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $combo_name = 'book_learning_type';
        $book_learning_type = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $photograph = @$master->document->photograph;
        $signature = @$master->document->signature;
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'fresh_student_verfication_status';
        $fresh_student_verfication_status = $this->master_details($combo_name);
        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master->mobile = $custom_component_obj->_replaceTheStringWithX(@$master->mobile);
        $master->application->jan_aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->jan_aadhar_number);
        $master->application->aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->aadhar_number);
        if (@$master->bankdetils->account_number) {
            $master->bankdetils->account_number = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->account_number);
        }
        if (@$master->bankdetils->linked_mobile) {
            $master->bankdetils->linked_mobile = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->linked_mobile);
        }
        if (@$master->bankdetils->ifsc_code) {
            $master->bankdetils->ifsc_code = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->ifsc_code);
        }
        $master->challan_tid = $custom_component_obj->_replaceTheStringWithX(@$master->challan_tid);
        /* Replace string with X end */

        $details = $this->getDefaultImgAndSign($photograph, $signature, $studentDocumentPath);
        $booklearningtype = 'N/A';
        $ismultiplefaculty = 'N/A';
        $selected_faculty = 'N/A';
        if (@$book_learning_type[@$master->book_learning_type_id]) {
            $booklearningtype = @$book_learning_type[@$master->book_learning_type_id];
        }
        if (@$yesno[@$master->application->is_multiple_faculty]) {
            $ismultiplefaculty = @$yesno[@$master->application->is_multiple_faculty];
        }
        if (@$master->application->selected_faculty) {
            $selected_faculty = $master->application->selected_faculty;
        }
        $output['personalDetails'] = array(
            "photograph" => array(
                "fld" => "photograph",
                "label" => "फोटोग्राफ (photograph)",
                "value" => "<img height='100px' width='100px' target='_blank' src='" . $details['photographimageurl'] . "'/>"
            ),
            "signature" => array(
                "fld" => "signature",
                "label" => "हस्ताक्षर (Signature)",
                "value" => "<img height='100px' width='100px' target='_blank' src='" . $details['signatureimageurl'] . "'/>"
            ),
            "ai_code" => array(
                "fld" => "ai_code",
                "label" => "एआई कोड(AI Code)",
                "value" => @$master->ai_code
            ),
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन (Enrollment)",
                "value" => @$master->enrollment
            ),
            "is_self_filled" => array(
                "fld" => "is_self_filled",
                "label" => "छात्र द्वारा स्वयं भरा गया है? (Is it filled by the student themselves?)",
                "value" => @$yesno[@$master->is_self_filled]
            ),
            "is_otp_verified" => array(
                "fld" => "is_otp_verified",
                "label" => "क्या ओटीपी सत्यापित है?(Is OTP verified)",
                "value" => @$yesno[@$master->is_otp_verified]
            ),
            "ssoid" => array(
                "fld" => "ssoid",
                "label" => "एसएसओ (SSO)",
                "value" => @$master->ssoid
            ),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            "Exam" => array(
                "fld" => "Exam",
                "label" => " परीक्षा (Exam)",
                "value" => @$exam_session[@$master->exam_month]
            ),
            "gender_id" => array(
                "fld" => "gender_id",
                "label" => " लिंग (Gender)",
                "value" => @$gender_id[@$master->gender_id]
            ),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth) (DD-MM-YYYY):",
                "value" => @$dobs
            ), "religion_id" => array(
                "fld" => "religion_id",
                "label" => "धर्म (Religion)",
                "value" => @$religion[@$master->application->religion_id]
            ), "is_multiple_faculty" => array(
                "fld" => "is_multiple_faculty",
                "label" => " विभिन्न संकाय विषय(Multiple Faculty Subjects)",
                "value" => $ismultiplefaculty
            ), "selected_faculty" => array(
                "fld" => "selected_faculty",
                "label" => "मुख्य संकाय(Preferred Faculty)",
                "value" => $selected_faculty
            ), "nationality" => array(
                "fld" => "nationality",
                "label" => "राष्ट्रीयता (Nationality)",
                "value" => @$nationality[@$master->application->nationality]
            ), "category_a" => array(
                "fld" => "category_a",
                "label" => "श्रेणी ए (Category A)",
                "value" => @$categorya[@$master->application->category_a]
            ), "disability" => array(
                "fld" => "disability",
                "label" => "विकलांगता (Disability)",
                "value" => @$disability[@$master->application->disability]
            ), "disadvantage_group" => array(
                "fld" => "disadvantage_group",
                "label" => "वंचित वर्ग (Disadvantage Group)",
                "value" => @$dis_adv_group[@$master->application->disadvantage_group]
            ), "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$course[@$master->course]
            ), "mobile" => array(
                "fld" => "mobile",
                "label" => "मोबाइल (Mobile)",
                "value" => @$master->mobile
            ), "midium" => array(
                "fld" => "midium",
                "label" => "अध्ययन का माध्यम (Medium of Study)",
                "value" => @$midium[@$master->application->medium]
            ), "stream" => array(
                "fld" => "stream",
                "label" => "स्ट्रीम (Stream)",
                "value" => @$stream_id[@$master->stream]
            ),
            "jan_aadhar_number" => array(
                "fld" => "jan_aadhar_number",
                "label" => "जन आधार संख्या (Jan Aadhar Number)",
                "value" => @$master->application->jan_aadhar_number
            ),
            "aadhar_number" => array(
                "fld" => "aadhar_number",
                "label" => "आधार नंबर (Aadhar Number)",
                "value" => @$master->application->aadhar_number
            ), "are_you_from_rajasthan	" => array(
                "fld" => "are_you_from_rajasthan	",
                "label" => "क्या आप राजस्थान के मूल निवासी हैं? (Are You domicile of Rajasthan)",
                "value" => @$are_you_from_rajasthan[@$master->are_you_from_rajasthan]
            ),
            "email" => array(
                "fld" => "email",
                "label" => "ईमेल (Email)",
                "value" => @$master->email
            ), "rural_urban" => array(
                "fld" => "rural_urban",
                "label" => "शहरी /ग्रामीण (Rural/Urban)",
                "value" => @$rural_urban[@$master->application->rural_urban]
            ),
            "employment" => array(
                "fld" => "employment",
                "label" => "रोज़गार (Employment)",
                "value" => @$employment[@$master->application->employment]
            ), "pre_qualification" => array(
                "fld" => "pre_qualification",
                "label" => "पूर्व योग्यता (Pre Qualification)",
                "value" => @$pre_qualifi[@$master->application->pre_qualification]
            )
        );
        if (@$master->stream && $master->stream == 2) {

        } else {
            $output['personalDetails']['book_learning_type_id'] = array(
                "fld" => "book_learning_type_id",
                "label" => "शिक्षण ई-सामग्री/किताबें ?(Types of learning e-content/books?)",
                "value" => @$booklearningtype
            );
        }

        if (@$master->is_dgs == '1') {
            $output['personalDetails']['is_dgs'] = array(
                "fld" => "is_dgs",
                "label" => "पिछड़े वर्ग के छात्र(Disadvantage Group Student)",
                "value" => @$yesno[@$master->is_dgs],
            );

            $output['personalDetails']['user_name'] = array(
                "fld" => "username",
                "label" => "उपयोगकर्ता नाम(UserName)",
                "value" => @$master->username,
            );
        }


        $super_admin_id = Config::get("global.super_admin_id");
        $documentverifications = DocumentVerification::where('student_id', $student_id)
            ->where('role_id', $super_admin_id)
            // ->whereNotNull('challan_tid')
            ->orderby("id", "DESC")->first();
        $output['verifiyDetails'] = array();
        if (@$master->exam_year > 126) {
            $output['verifiyDetails'] = array(
                "verifier_status" => array(
                    "fld" => "verifier_status",
                    "label" => "सत्यापनकर्ता (Verifier)",
                    "value" => @$fresh_student_verfication_status[@$master->verifier_status]
                )
            );
            if (@$master->verifier_status != 1) {
                $output['verifiyDetails'] = array(
                    "department_status" => array(
                        "fld" => "department_status",
                        "label" => "विभाग(Department)",
                        "value" => ($master->department_status == 4 && $documentverifications->challan_tid == null) ? "Clarification of payment pending at the student level." : @$fresh_student_verfication_status[@$master->department_status]
                    )
                );
            }
            if (@$master->ao_status != 1 && @$master->ao_status != '') {
                $output['verifiyDetails'] = array(
                    "ao_status" => array(
                        "fld" => "ao_status",
                        "label" => "शैक्षणिक अधिकारी(Academic Officer)",
                        "value" => @$fresh_student_verfication_status[@$master->ao_status]
                    )
                );
            }


        }

        $output['addressDetails'] = array(
            "address1" => array(
                "fld" => "address1",
                "label" => " पता पंक्ति 1 (Address Line 1)",
                "value" => @$master->address->address1
            ),
            "address2" => array(
                "fld" => "address2",
                "label" => "पता पंक्ति 2 (Address Line 2)",
                "value" => @$master->address->address2
            ),
            "address3" => array(
                "fld" => "address3",
                "label" => "पता पंक्ति 3 (Address Line 3)",
                "value" => @$master->address->address3
            ),
            "state_name" => array(
                "fld" => "state_name",
                "label" => " राज्य (State)",
                "value" => @$master->address->state_name
            ),
            "district_name" => array(
                "fld" => "district_name",
                "label" => " जिला (District)",
                "value" => @$master->address->district_name
            ),
            "tehsil_name" => array(
                "fld" => "tehsil_name",
                "label" => "तहसील (Tehsil)",
                "value" => @$master->address->tehsil_name
            ),
            "block_name" => array(
                "fld" => "block_name",
                "label" => "ब्लॉक/(Block)",
                "value" => @$master->address->block_name
            ),
            "city_name" => array(
                "fld" => "city_name",
                "label" => " शहर / गाँव (City/Village)",
                "value" => @$master->address->city_name
            ),
            "pincode" => array(
                "fld" => "pincode",
                "label" => " पिन कोड (Pincode)",
                "value" => @$master->address->pincode
            )
        );

        if (@$master->address->is_both_same) {

            $output['currentAddressDetails'] = array(
                "is_both_same" => array(
                    "fld" => "is_both_same",
                    "label" => "यदि स्थायी और पत्राचार पता समान है तो (if the permanent and correspondence address are same)",
                    "value" => @$yesno[@$master->address->is_both_same]
                )
            );
        } else {
            $output['currentAddressDetails'] = array(

                "current_address1" => array(
                    "fld" => "address1",
                    "label" => " पता पंक्ति 1 (Address Line 1)",
                    "value" => @$master->address->current_address1
                ),
                "current_address2" => array(
                    "fld" => "address2",
                    "label" => "पता पंक्ति 2 (Address Line 2)",
                    "value" => @$master->address->current_address2
                ),
                "current_address3" => array(
                    "fld" => "address3",
                    "label" => "पता पंक्ति 3 (Address Line 3)",
                    "value" => @$master->address->current_address3
                ),
                "current_state_name" => array(
                    "fld" => "state_name",
                    "label" => " राज्य (State)",
                    "value" => @$master->address->current_state_name
                ),
                "current_district_name" => array(
                    "fld" => "district_name",
                    "label" => " जिला (District)",
                    "value" => @$master->address->current_district_name
                ),
                "current_tehsil_name" => array(
                    "fld" => "tehsil_name",
                    "label" => "तहसील (Tehsil)",
                    "value" => @$master->address->current_tehsil_name
                ),
                "current_block_name" => array(
                    "fld" => "block_name",
                    "label" => "ब्लॉक/(Block)",
                    "value" => @$master->address->current_block_name
                ),
                "current_city_name" => array(
                    "fld" => "city_name",
                    "label" => " शहर / गाँव (City/Village)",
                    "value" => @$master->address->current_city_name
                ),
                "current_pincode" => array(
                    "fld" => "pincode",
                    "label" => " पिन कोड (Pincode)",
                    "value" => @$master->address->current_pincode
                )
            );
        }


        $output['bankDetails'] = array(
            "account_holder_name" => array(
                "fld" => "account_holder_name",
                "label" => " Account Holder Name (खाता धारक का नाम)",
                "value" => @$master->bankdetils->account_holder_name
            ),
            "branch_name" => array(
                "fld" => "branch_name",
                "label" => "शाखा का नाम (Branch Name)",
                "value" => @$master->bankdetils->branch_name
            ),
            "account_number" => array(
                "fld" => "account_number",
                "label" => "खाता संख्या (Account Number)",
                "value" => @$master->bankdetils->account_number
            ),
            "ifsc_code" => array(
                "fld" => "ifsc_code",
                "label" => " आईएफएससी कोड (IFSC Code)",
                "value" => @$master->bankdetils->ifsc_code
            ),
            "bank_name" => array(
                "fld" => "bank_name",
                "label" => " बैंक का नाम (Bank Name)",
                "value" => @$master->bankdetils->bank_name
            ),
            "linked_mobile" => array(
                "fld" => "linked_mobile",
                "label" => "मोबाइल (mobile)",
                "value" => @$master->bankdetils->linked_mobile
            ),
        );

        $output['studentfeesDetails'] = array(
            "registration_fees" => array(
                "fld" => "registration_fees",
                "label" => " पंजीकरण शुल्क (Registration Fees)",
                "value" => @$master->studentfee->registration_fees
            ),
            "readm_exam_fees" => array(
                "fld" => "readm_exam_fees",
                "label" => "परीक्षा शुल्क(Exam Fees)",
                "value" => @$master->studentfee->readm_exam_fees
            ),
            "forward_fees" => array(
                "fld" => "forward_fees",
                "label" => "अग्रिम  शुल्क(Forward Fees)",
                "value" => @$master->studentfee->forward_fees
            ),
            "late_fee" => array(
                "fld" => "late_fee",
                "label" => "विलम्ब शुल्क (Late Fees)",
                "value" => @$master->studentfee->late_fee
            )
        );

        if (@$master->challan_tid) {
            $output['TransactionDetails'] = array(
                "challan_tid " => array(
                    "fld" => "challan_tid ",
                    "label" => " चालान संख्या (Challan Number)",
                    "value" => @$master->challan_tid
                ),
                "submitted" => array(
                    "fld" => "submitted",
                    "label" => "शुल्क जमा तिथि( Fees Submitted Date)",
                    "value" => @$master->submitted
                ),
            );
        }

        $super_admin_id = Config::get("global.super_admin_id");
        $documentverifications = DocumentVerification::where('student_id', $student_id)
            ->where('role_id', $super_admin_id)
            ->whereNotNull('challan_tid')
            ->orderby("id", "DESC")->first();
        $output['ClarificaitonTransactionDetails'] = array();
        if (@$documentverifications && @$documentverifications->challan_tid) {
            $output['ClarificaitonTransactionDetails'] = array(
                "challan_tid " => array(
                    "fld" => "challan_tid ",
                    "label" => " चालान संख्या (Challan Number)",
                    "value" => @$documentverifications->challan_tid
                ),
                "submitted" => array(
                    "fld" => "submitted",
                    "label" => "शुल्क जमा तिथि( Fees Submitted Date)",
                    "value" => @$documentverifications->submitted
                ),
            );
        }


        if ($master->adm_type != 5) {
            $output['studentfeesDetails']["add_sub_fees"] = array(
                "fld" => "add_sub_fees",
                "label" => "विषय जोड़ें शुल्क (Add Sub Fees)",
                "value" => @$master->studentfee->add_sub_fees
            );
            $output['studentfeesDetails']["toc_fees"] = array(
                "fld" => "toc_fees",
                "label" => " टीओसी शुल्क (Toc Fees)",
                "value" => @$master->studentfee->toc_fees
            );

            $output['studentfeesDetails']["practical_fees"] = array(
                "fld" => "practical_fees",
                "label" => "प्रायोगिक शुल्क (Practical Fees)",
                "value" => @$master->studentfee->practical_fees
            );


        } else if ($master->adm_type == 3) {
            $output['studentfeesDetails']["practical_fees"] = array(
                "fld" => "practical_fees",
                "label" => "प्रायोगिक शुल्क (Practical Fees)",
                "value" => @$master->studentfee->practical_fees
            );

        }
        $output['studentfeesDetails']["total"] = array(
            "fld" => "total",
            "label" => "कुल  शुल्क(	Total Fees)",
            "value" => @$master->studentfee->total
        );
        if (@$makepaymentchangerequerts == 'true' && @$changerequeststudent->student_change_requests == 2) {
            $output['studentfeesDetails']["previous_fees"] = array(
                "fld" => "total",
                "label" => "पिछली फीस का भुगतान किया गया(	Previous fees paid)",
                "value" => @$master->changerequestoldfees->total
            );

            $output['studentfeesDetails']["remaining_fees"] = array(
                "fld" => "total",
                "label" => @$labelchangerequest,
                "value" => @$master->studentfee->total - @$master->changerequestoldfees->total
            );
        }
        if (@$makepaymentchangerequerts == 'false' && @$changerequeststudent->student_change_requests == 2) {
            $output['studentfeesDetails']["previous_fees"] = array(
                "fld" => "total",
                "label" => "पिछली फीस का भुगतान किया गया(	Previous fees paid)",
                "value" => @$master->changerequestoldfees->total
            );
        }
        foreach (@$master->admission_subject as $k => $v) {
            $extraFlag = null;

            if ($v['is_additional'] == 1) {
                $extraFlag = "<span style='color:blue;font-weight:900;'>(Additional)</span>";
            }
            $output['admissionSubjectDetails'][] = array(
                "fld" => "subject_id",
                "label" => " Subject " . ($k + 1),
                "value" => @$subject_list[$v['subject_id']] . $extraFlag
            );
        }


        // foreach(@$master->toc_subject as $k => $v){
        // 	$output['tocSubjectDetails'][] = array(
        // 		"fld" => "subject_id",
        // 		"label" => " (Subject ". ($k+1) . ")",
        // 		"value" => @$subject_list[$v['subject_id']]
        // 	);
        // }

        foreach (@$master->exam_subject as $k => $v) {
            $selected_exam_year = CustomHelper::_get_selected_sessions();
            if ($v['exam_year'] != $selected_exam_year) {
                continue;
            }
            $extraFlag = null;
            if ($v['is_additional'] == 1) {
                $extraFlag = "<span style='color:blue;font-weight:900;'>(Additional)</span>";
            }
            $output['examSubjectDetails'][] = array(
                "fld" => "subject_id",
                "label" => " Subject  " . ($k + 1),
                "value" => @$subject_list[$v['subject_id']] . $extraFlag
            );
        }


        $documents = $this->getStudentRequriedDocuments($student_id);

        if (@$documents) {
            foreach (@$documents as $k => $v) {
                $output['documentDetails'][$k] = array(
                    "fld" => $k,
                    "label" => $v,
                    // "value" => "<a title= 'Click here to download " . @$v ."' href=".route('download',Crypt::encrypt('/'.$studentDocumentPath . "/" . @$master->document->$k)) . "><i class='material-icons'>file_download</i></a>"
                    "value" => "<a target='_blank' title='Click here to verify " . @$v . "' href=" . url('public/' . $studentDocumentPath . "/" . @$master->document->$k) . "><span class='material-icons'>link</span></a>"
                );
            }
        }


        $output = array(
            "personalDetails" => array(
                "seciontLabel" => "Personal Details",
                "data" => @$output['personalDetails']
            ),
            "verifiyDetails" => array(
                "seciontLabel" => "Verification Details",
                "data" => @$output['verifiyDetails']
            ),
            "addressDetails" => array(
                "seciontLabel" => "Address Details",
                "data" => @$output['addressDetails']
            ),
            "currentAddressDetails" => array(
                "seciontLabel" => "Correspondence Address Details",
                "data" => @$output['currentAddressDetails']
            ),
            "bankDetails" => array(
                "seciontLabel" => "Bank Details",
                "data" => @$output['bankDetails']
            ),
            "admissionSubjectDetails" => array(
                "seciontLabel" => "Admission Subject Details",
                "data" => @$output['admissionSubjectDetails']
            ),
            // "tocSubjectDetails" => array(
            // 	"seciontLabel" => "TOC Subjects Details",
            // 	"data" => @$output['tocSubjectDetails']
            // ),
            "examSubjectDetails" => array(
                "seciontLabel" => "Exam Subjects Details",
                "data" => @$output['examSubjectDetails']
            ),
            "documentDetails" => array(
                "seciontLabel" => "Document Details",
                "data" => @$output['documentDetails']
            ),
            "studentfeesDetails" => array(
                "seciontLabel" => "Student Fees Details",
                "data" => @$output['studentfeesDetails']
            ),
            // "TransactionDetails" => array(
            // "seciontLabel" => "Transaction Details",
            // "data" => @$output['TransactionDetails']
            // ),
            // "ClarificaitonTransactionDetails" => array(
            // "seciontLabel" => "Clarificaiton Transaction Details",
            // "data" => @$output['ClarificaitonTransactionDetails']
            // ),
        );
        if (@$output['TransactionDetails']['data']) {
            $output["TransactionDetails"] = array(
                "seciontLabel" => "Transaction Details",
                "data" => @$output['TransactionDetails']
            );
        }
        if (@$output['ClarificaitonTransactionDetails']['data']) {
            $output["TransactionDetails"] = array(
                "seciontLabel" => "Clarificaiton Transaction Details",
                "data" => @$output['ClarificaitonTransactionDetails']
            );
        }


        return $output;
    }

    public function changerequestcheckfees($student_id)
    {
        @$master = StudentFee::where('student_id', $student_id)->first('total');
        @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        @$getstudenttotalfeesold = ChangeRequertOldStudentFees::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)->orderBy('id', 'desc')->first('total');
        @$getstudenttotalfeesnew = @$master->total;
        if (@$getstudenttotalfeesold->total == @$getstudenttotalfeesnew) {
            $makepaymentchangerequerts = 'false';
        } elseif (@$getstudenttotalfeesold->total < @$getstudenttotalfeesnew) {
            $makepaymentchangerequerts = 'true';
        } elseif (@$getstudenttotalfeesold->total > @$getstudenttotalfeesnew) {
            @$makepaymentchangerequerts = 'false';
        }
        return @$makepaymentchangerequerts;
    }

    public function getStudentRequriedDocuments($student_id = null)
    {
        $master = Student::with('application')->where('id', $student_id)->first();

        $combo_name = 'adm_type';
        $adm_type = $this->master_details($combo_name);
        $combo_name = 'gender_id';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);

        // dd($master);
        if (!in_array(@$master->application->category_a, array(1))) {
            $cast_certificate = @$categorya[$master->application->category_a];
            $documentInput["cast_certificate"] = "$cast_certificate Cast Certificate";
        }

        if (in_array(@$master->application->disadvantage_group, array(4, 5, 6, 7))) {
            $disadvantage_group = @$categorya[$master->application->disadvantage_group];
            $documentInput["disadvantage_group"] = "$disadvantage_group Disadvantage Group Certificate";
        }
        // dd($master->gender_id);
        if ($master->gender_id == 3) {
            $gender_id = @$gender_id[$master->gender_id];
            $documentInput["gender_id"] = "$gender_id Certificate";
        }

        //if( $master->adm_type == 5){
        //$adm_type = @$adm_type[$master->adm_type];
        //$documentInput["iti_marksheet"] = "$adm_type first year marksheet or first and second semester joint marksheet";
        //}
        // $documentInput["gender_id"] = "Gender Certificate";
        // $documentInput["disadvantage_group"] = "Disadvantage Group Certificate";
        $documentInput["category_a"] = "DOB Certificate";
        $documentInput["category_b"] = "Address Proof Certificate";
        $documentInput["pre_qualification"] = "Previous Qualification Certificate";
        // $documentInput["category_c"] = "Other-I docuemnt Certificate";
        // $documentInput["category_d"] = "Other-II docuemnt Certificate";

        if (isset($master->application->disability) && $master->application->disability > 0 && $master->application->disability < 10) {
            $disability = @$disability[$master->application->disability];
            $documentInput["disability"] = "$disability Disability Certificate";
        }

        return $documentInput;
    }

    public function get_table_data_by_id($id = null, $table = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($id)) {
            $condtions = ['id' => $id];
        }
        $mainTable = $table;
        $cacheName = $mainTable . $id;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->get()->pluck('name');
                return $result;
            });
        }
        return $result;
    }

    public function getRecordExistorNot($student_id = null, $table = null, $exam_year = null, $exam_month = null)
    {
        $result = array();
        $condtions = array();
        if ($table == "students") {
            if (!empty($student_id)) {
                $condtions = ['id' => $student_id];
            }
        } else if ($table == "applications") {
            if (!empty($student_id)) {
                $condtions = ['student_id' => $student_id, 'is_toc_marked' => 1];
            }
        } else if ($table == "supplementaries") {
            if (!empty($student_id)) {
                if (@$exam_year && @$exam_month) {
                    $condtions = ['student_id' => $student_id, 'exam_year' => $exam_year, 'exam_month' => $exam_month];
                } else {
                    $condtions = ['student_id' => $student_id];
                }

            }
        } else {
            if (!empty($student_id)) {
                $condtions = ['student_id' => $student_id];
            }
        }
        $resultCount = DB::table($table)->where($condtions)->whereNull('deleted_at')->count();

        if ($resultCount > 0) {
            return true;
        }
        return false;

    }

    public function districtsByState($state_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($state_id)) {
            $condtions = ['state_id' => $state_id];
        }
        $mainTable = "districts";
        $cacheName = "districts";
        // return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name','id');

        if (!empty($state_id)) {
            $cacheName = "districts" . $state_id;
        }
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->whereNull('deleted_at')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function getOldDistrictsByState($state_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($state_id)) {
            $condtions = ['state_id' => $state_id];
        }
        $mainTable = "districts";
        $cacheName = "getOldDistrictsByState";
        // return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name','id');

        if (!empty($state_id)) {
            $cacheName = "districts" . $state_id;
        }
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where('id', "<=", 33)->where($condtions)->orderBy('name', 'asc')->whereNull('deleted_at')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function districtOnlyNameById($id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($id)) {
            $condtions = ['id' => $id];
        }
        $mainTable = "districts";
        $cacheName = "districts";
        // return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name','id');

        if (!empty($id)) {
            $cacheName = "districts_" . $id;
        }
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->first('name');
            });
        }
        return $result;
    }

    public function districtOnlyIdByName($name = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($name)) {
            $condtions = ['name' => $name];
        }
        $mainTable = "districts";
        $cacheName = "districts";
        // return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name','id');

        if (!empty($id)) {
            $cacheName = "districts_" . $name;
        }
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->first('id');
            });
        }
        return $result;
    }

    public function tehsilOnlyIdByName($name = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($name)) {
            $condtions = ['name' => $name];
        }
        $mainTable = "tehsils";
        $cacheName = "tehsil";
        // return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name','id');

        if (!empty($id)) {
            $cacheName = "tehsil_" . $name;
        }
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->first('id');
            });
        }
        return $result;
    }

    public function division_details($state_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($state_id)) {
            $condtions = ['state_id' => $state_id];
        }
        $mainTable = "divisions";
        if (Cache::has($mainTable)) { //Cache::forget($mainTable);
            $result = Cache::get($mainTable);
        } else {
            $result = Cache::rememberForever($mainTable, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function getListRsosYears()
    {
        $condtions = null;
        $result = array();
        $mainTable = "rsos_years";
        $cacheName = "rsos_years";
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('yearstext', 'id');
            });
        }
        return $result;
    }

    public function block_details($district_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($district_id)) {
            $condtions = ['district_id' => $district_id];
        }
        $mainTable = "blocks";

        $cacheName = $mainTable . "_";
        if (!empty($district_id)) {
            $cacheName = $mainTable . "_" . $district_id;
        }
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function temp_block_details($temp_district_id = null, $req_type = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($temp_district_id)) {
            $condtions = ['district_id' => $temp_district_id];
        }
        if (!empty($req_type)) {
            //$condtions[$req_type] = 1;
        }

        //dd($condtions);
        $mainTable = "blocks";
        $cacheName = $mainTable . "_temp_district_id";
        if (!empty($temp_district_id)) {
            $cacheName = $mainTable . "_" . $temp_district_id;
        }
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($cacheName);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function state_details()
    {
        $condtions = array();
        $result = array();
        $mainTable = "states";
        $cacheName = "states_list_final";

        $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->whereNull('deleted_at')->get()->pluck('name', 'id');
        return $result;

        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->whereNull('deleted_at')->get()->pluck('name', 'id');

                return $result;
            });
        }
        return $result;
    }

    public function _get_subject_faculty_wise($faculty_type_id = null)
    {
        $condtions = array();
        $result = array();
        $mainTable = "states";
        $cacheName = "states_list_final";
        $customConditions = array();
        $custom_component_obj = new CustomComponent;
        $isStudent = $custom_component_obj->_getIsStudentLogin();
        $course = null;
        if (@$isStudent) {
            $course = Session::get('studentcourse');
            // $course = @Auth::guard('student')->user()->course;
        }
        if ($course != null) {
            $customConditions['course'] = $course;
        }
        if ($faculty_type_id == 1) {
            $customConditions['is_science_faculty'] = 1;
        } else if ($faculty_type_id == 2) {
            $customConditions['is_commerce_faculty'] = 1;
        } else if ($faculty_type_id == 3) {
            $customConditions['is_arts_faculty'] = 1;
        } else if ($faculty_type_id == 4) {
            $customConditions['is_agricultre_faculty'] = 1;
        }

        if (@$customConditions) {
            $result = Subject::where($customConditions)->pluck('name', 'id');
        } else {
            $result = Subject::pluck('name', 'id');
        }

        return @$result;
    }

    public function tehsilsByDistrictId($district_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($district_id)) {
            $condtions = ['district_id' => $district_id];
        }
        $mainTable = "tehsils";
        $cacheName = $mainTable . "_" . $district_id;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {

                return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function subjectListArray($course = null)
    {
        $condtions = null;
        $result = array();
        if ($course != null) {
            $condtions = ['course' => $course, 'deleted' => 0];
        } else {
            $condtions = ['deleted' => 0];
        }

        $mainTable = "subjects";
        $cacheName = "Subjects_Arr_Update_2" . $course;
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->get()->pluck('name', 'id')->toArray();

            });
        }
        return $result;
    }

    public function subjectIdByCode($subject_code = null)
    {
        $condtions = null;
        $result = array();
        if ($subject_code != null) {
            $condtions = ['subject_code' => $subject_code, 'deleted' => 0];
        } else {
            $condtions = ['deleted' => 0];
        }

        $mainTable = "subjects";
        $cacheName = "Subject_ID_By_Code_Update_2" . $subject_code;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->first(['subject_code', 'id']);
                return $result = @$result->id;

            });
        }
        return $result;
    }

    public function _sendTestSMS($student_id = null)
    {
        $status = false;
        $student = Student::where('id', $student_id)->first();
        if (@$student) {
            $studentName = $student->name;
            $enrollment = $student->enrollment;
            $date = "28/01/2024";

            $sms = "प्रिय " . $studentName . " नामांकन संख्या " . $enrollment . " आपके RSOS आवेदन को SSOID से मैप (जोड़े) करने के लिए rsosadmission.rajasthan.gov.in पर जाकर प्रक्रिया अनुसार मैप (जोड़े) करें अथवा अपने संदर्भ केन्द्र पर जाकर दिनांक " . $date . " तक अनिवार्य रुप से मैप (जोड़े) कराएं अन्यथा आवेदन निरस्त हो सकता है| - RSOS,GoR";
            $templateID = "1107170505696985578";
            $this->_sendSMS($student->mobile, $sms, $templateID);
            $status = true;
        }
        return $status;
    }

    public function _sendSMS($mobile, $sms, $templateID = null)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        // echo Configure::read("Message.application_sendsms");die;
        // if(Configure::read("Message.application_sendsms") == 1){
        // $mobile = 8946919241;
        if ($mobile == "" || $sms == "") {
            return false;
        }
        $curl = curl_init();
        $client_id = 'e6bc53b8-14c4-4501-b8f7-1abd83faf77e';
        $CURLOPT_POSTFIELDS = array();
        $CURLOPT_POSTFIELDS['UniqueID'] = 'HGHTCH_EDU_SMS';
        $CURLOPT_POSTFIELDS['username'] = 'HighEduSms';
        $CURLOPT_POSTFIELDS['password'] = 'Ed#MsmDt_0o1';
        $CURLOPT_POSTFIELDS['serviceName'] = 'eSanchar Send SMS Request';
        $CURLOPT_POSTFIELDS['language'] = 'HIN';
        $CURLOPT_POSTFIELDS['message'] = $sms;
        $CURLOPT_POSTFIELDS['mobileNo'] = array();
        $CURLOPT_POSTFIELDS['mobileNo'][] = $mobile;
        if ($templateID != "") {
            $CURLOPT_POSTFIELDS['templateID'] = $templateID;
        }
        //echo json_encode($CURLOPT_POSTFIELDS); exit;
        curl_setopt_array($curl, array(
            // CURLOPT_URL => "https://api.sewadwaar.rajasthan.gov.in/app/live/eSanchar/Prod/Service/api/OBD/CreateSMS/Request?client_id=$client_id",
            CURLOPT_URL => "https://api.sewadwaar.rajasthan.gov.in/app/live/eSanchar/Prod/Service/api/OBD/CreateOTP/Request?client_id=$client_id",
            //CURLOPT_URL => "https://api.sewadwaar.rajasthan.gov.in/app/live/eSanchar/Prod/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($CURLOPT_POSTFIELDS),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json",
                "username: HighEduSms",
                "password: Ed#MsmDt_0o1"
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // "cURL Error #:" . $err;
        } else {
            // $response;
        }
        // }
        return true;

    }

    public function subjectListName($course = null)
    {
        $condtions = null;
        $result = array();
        if ($course != null) {
            $condtions = ['course' => $course, 'deleted' => 0];
        } else {
            $condtions = ['deleted' => 0];
        }

        $mainTable = "subjects";
        $cacheName = "Subjects_Real_Name_" . $course;
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('real_name', 'id');

            });
        }
        return $result;
    }

    public function subjectCodeList($course = null)
    {
        $condtions = null;
        $result = array();
        if ($course != null && $course != 0) {
            $condtions = ['course' => $course, 'deleted' => 0];
        } else {
            $condtions = ['deleted' => 0];
        }

        $mainTable = "subjects";
        $cacheName = "Subjects_Code_" . $course;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->orderBy('subject_code')->get()->pluck('subject_code', 'id');

            });
        }
        return $result;
    }

    public function subjectPracticalCodeList($course = null)
    {
        $condtions = null;
        $result = array();
        if ($course != null && $course != 0) {
            $condtions = ['course' => $course, 'deleted' => 0, 'practical_type' => 1];
        } else {
            $condtions = ['deleted' => 0, 'practical_type' => 1];
        }
        $mainTable = "subjects";
        $cacheName = "Subjects_Code_Pracitcal_Type_" . $course;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->orderBy('subject_code')->get()->pluck('subject_code', 'id');

            });
        }
        return $result;
    }

    public function subjectTypeList($course = null)
    {
        $condtions = null;
        $result = array();
        if ($course != null && $course != 0) {
            $condtions = ['course' => $course, 'deleted' => 0];
        } else {
            $condtions = ['deleted' => 0];
        }

        $mainTable = "subjects";
        $cacheName = "Subjects_Type_" . $course;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('subject_type', 'id');

            });
        }
        return $result;
    }

    public function getBoardList()
    {
        $condtions = null;
        $result = array();
        $mainTable = "boards";
        $cacheName = "boards";
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('name', 'id');

            });
        }
        return $result;
    }

    public function getAdmissionTypeBords($admtype = null)
    {
        $boards = array();
        if ($admtype == 1 || $admtype == 3 || $admtype == 5) { //General,
            $boards = DB::table('boards')->get()->pluck('name', 'id');
        } elseif ($admtype == 2) { //Re-Admission
            $boards = DB::table('boards')->whereIn('id', [81])->pluck('name', 'id');
        } elseif ($admtype == 4) { //Improvment
            $boards = DB::table('boards')
                ->whereIn('id', [81, 56])
                ->pluck('name', 'id');
        }

        return $boards;
    }

    public function getfailandpassingyears($adm_type, $stream, $board, $toc)
    {

        $result = DB::table('toc_validations')->where([
            'adm_type' => $adm_type,
            'board_id' => $board,
        ])->first();
        return $result;
    }

    public function getRsosYearsList()
    {
        $condtions = null;
        $result = array();
        $mainTable = "rsos_years";
        $cacheName = "rsos_years";
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('yearstext', 'id');

            });
        }
        return $result;
    }

    public function getTestRsosYearsList($board = null)
    {
        $condtions = null;
        $result = array();
        $mainTable = "rsos_years";
        $cacheName = "rsos_years_" . $board;
        if ($board != null) {
            return $result;
        }
        if ($board != null && $board == 81) {
            $condtions = ['for_rsos' => 1];
        } else {
            $condtions = ['for_rsos_other' => 1];
        }
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('yearstext', 'id');
            });
        }
        return $result;
    }

    public function getRsosFailYearsList($board)
    {
        $condtions = null;
        $result = array();
        $mainTable = "rsos_years_fail";
        $cacheName = "rsos_years_fail_" . $board;

        if ($board != null && $board == 81) {
            $condtions = ['for_rsos' => 1];
        } else {
            $condtions = ['for_rsos_other' => 1];
        }

        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('yearstext', 'id');
            });
        }
        return $result;
    }

    public function studentSubjectDropdown($student_id = null, $isImprovementStudent = null)
    {
        $subject_arr = $this->subjectList();
        $result = $this->studentSubjectData($student_id, $isImprovementStudent);
        $student_subject_list = array();
        foreach ($result as $each) {
            if (isset($subject_arr[$each])) {
                $student_subject_list[$each] = $subject_arr[$each];
            } else {
                $student_subject_list[$each] = '';
            }
        }
        return $student_subject_list;
    }

    public function studentSubjectData($student_id = null, $isImprovementStudent = null)
    {
        $condtions = null;
        $result = array();
        $condtions['student_id'] = $student_id;
        if ($isImprovementStudent == true) {
        } else {
            $condtions['is_additional'] = 0;
        }
        $mainTable = "admission_subjects";
        $cacheName = "AdjustStudentDetailsbjectUp" . $student_id . $isImprovementStudent;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {  //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->get()->pluck('subject_id', 'id');
            });
        }
        return $result;
    }

    public function studentSubjectCount($student_id = null, $isImprovementStudent = null)
    {
        $result = $this->studentSubjectData($student_id, $isImprovementStudent);
        return COUNT($result);
    }

    public function getSubjectCountAdmType($adm_type)
    {
        $condtions = null;
        $result = array();
        $condtions = ['adm_type_id' => $adm_type];
        $mainTable = "subject_adm_type";
        $cacheName = "SubjectAdmType" . $adm_type;
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->first();
            });
        }
        return $result;
    }

    public function getStudentDocumentListForVerification($student_id = null)
    {
        $docsList = Document::where('student_id', $student_id)->first();
        $documentInput = array();

        $role_id = Session::get('role_id');
        $baseName = null;
        $baseNameLast = "_is_verify";

        $academicofficer_id = Config::get('global.academicofficer_id');

        if ($role_id == Config::get('global.verifier_id')) {
            $baseNameFirst = "verifier_";
        } elseif ($role_id == Config::get('global.super_admin_id')) {
            $baseNameFirst = "dept_";
        } else if ($role_id == $academicofficer_id) {
            $baseNameFirst = "ao_";
        }
        $mainFldName = "photograph";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = ucfirst($mainFldName);
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "signature";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = ucfirst($mainFldName);
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "category_a";
        $mainFldNames = "DOB Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "category_b";
        $mainFldNames = "Address Proof Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "category_c";
        $mainFldNames = "Category C Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }


        $mainFldName = "category_d";
        $mainFldNames = "Category D Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "pre_qualification";
        $mainFldNames = "Pre Qualification Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "disability";
        $mainFldNames = "Disability Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }
        return $documentInput;
    }

    public function getStudentClarificationDocumentListForVerification($student_id = null)
    {
        $docsList = StudentDocumentVerification::where('student_id', $student_id)->orderby("id", "DESC")->first();

        $documentInput = array();

        $role_id = Session::get('role_id');
        $baseName = null;
        $baseNameLast = "_is_verify";
        if (Session::get('role_id') == Config::get('global.verifier_id')) {
            $baseNameFirst = "verifier_";
        } elseif (Session::get('role_id') == Config::get('global.super_admin_id')) {
            $baseNameFirst = "dept_";
        }
        $mainFldName = "photograph";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;

        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = ucfirst($mainFldName);
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "signature";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = ucfirst($mainFldName);
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "category_a";
        $mainFldNames = "DOB Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "category_b";
        $mainFldNames = "Address Proof Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }
        $mainFldName = "category_c";
        $mainFldNames = "Category C Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "category_d";
        $mainFldNames = "Category D Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "pre_qualification";
        $mainFldNames = "Pre Qualification Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }

        $mainFldName = "disability";
        $mainFldNames = "Disability Certificate";
        $fld = $baseNameFirst . $mainFldName . $baseNameLast;
        if (@$docsList->$mainFldName) {
            $documentInput[$fld]['value'] = $docsList->$mainFldName;
            $documentInput[$fld]["label"] = $mainFldNames;
            $documentInput[$fld]["main_fld_name"] = $mainFldName;
        }
        return $documentInput;
    }

    public function getStudentRequriedDocument($student_id = null)
    {
        $master = Student::with('application')->where('id', $student_id)->first();


        $documentInput["category_a"] = "Upload DOB Certificate " . config('global.starMark');
        $documentInput["category_b"] = "Upload Address Proof Certificate " . config('global.starMark');
        // $documentInput["gender_id"] = "Upload Gender Certificate ".config('global.starMark');
        if ($master->course == 10 && $master->adm_type == 5) {
            $documentInput["pre_qualification"] = "Upload 8th Marksheet" . config('global.starMark');
        } elseif ($master->course == 12 && $master->adm_type == 5) {
            $documentInput["pre_qualification"] = "Upload 10th Marksheet " . config('global.starMark');
        } else {
            $documentInput["pre_qualification"] = "Upload Pre Qualification Certificate " . config('global.starMark');
        }
        if ($master->adm_type == 5) {
            $combo_name = 'adm_type';
            $categorya = $this->master_details($combo_name);
            $documentInput["iti_marksheet"] = "Upload " . @$categorya[$master->adm_type] . " first year marksheet or first and second semester joint marksheet " . config('global.starMark');
            $documentInput['label']["adm_type_label"] = "";
        }

        // $documentInput["disadvantage_group"] = "Upload Disadvantage Group Certificate ". config('global.starMark');
        $documentInput['label']["category_a_label"] = "(DOB) : Affidavit(Footpath &amp; Orphan) Or Birth Certificate Or Marksheet/TC Or Seva-Pustika Or Transfer Certificate " . config('global.starMark');
        $documentInput['label']["category_b_label"] = "(Address Proof) : Aadhar Card Or Bhamashah Card Or Driving License Or Ration Card Or Voter ID Card " . config('global.starMark');
        $documentInput['label']["pre_qualification_label"] = "";


        if (in_array($master->application['disability'], array(1, 2, 3, 4, 5, 6, 7, 8, 9))) {
            $combo_name = 'disability';
            $disability = $this->master_details($combo_name);
            $documentInput["disability"] = "Upload Disability " . $disability[$master->application['disability']] . " Certificate " . config('global.starMark');
            $documentInput['label']["disability_label"] = "";
        }

        // if (!in_array($master->application['category_a'], array(1,4,5))){
        if (!in_array($master->application['category_a'], array(1))) {
            $combo_name = 'categorya';
            $categorya = $this->master_details($combo_name);
            $documentInput["cast_certificate"] = "Upload " . @$categorya[$master->application['category_a']] . " Caste Category  Certificate " . config('global.starMark');
            $documentInput['label']["caste_certificate_label"] = "";
        }
        if ($master->gender_id == 3) {
            $combo_name = 'gender_id';
            $categorya = $this->master_details($combo_name);
            $documentInput["gender_id"] = "Upload " . @$gender_id[$master->gender_id] . " Gender Certificate " . config('global.starMark');
            $documentInput['label']["gender_id_label"] = "";
        }

        // dd($master->gender_id);
        // dd($documentInput);

        if (in_array($master->application['disadvantage_group'], array(4, 5, 6, 7))) {
            $combo_name = 'dis_adv_group';
            $dis_adv_group = $this->master_details($combo_name);
            $documentInput["disadvantage_group"] = "Upload Disadvantage Group " . @$dis_adv_group[$master->application['disadvantage_group']] . "  Certificate " . config('global.starMark');
            $documentInput['label']["disadvantage_group_label"] = "";
        }

        $documentInput["category_c"] = "Upload Other-I document Certificate ";
        $documentInput["category_d"] = "Upload Other-II document Certificate";
        $documentInput['label']["category_c_label"] = "";
        $documentInput['label']["category_d_label"] = "";

        return $documentInput;
    }

    public function getPendingDocuemntDetails($student_id = null)
    {
        $documentErrors = array();
        $manDocuemnts = $this->getStudentRequriedDocuments($student_id);


        $documents = Document::where('student_id', $student_id)->first();
        if (empty($documents)) {
            $documentErrors[] = "Please upload Document first.";
            return $documentErrors;
        }

        $counter = 0;
        foreach (@$manDocuemnts as $k => $v) {
            if (@$documents->$k == "") {
                $counter++;
                $documentErrors[] = $counter . ". Please upload " . $v . ".";
            }
        }
        return $documentErrors;
    }

    public function getSubjectvalidations($adm_type = null, $course = null, $is_stream1 = null, $last_year_qualification = null, $is_last_year_passout = null)
    {
        $condtions = null;
        $result = array();
        $mainTable = "subject_validations";

        $cacheName = "SubjectValidations" . $adm_type . "_ " . $course . "_ " . $is_stream1 . "_ " . $last_year_qualification . "_ " . $is_last_year_passout;

        $fld = "adm_type";
        if (!empty($fld)) {
            $condtions[$fld] = $$fld;
        }
        $fld = "course";
        if (!empty($fld)) {
            $condtions[$fld] = $$fld;
        }

        //echo $is_stream1."</br>";
        //echo $last_year_qualification;
        //echo $is_last_year_passout;
        if ($is_stream1 == 1 && $last_year_qualification == 10 && $is_last_year_passout == 1) {
            $fld = "";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "last_year_qualification";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_last_year_passout";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        }

        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->first();

            });
        }
        // @dd($result);
        return $result;
    }

    public function getSuppSubjectvalidations($adm_type = null, $course = null)
    {
        $condtions = null;
        $result = array();
        $mainTable = "supp_subject_validations";

        $cacheName = "SuppSubjectValidations" . $adm_type . "_ " . $course;

        $fld = "adm_type";
        if (!empty($fld)) {
            $condtions[$fld] = $$fld;
        }
        $fld = "course";
        if (!empty($fld)) {
            $condtions[$fld] = $$fld;
        }

        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->first();
            });
        }
        return $result;
    }

    public function oldcheckIsPracticalSubject($subject_id = null, $board_id = null)
    {

        $condtions = null;
        $result = false;
        $condtions['deleted'] = 0;
        $condtions['id'] = $subject_id;
        $condtions['practical_type'] = 1;

        $mainTable = "subjects";
        $cacheName = "IsPracticalSubject_" . $subject_id;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->count();
                if ($result > 0) {
                    $result = true;
                } else {
                    $result = false;
                }
                return $result;
            });
        }
        return $result;
    }

    public function getCountFacultySubjectSelection($inputs = null, $subjectCountDetails = null, $course = null, $faculty_type_id = null)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $customConditions = array();
        $subjectMaster = $commerceFaculty = $artsFaculty = $scienceFaculty = array();
        if ($faculty_type_id == 1) {
            $customConditions['is_science_faculty'] = 1;
        } else if ($faculty_type_id == 2) {
            $customConditions['is_commerce_faculty'] = 1;
        } else if ($faculty_type_id == 3) {
            $customConditions['is_arts_faculty'] = 1;
        } else if ($faculty_type_id == 4) {
            $customConditions['is_agricultre_faculty'] = 1;
        }
        $subjectMaster = $artsFaculty = Subject::where($customConditions)->pluck('id', 'id')->toArray();

        $InvalidSujbectAsFacultySeleted = false;
        if (!empty(@$inputs)) {
            foreach (@$inputs as $k => $v) {
                if ($v == 'Select Any') {
                    unset($inputs[$k]);
                }
            }
            foreach (@$inputs as $k => $subject_id) {
                if (@$subject_id) {
                    if (!in_array($subject_id, $subjectMaster)) {
                        $InvalidSujbectAsFacultySeleted = true;
                        break;
                    }
                }
            }

            if ($InvalidSujbectAsFacultySeleted) {
                $fld = 'faculty_type_id';
                $errMsg = 'You have select multiple faculty in subject.';
                $errors = $errMsg;
                $validator->getMessageBag()->add($fld, $errMsg);
                $isValid = false;
            }
        }
        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;

        return $response;
    }

    public function isValidSubjectSelection($inputs = null, $subjectCountDetails = null, $course = null, $faculty_type_id = null)
    {
        $filledComSubjects = 0;
        $filledAddiSubjects = 0;
        $filledComLanSubjects = 0;
        $filledAddiLangSubjects = 0;

        $isValid = true;
        $errors = null;

        $validator = Validator::make([], []);

        if (!empty(@$inputs)) {
            foreach (@$inputs as $k => $v) {
                if ($v == 'Select Any') {
                    unset($inputs[$k]);
                }
            }
            foreach (@$inputs as $k => $v) {
                $subject_id = $v;
                $filledSubjects[] = $subject_id;

                $requiredAddSubArray = array();
                if ($subjectCountDetails->addi_subject_requried_count == 1) {
                    $requiredAddSubArray = array('5');
                } else if ($subjectCountDetails->addi_subject_requried_count == 2) {
                    $requiredAddSubArray = array('5', '6');
                }

                if ($v == null && in_array($k, $requiredAddSubArray)) {
                    $fld = 'subject_id';
                    $errMsg = 'Please select mandatory subject.';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }

                if ($isValid) {
                    //here get subjects count with com and add max and min
                    if ($k >= 0 && $k <= 4) {
                        if ($v != null) {
                            $filledComSubjects++;
                        }
                    }
                    if ($k == 5 || $k == 6) {
                        if ($v != null) {
                            $filledAddiSubjects++;
                        }
                    }
                }

                if ($isValid) {
                    //here get subjects count with com and add max and min
                    if ($k >= 0 && $k <= 4) { //compolsory
                        if ($v != null) {
                            //funciton check is lang or not
                            $isLangSubject = $this->checkIsLanguageSubject($subject_id);
                            if ($isLangSubject) {
                                $filledComLanSubjects++;
                            }
                        }
                    }
                    if ($k == 5 || $k == 6) { //additonal
                        $isLangSubject = $this->checkIsLanguageSubject($subject_id);
                        if ($isLangSubject) {
                            $filledAddiLangSubjects++;
                        }
                    }
                }
            }

            if ($isValid && $course == 12) {
                // for 12th course
                if (empty($faculty_type_id)) {
                    // Hold for temp still waiting for client start
                    // $fld = 'faculty_type_id';
                    // $errMsg = 'Please select Preferred Faculty(मुख्य संकाय).';
                    // $errors[$fld][] = $errMsg;
                    // $validator->getMessageBag()->add($fld, $errMsg);
                    // $isValid = false;
                    // Hold for temp still waiting for client end
                }
            }
            if ($isValid) {

                // echo "subjectCountDetails : "; $this->pr($subjectCountDetails);
                //echo "comp_lang_subject_min_requried_count  : "; $this->pr($subjectCountDetails->comp_lang_subject_min_requried_count); //2
                //echo "comp_lang_subject_max_requried_count  : "; $this->pr($subjectCountDetails->comp_lang_subject_max_requried_count); //2
                // echo "filledComSubjects  : "; $this->pr($filledComSubjects); //4
                //echo "comp_subject_requried_count  : "; $this->pr($subjectCountDetails->comp_subject_requried_count); //1
                //echo "filledComLanSubjects : ".$filledComLanSubjects;

                if (!empty($filledComSubjects) && $filledComSubjects < $subjectCountDetails->comp_subject_requried_count) {
                    //echo "</br>subject error1";
                    $fld = 'subject_id';
                    $errMsg = 'Please select mandatory compulsory subject.';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }

                if (!empty($subjectCountDetails->comp_lang_subject_max_requried_count)
                    && $subjectCountDetails->comp_lang_subject_min_requried_count > $filledComLanSubjects
                    && $subjectCountDetails->comp_lang_subject_max_requried_count > $filledComLanSubjects) {
                    $fld = 'subject_id';
                    $errMsg = 'Please select mandatory language compulsory subject.';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }
                // echo "test"; die;

                if (!empty($filledAddiSubjects) && !empty($subjectCountDetails->addi_subject_requried_count)
                    && $subjectCountDetails->addi_subject_requried_count > 0
                    && $filledAddiSubjects != $subjectCountDetails->addi_subject_requried_count) {
                    $fld = 'subject_id';
                    $errMsg = 'Please select mandatory additional subject.';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }
            }

            if ($isValid) {
                $isUniqueSubects = $this->checkIsUniqueSubject($filledSubjects);
                if (!$isUniqueSubects) {
                    $duplicateSubjectsName = $this->getDuplicateSubject($filledSubjects);
                    $fld = 'subject_id';
                    $errMsg = 'Please select unique compulsory/addtional subjects.  (Duplicate subjects : ' . $duplicateSubjectsName . ')';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }
            }

            if ($isValid) {
                // com min  and com max

                if ($subjectCountDetails->comp_lang_subject_min_requried_count != 'Null' && !empty($filledComLanSubjects) && !empty($subjectCountDetails->comp_lang_subject_min_requried_count) && $subjectCountDetails->comp_lang_subject_min_requried_count > $filledComLanSubjects) {
                    $fld = 'subject_id';
                    $errMsg = 'Please select at least ' . $subjectCountDetails->comp_lang_subject_min_requried_count . ' compulsory language subject.';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }

                if ($subjectCountDetails->comp_lang_subject_max_requried_count != 'Null' && !empty($filledComLanSubjects) && !empty($subjectCountDetails->comp_lang_subject_max_requried_count) && $filledComLanSubjects > $subjectCountDetails->comp_lang_subject_max_requried_count) {
                    $fld = 'subject_id';
                    $errMsg = 'Please select maximum ' . $subjectCountDetails->comp_lang_subject_max_requried_count . ' compulsory language subject.';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }
            }

            if ($isValid) {
                // additional min  and com max

                if ($subjectCountDetails->addi_lang_subject_min_requried_count != 'Null' && !empty($filledAddiLangSubjects) && !empty($subjectCountDetails->addi_lang_subject_min_requried_count) && $subjectCountDetails->addi_lang_subject_min_requried_count < $filledAddiLangSubjects) {
                    $fld = 'subject_id';
                    $errMsg = 'Please select at least ' . $subjectCountDetails->addi_lang_subject_min_requried_count . ' additional language subject.';
                    $errors[$fld][] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }

                if ($subjectCountDetails->addi_lang_subject_max_requried_count != 'Null' && !empty($filledAddiLangSubjects) && !empty($subjectCountDetails->addi_lang_subject_max_requried_count) && $filledAddiLangSubjects > $subjectCountDetails->addi_lang_subject_max_requried_count) {
                    $fld = 'subject_id';
                    $errMsg = 'Please select maximum ' . $subjectCountDetails->addi_lang_subject_max_requried_count . ' additional language subject.';

                    $errors[$fld][$fld] = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }
            }
        }
        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;

        return $response;
    }

    public function checkIsLanguageSubject($subject_id = null)
    {
        $condtions = null;
        $result = false;
        $condtions['deleted'] = 0;
        $condtions['id'] = $subject_id;
        $condtions['subject_type'] = "A";

        $mainTable = "subjects";
        $cacheName = "IsLangSubject_U_" . $subject_id;

        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->count();

                if ($result > 0) {
                    $result = true;
                } else {
                    $result = false;
                }
                return $result;
            });
        }
        return $result;
    }

    function checkIsUniqueSubject($subjects = null)
    {
        $status = false;
        foreach (@$subjects as $k => $v) {
            if ($v == 'Select Any') {
                unset($subjects[$k]);
            }
        }

        if (count($subjects) > 0) {
            $subjects = array_values(array_filter($subjects));
            if (count($subjects) == count(array_unique($subjects))) {
                $status = true;
            }
        }
        return $status;
    }

    function getDuplicateSubject($subjects = null)
    {
        $subjectsList = $this->subjectList();
        $finalSubjects = array();
        $duplicateSujects = array();

        $subjects = array_values(array_filter($subjects));
        $subjects = array_unique(array_diff_assoc($subjects, array_unique($subjects)));
        foreach ($subjects as $k => $sub) {
            $finalSubjects[] = @$subjectsList[$sub];
        }
        $finalSubjects = implode(",", $finalSubjects);
        return $finalSubjects;
    }

    function getPassedSubject($student_id = null)
    {
        // $examSubjectPassedList = ExamSubject::select('subject_id')->where("student_id",$student_id)->where("final_result",'P')->get()->toArray();//In-correct

        $examSubjectPassedList = ExamSubject::where('student_id', $student_id)->whereNull('deleted_at')->distinct('subject_id')->where("final_result", 'P')->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'DESC')->select('subject_id')->get()->toArray();


        $examSubjectPassedArr = array();
        if (isset($examSubjectPassedList) && !empty($examSubjectPassedList)) {
            foreach ($examSubjectPassedList as $examSubjectPassed) {
                $examSubjectPassedArr[] = $examSubjectPassed['subject_id'];
            }
        }
        return $examSubjectPassedArr;
    }

    public function _getStudentDetailedFee($student_id = null)
    {
        $studentdata = Student::with('application')->where('id', $student_id)->first();
        $condtions = null;
        $result = array();
        $mainTable = "fee_structures";

        $fld = "adm_type";
        $$fld = $studentdata->$fld;
        $fld = "course";
        $$fld = $studentdata->$fld;
        $fld = "gender_id";
        $$fld = $studentdata->$fld;
        $fld = "category_a";
        $$fld = '';
        $fld = "is_wdp_wpp";
        $$fld = 0;
        $fld = "disability";
        $$fld = 0;
        if ($studentdata->application->disability != 10) {
            $fld = "disability";
            $$fld = $studentdata->application->disability_percentage;
        }
        $cacheName = "StudentDetailedFeeMaster_" . $adm_type . "_ " . $course . "_  " . $gender_id . "_ " . $disability . "_ " . $is_wdp_wpp;
        if (isset($studentdata->application) && isset($studentdata->application->category_a) && $studentdata->application->category_a == 7) {
            $fld = "is_jail_inmates";
            $$fld = 1;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            if ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
                if ($studentdata->are_you_from_rajasthan == 2) {
                    $fld = "is_out_of_rajasthan";
                    $$fld = 2;
                    $fld = "is_out_of_rajasthan";
                    if (!empty($fld)) {
                        $condtions[$fld] = $$fld;
                    }
                }
            }
        } elseif ($studentdata->adm_type == 5 || $studentdata->adm_type == 2 || $studentdata->adm_type == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }

        } elseif (@$studentdata->adm_type == 1 && @$studentdata->application->category_a == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 6) {
            if ($studentdata->application->category_a == 6) {
                $fld = "is_wdp_wpp";
                $$fld = 1;
            } else {
                $fld = "is_wdp_wpp";
                $$fld = 0;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 6) {
            if ($studentdata->application->category_a == 6) {
                $fld = "is_wdp_wpp";
                $$fld = 1;
            } else {
                $fld = "is_wdp_wpp";
                $$fld = 0;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && $studentdata->application->disability == 10) {
            /*if($studentdata->application->disability == 10 ){
			$fld = "disability";  $$fld = 0;
		}else{
			$fld = "disability";  $$fld = 1;
		}*/
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && @$studentdata->application->disability == 10) {
            /*if($studentdata->application->disability == 10 ){
			$fld = "disability";  $$fld = 0;
		}else{
			$fld = "disability";  $$fld = 1;
		}*/
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 || $studentdata->adm_type == 4) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";  //$$fld = 1;
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            $$fld = 0;
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        }


        if ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
            $result = DB::table('fee_structures')->where($condtions)->first();
        } else {
            if ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 2) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 2) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 3) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 3) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else {

                $result = DB::table('fee_structures')->where($condtions)->first();
            }
        }
        //dd($result);
        return $result;
    }

    public function _getFeeDetailsForDispaly($studentDetailedFees = null, $student_id = null)
    {
        $result = array();
        $baseFees = array();
        $studentdata = Student::with('application')->where('id', $student_id)->first();
        $baseName = null;
        if (@$studentdata->book_learning_type_id && $studentdata->book_learning_type_id == 1) {
            $baseName = "econtent_";
        }
        if ($baseName == null) {
            $baseFees = ((@$studentDetailedFees->pay_registration_fees) +
                (@$studentDetailedFees->pay_forward_fees) +
                (@$studentDetailedFees->pay_online_services_fees));

            if ($studentdata->adm_type == 4) {
                $tocSubjects = 0;
            } else {
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
            }

            $studentpracticaltypesubject = ExamSubject::join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
                ->where('subjects.practical_type', 1)->where('exam_subjects.student_id', $student_id)
                ->count();
            if ($studentdata->adm_type == 4) {
                $addSubjectCount = 0;
            } else {
                $addSubjectCount = ExamSubject::where("student_id", $student_id)->where("is_additional", 1)->count();
            }

            //addtional subject from admission subject table
            $tocSubjectCount = $tocSubjects;
            $readmExamFeeCount = 0;
            $variableFees = 0;
            if ($studentdata->adm_type == 3) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            } else if ($studentdata->adm_type == 2) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
                $partadmintionstudent1 = $partadmintionstudent;
            } else if ($studentdata->adm_type == 2 && $tocSubjects == 0) {
                $partadmintionstudent1 = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            }
            $practicalSubjectCount = $studentpracticaltypesubject;
            $finalFees = 0;
            $fld = 'pay_add_sub_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$addSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$addSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = $variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'pay_toc_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$tocSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$tocSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'pay_practical_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$practicalSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$practicalSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            if ($studentdata->adm_type == 5 || $studentdata->adm_type == 1) {
                $fld = 'pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 3) {
                $fld = 'pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 2) {
                $fld = 'pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent1);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent1);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            }

            $studentDetails = Student::where('id', $student_id)->first();
            if (@$studentdata->application->category_a == 7) {
                $lateFees = 0;
            } else {
                $lateFees = $this->_getLateFeeAmount(@$studentDetails->stream, @$studentDetails->gender_id, $student_id);
            }
            //22092023
            /* set old late fee in academic/devloper login start */
            $studentLateFeeData = StudentFee::where('student_id', $student_id)->first(['late_fee']);
            $role_id = Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            $developer_admin = Config::get("global.developer_admin");
            if ($role_id == $super_admin_id || $role_id == $developer_admin) {
                $lateFees = @$studentLateFeeData->late_fee;
            }
            /* set old late fee in academic/devloper login end */

            $finalFees = (float)@$baseFees + (float)@$variableFees + (float)@$lateFees;

            $result['registration_fees'] = @$studentDetailedFees->pay_registration_fees;
            $result['forward_fees'] = @$studentDetailedFees->pay_forward_fees;
            $result['online_services_fees'] = @$studentDetailedFees->pay_online_services_fees;
            $result['add_sub_fees'] = @$pay_add_sub_fees;
            $result['toc_fees'] = @$pay_toc_fees;
            $result['practical_fees'] = @$pay_practical_fees;
            $result['readm_exam_fees'] = @$pay_exam_fees;
            $result['baseFee'] = @$baseFees;
            $result['variableFees'] = @$variableFees;
            $result['late_fees'] = @$lateFees;
            $result['final_fees'] = @$finalFees;

        } else {
            $baseFees = ((@$studentDetailedFees->econtent_pay_registration_fees) +
                (@$studentDetailedFees->econtent_pay_forward_fees) +
                (@$studentDetailedFees->econtent_pay_online_services_fees));


            if ($studentdata->adm_type == 4) {
                $tocSubjects = 0;
            } else {
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
            }
            $studentpracticaltypesubject = ExamSubject::join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
                ->where('subjects.practical_type', 1)->where('exam_subjects.student_id', $student_id)
                ->count();
            if ($studentdata->adm_type == 4) {
                $addSubjectCount = 0;
            } else {
                $addSubjectCount = ExamSubject::where("student_id", $student_id)->where("is_additional", 1)->count();
            }

            //addtional subject from admission subject table
            $tocSubjectCount = $tocSubjects;
            $readmExamFeeCount = 0;
            $variableFees = 0;
            if ($studentdata->adm_type == 3) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            } else if ($studentdata->adm_type == 2) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
                $partadmintionstudent1 = $partadmintionstudent;
            } else if ($studentdata->adm_type == 2 && $tocSubjects == 0) {
                $partadmintionstudent1 = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            }

            $practicalSubjectCount = $studentpracticaltypesubject;
            $finalFees = 0;
            $fld = 'econtent_pay_add_sub_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$addSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$addSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = $variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'econtent_pay_toc_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$tocSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$tocSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'econtent_pay_practical_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$practicalSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$practicalSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            if ($studentdata->adm_type == 5 || $studentdata->adm_type == 1) {
                $fld = 'econtent_pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 3) {
                $fld = 'econtent_pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 2) {
                $fld = 'econtent_pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent1);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent1);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            }

            $studentDetails = Student::where('id', $student_id)->first();
            if (@$studentdata->application->category_a == 7) {
                $lateFees = 0;
            } else {
                $lateFees = $this->_getLateFeeAmount(@$studentDetails->stream, @$studentDetails->gender_id, $student_id);
            }
            //22092023
            /* set old late fee in academic/devloper login start */
            $studentLateFeeData = StudentFee::where('student_id', $student_id)->first(['late_fee']);
            $role_id = Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            $developer_admin = Config::get("global.developer_admin");
            if ($role_id == $super_admin_id || $role_id == $developer_admin) {
                $lateFees = @$studentLateFeeData->late_fee;
            }
            /* set old late fee in academic/devloper login end */

            $finalFees = (float)@$baseFees + (float)@$variableFees + (float)@$lateFees;

            $result['registration_fees'] = @$studentDetailedFees->econtent_pay_registration_fees;
            $result['forward_fees'] = @$studentDetailedFees->econtent_pay_forward_fees;
            $result['online_services_fees'] = @$studentDetailedFees->econtent_pay_online_services_fees;
            $result['add_sub_fees'] = @$econtent_pay_add_sub_fees;
            $result['toc_fees'] = @$econtent_pay_toc_fees;
            $result['practical_fees'] = @$econtent_pay_practical_fees;
            $result['readm_exam_fees'] = @$econtent_pay_exam_fees;
            $result['baseFee'] = @$baseFees;
            $result['variableFees'] = @$variableFees;
            $result['late_fees'] = @$lateFees;
            $result['final_fees'] = @$finalFees;
        }

        if ($result['late_fees'] == null) {
            $result['late_fees'] = 0;
        }

        /* Rohit Disadvante start */
        $freeFees = array(4, 5, 6, 7);
        if (

            in_array($studentdata->application->disadvantage_group, $freeFees)
            ||

            (!in_array($studentdata->application->disability, array(10)) && $studentdata->application->disability_percentage == 1)
            ||
            in_array($studentdata->gender_id, array(3))
        ) {

            $finalFees = (float)@$lateFees;
            $result['registration_fees'] = 0;
            $result['forward_fees'] = 0;
            $result['online_services_fees'] = 0;
            $result['add_sub_fees'] = 0;
            $result['toc_fees'] = 0;
            $result['practical_fees'] = 0;
            $result['readm_exam_fees'] = 0;
            $result['baseFee'] = 0;
            $result['variableFees'] = 0;
            $result['late_fees'] = @$lateFees;
            $result['final_fees'] = @$finalFees;
            if ($result['late_fees'] == null) {
                $result['late_fees'] = 0;
            }
        }
        /* Rohit Disadvante end */

        return $result;

    }

    public function _getLateFeeAmount($stream = null, $gender_id = null, $student_id = null)
    {
        $lateFeeExtraMarginDays = 0;
        if (!empty($student_id) && $student_id > 0) {
            $studentsDetails = Student::where('students.id', '=', $student_id)
                ->join('applications', 'applications.student_id', '=', 'students.id')
                ->first();

            if (@$studentsDetails->locksumbitted && @$studentsDetails->locksubmitted_date) {
                $studentLocked = $studentsDetails->locksubmitted_date;
                if ($studentsDetails->fee_paid_amount == null) {
                    $masterMarginDays = ExamLateFeeDate::where('stream', $stream)
                        ->where('gender_id', $gender_id)
                        ->where('from_date', '<=', $studentLocked)
                        ->where('to_date', '>=', $studentLocked)
                        ->where('is_supplementary', '=', 0)
                        ->first();
                    if (@$masterMarginDays->latefee_extra_days) {
                        $lateFeeExtraMarginDays = $masterMarginDays->latefee_extra_days;
                    }
                }
            }
        }

        $currentDate = date('Y-m-d');
        $toDate = null;
        $afterExtraDaysDate = null;
        if (@$masterMarginDays->to_date) {
            $toDate = date("Y-m-d", strtotime($masterMarginDays->to_date));
        }
        if ($lateFeeExtraMarginDays > 0) {
            if (@$toDate) {
                $toDate = strtotime($toDate);
                $toDate = strtotime("+" . $lateFeeExtraMarginDays . " day", $toDate);
                $afterExtraDaysDate = date('Y-m-d', $toDate);
            }
        }

        if ($currentDate <= $afterExtraDaysDate) {
            return @$masterMarginDays->late_fee;
        }
        // echo "TTT";dd($masterMarginDays);

        $master = ExamLateFeeDate::where('stream', $stream)
            ->where('gender_id', $gender_id)
            ->where('from_date', '<=', Carbon::now())
            ->where('to_date', '>=', Carbon::now())
            ->where('is_supplementary', '=', 0)
            ->first();

        // @dd($master);

        /* For special ai center allow for zero(0) late fee as per letter start */
        /*
			$aiCodeAllowFeeAsPerLetter = array("28010", "09001","9001");
			if(@$studentsDetails->ai_code && in_array($studentsDetails->ai_code, $aiCodeAllowFeeAsPerLetter)){
				if(@$studentsDetails->locksumbitted  && @$studentsDetails->locksubmitted_date){
					$studentLocked = $studentsDetails->locksubmitted_date;
					if($studentLocked <= "2022-07-31 24:00:00"){
						return 0;
					}
				}
			}
			*/
        /* For special ai center allow for zero(0) late fee as per letter end */
        if (@$studentsDetails->is_dgs && $studentsDetails->is_dgs == 1) {
        } else {
            return 500;
        }
        if (!@$master->late_fee) {
            return 0;
        }
        return @$master->late_fee;
    }

    public function old_getStudentDetailedFee($student_id = null)
    {
        $studentdata = Student::with('application')->where('id', $student_id)->first();
        $condtions = null;
        $result = array();
        $mainTable = "fee_structures";

        $fld = "adm_type";
        $$fld = $studentdata->$fld;
        $fld = "course";
        $$fld = $studentdata->$fld;
        $fld = "gender_id";
        $$fld = $studentdata->$fld;
        $fld = "category_a";
        $$fld = '';
        $fld = "is_wdp_wpp";
        $$fld = 0;
        $fld = "disability";
        $$fld = 0;


        /* Rohit Dis Adv Group Start */
        $freeFees = array(4, 5, 6, 7);
        if (isset($studentdata->application) && isset($studentdata->application->disadvantage_group) && in_array($studentdata->application->disadvantage_group, $freeFees)) {
            $fld = "is_disadvantage_group";
            $condtions[$fld] = 1;
        }
        /* Rohit Dis Adv Group End */

        $cacheName = "StudentDetailedFeeMaster_" . $adm_type . "_ " . $course . "_  " . $gender_id . "_ " . $disability . "_ " . $is_wdp_wpp;
        if (isset($studentdata->application) && isset($studentdata->application->category_a) && $studentdata->application->category_a == 7) {
            $fld = "is_jail_inmates";
            $$fld = 1;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }

            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            if ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
                if ($studentdata->are_you_from_rajasthan == 2) {
                    $fld = "is_out_of_rajasthan";
                    $$fld = 2;
                    $fld = "is_out_of_rajasthan";
                    if (!empty($fld)) {
                        $condtions[$fld] = $$fld;
                    }

                    /* 0107 */
                    if (@$studentdata->application->disability) {
                        if ($studentdata->application->disability == 10) {
                            $fld = "disability";
                            $$fld = 0;
                        } else {
                            $fld = "disability";
                            $$fld = 1;
                            $fld = "disability";
                            if (!empty($fld)) {
                                $condtions[$fld] = $$fld;
                            }
                            unset($condtions['is_out_of_rajasthan']);
                        }
                    }
                    /* 0107 */
                    $freeFees = array(4, 5, 6, 7);
                    if (isset($studentdata->application) && isset($studentdata->application->disadvantage_group) && in_array($studentdata->application->disadvantage_group, $freeFees)) {
                        $fld = "is_disadvantage_group";
                        $condtions[$fld] = 1;
                        unset($condtions['is_out_of_rajasthan']);
                    }
                } else {
                }
            }
        } elseif ($studentdata->adm_type == 5 || $studentdata->adm_type == 2 || $studentdata->adm_type == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }

        } elseif (@$studentdata->adm_type == 1 && @$studentdata->application->category_a == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 6) {
            if ($studentdata->application->category_a == 6) {
                $fld = "is_wdp_wpp";
                $$fld = 1;
            } else {
                $fld = "is_wdp_wpp";
                $$fld = 0;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 6) {
            if ($studentdata->application->category_a == 6) {
                $fld = "is_wdp_wpp";
                $$fld = 1;
            } else {
                $fld = "is_wdp_wpp";
                $$fld = 0;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && $studentdata->application->disability == 10) {
            if ($studentdata->application->disability == 10) {
                $fld = "disability";
                $$fld = 0;
            } else {
                $fld = "disability";
                $$fld = 1;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && @$studentdata->application->disability == 10) {
            if ($studentdata->application->disability == 10) {
                $fld = "disability";
                $$fld = 0;
            } else {
                $fld = "disability";
                $$fld = 1;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 || $studentdata->adm_type == 4) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            $$fld = 1;
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            $$fld = 0;
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        }


        if (@$condtions['gender_id'] == 3 && (@$condtions['is_disadvantage_group'] == 1 ||
                @$condtions['disability'] == 1 ||
                @$condtions['is_wdp_wpp'] == 1)) {

            if (isset($condtions['is_disadvantage_group'])) {
                unset($condtions['is_disadvantage_group']);
            }
            if (isset($condtions['disability'])) {
                unset($condtions['disability']);
            }
            if (isset($condtions['is_wdp_wpp'])) {
                unset($condtions['is_wdp_wpp']);
            }
        }

        if (((@$studentdata->are_you_from_rajasthan == 2 || @$studentdata->are_you_from_rajasthan == 1)
                && (@$studentdata->gender_id == 2 || @$studentdata->gender_id == 1)
                ||
                (isset($studentdata->application) && isset($studentdata->application->category_a)
                    && in_array($studentdata->application->category_a, array(2, 3))))

            && (@$condtions['is_disadvantage_group'] == 1 ||
                @$condtions['disability'] == 1 || @$condtions['is_wdp_wpp'] == 1)
        ) {
            if (@$condtions['is_disadvantage_group'] == 1) {
                unset($condtions['disability']);
                unset($condtions['is_wdp_wpp']);
            }
            if (@$condtions['disability'] == 1) {
                unset($condtions['is_disadvantage_group']);
                unset($condtions['is_wdp_wpp']);
            }
            if (@$condtions['is_wdp_wpp'] == 1) {
                unset($condtions['disability']);
                unset($condtions['is_disadvantage_group']);
            }
            if ((isset($studentdata->application) && isset($studentdata->application->category_a)
                && in_array($studentdata->application->category_a, array(2, 3)))) {
                // unset($studentdata->application->category_a);
            }
        }
        // dd($studentdata->are_you_from_rajasthan);
        // echo "ddddddd";dd($studentdata->are_you_from_rajasthan);

        if ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
            $result = DB::table('fee_structures')->where($condtions)->first();
        } else {
            if ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 2) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 2) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 1 && @$studentdata->application->category_a == 3) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 4 && @$studentdata->application->category_a == 3) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else {
                $result = DB::table('fee_structures')->where($condtions)->first();
            }
        }

        return $result;
    }

    public function old_getFeeDetailsForDispaly($studentDetailedFees = null, $student_id = null)
    {

        $result = array();
        $lateFees = 0;
        $studentdata = Student::with('application')->where('id', $student_id)->first();
        $baseName = null;
        if (@$studentdata->book_learning_type_id && $studentdata->book_learning_type_id == 1) {
            $baseName = "econtent_";
        }

        // dd($studentDetailedFees);

        if ($baseName == null) {
            $baseFees = ((@$studentDetailedFees->pay_registration_fees) +
                (@$studentDetailedFees->pay_forward_fees) +
                (@$studentDetailedFees->pay_online_services_fees));

            if ($studentdata->adm_type == 4) {
                $tocSubjects = 0;
            } else {
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
            }

            $studentpracticaltypesubject = ExamSubject::join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
                ->where('subjects.practical_type', 1)->where('exam_subjects.student_id', $student_id)
                ->count();
            if ($studentdata->adm_type == 4) {
                $addSubjectCount = 0;
            } else {
                $addSubjectCount = ExamSubject::where("student_id", $student_id)->where("is_additional", 1)->count();
            }

            //addtional subject from admission subject table
            $tocSubjectCount = $tocSubjects;
            $readmExamFeeCount = 0;
            $variableFees = 0;
            if ($studentdata->adm_type == 3) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            } else if ($studentdata->adm_type == 2) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
                $partadmintionstudent1 = $partadmintionstudent;
            } else if ($studentdata->adm_type == 2 && $tocSubjects == 0) {
                $partadmintionstudent1 = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            }
            $practicalSubjectCount = $studentpracticaltypesubject;
            $finalFees = 0;
            $fld = 'pay_add_sub_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$addSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$addSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = $variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'pay_toc_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$tocSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$tocSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'pay_practical_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$practicalSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$practicalSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            if ($studentdata->adm_type == 5 || $studentdata->adm_type == 1) {
                $fld = 'pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 3) {
                $fld = 'pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 2) {
                $fld = 'pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent1);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent1);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            }

            $studentDetails = Student::where('id', $student_id)->first();

            if (@$studentdata->application->category_a == 7) {
                $lateFees = 0;
            } else {
                $lateFees = $this->_getLateFeeAmount(@$studentDetails->stream, @$studentDetails->gender_id, $student_id);
            }
            //22092023
            /* set old late fee in academic/devloper login start */
            $studentLateFeeData = StudentFee::where('student_id', $student_id)->first(['late_fee']);
            $role_id = Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            $developer_admin = Config::get("global.developer_admin");
            if ($role_id == $super_admin_id || $role_id == $developer_admin) {
                $lateFees = @$studentLateFeeData->late_fee;
            }
            /* set old late fee in academic/devloper login end */

            $finalFees = (float)@$baseFees + (float)@$variableFees + (float)@$lateFees;

            $result['registration_fees'] = @$studentDetailedFees->pay_registration_fees;
            $result['forward_fees'] = @$studentDetailedFees->pay_forward_fees;
            $result['online_services_fees'] = @$studentDetailedFees->pay_online_services_fees;
            $result['add_sub_fees'] = @$pay_add_sub_fees;
            $result['toc_fees'] = @$pay_toc_fees;
            $result['practical_fees'] = @$pay_practical_fees;
            $result['readm_exam_fees'] = @$pay_exam_fees;
            $result['baseFee'] = @$baseFees;
            $result['variableFees'] = @$variableFees;
            $result['late_fees'] = @$lateFees;
            $result['final_fees'] = @$finalFees;

        } else {
            $baseFees = ((@$studentDetailedFees->econtent_pay_registration_fees) +
                (@$studentDetailedFees->econtent_pay_forward_fees) +
                (@$studentDetailedFees->econtent_pay_online_services_fees));


            if ($studentdata->adm_type == 4) {
                $tocSubjects = 0;
            } else {
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
            }
            $studentpracticaltypesubject = ExamSubject::join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
                ->where('subjects.practical_type', 1)->where('exam_subjects.student_id', $student_id)
                ->count();
            if ($studentdata->adm_type == 4) {
                $addSubjectCount = 0;
            } else {
                $addSubjectCount = ExamSubject::where("student_id", $student_id)->where("is_additional", 1)->count();
            }

            //addtional subject from admission subject table
            $tocSubjectCount = $tocSubjects;
            $readmExamFeeCount = 0;
            $variableFees = 0;
            if ($studentdata->adm_type == 3) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            } else if ($studentdata->adm_type == 2) {
                $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
                $tocSubjects = TocMark::where("student_id", $student_id)->count();
                $partadmintionstudent1 = $partadmintionstudent;
            } else if ($studentdata->adm_type == 2 && $tocSubjects == 0) {
                $partadmintionstudent1 = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            }

            $practicalSubjectCount = $studentpracticaltypesubject;
            $finalFees = 0;
            $fld = 'econtent_pay_add_sub_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$addSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$addSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = $variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'econtent_pay_toc_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$tocSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$tocSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            $fld = 'econtent_pay_practical_fees';
            if (@$studentDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld * @$practicalSubjectCount);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld * @$practicalSubjectCount);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }

            if ($studentdata->adm_type == 5 || $studentdata->adm_type == 1) {
                $fld = 'econtent_pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 3) {
                $fld = 'econtent_pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            } else if ($studentdata->adm_type == 2) {
                $fld = 'econtent_pay_exam_fees';
                if (@$studentDetailedFees->$fld) {
                    $$fld = (@$studentDetailedFees->$fld * $partadmintionstudent1);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld * $partadmintionstudent1);
                } else {
                    $$fld = (@$studentDetailedFees->$fld);
                    $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
                }
            }

            $studentDetails = Student::where('id', $student_id)->first();
            if (@$studentdata->application->category_a == 7) {
                $lateFees = 0;
            } else {
                $lateFees = $this->_getLateFeeAmount(@$studentDetails->stream, @$studentDetails->gender_id, $student_id);
            }
            //22092023
            /* set old late fee in academic/devloper login start */
            $studentLateFeeData = StudentFee::where('student_id', $student_id)->first(['late_fee']);
            $role_id = Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            $developer_admin = Config::get("global.developer_admin");
            if ($role_id == $super_admin_id || $role_id == $developer_admin) {
                $lateFees = @$studentLateFeeData->late_fee;
            }
            /* set old late fee in academic/devloper login end */

            $finalFees = (float)@$baseFees + (float)@$variableFees + (float)@$lateFees;

            $result['registration_fees'] = @$studentDetailedFees->econtent_pay_registration_fees;
            $result['forward_fees'] = @$studentDetailedFees->econtent_pay_forward_fees;
            $result['online_services_fees'] = @$studentDetailedFees->econtent_pay_online_services_fees;
            $result['add_sub_fees'] = @$econtent_pay_add_sub_fees;
            $result['toc_fees'] = @$econtent_pay_toc_fees;
            $result['practical_fees'] = @$econtent_pay_practical_fees;
            $result['readm_exam_fees'] = @$econtent_pay_exam_fees;
            $result['baseFee'] = @$baseFees;
            $result['variableFees'] = @$variableFees;
            $result['late_fees'] = @$lateFees;
            $result['final_fees'] = @$finalFees;

        }

        if ($result['late_fees'] == null) {
            $result['late_fees'] = 0;
        }

        /* Rohit Disadvante start */
        $freeFees = array(4, 5, 6, 7);
        if (
            in_array($studentdata->application->disadvantage_group, $freeFees)
            ||

            !in_array($studentdata->application->disability, array(10))
            ||
            in_array($studentdata->gender_id, array(3))
        ) {
            $finalFees = (float)@$lateFees;
            $result['registration_fees'] = 0;
            $result['forward_fees'] = 0;
            $result['online_services_fees'] = 0;
            $result['add_sub_fees'] = 0;
            $result['toc_fees'] = 0;
            $result['practical_fees'] = 0;
            $result['readm_exam_fees'] = 0;
            $result['baseFee'] = 0;
            $result['variableFees'] = 0;
            $result['late_fees'] = @$lateFees;
            $result['final_fees'] = @$finalFees;
            if ($result['late_fees'] == null) {
                $result['late_fees'] = 0;
            }
        }
        /* Rohit Disadvante end */
        return $result;

    }

    public function _getAdmissionSubjects($student_id = null, $adm_type = null)
    {
        $admissionSubjects = AdmissionSubject::where("student_id", $student_id)->pluck("is_additional", "subject_id")->toArray();


        $examSubjectsCount = ExamSubject::where("student_id", $student_id)->count();


        $compSubjects = array();
        $addiSubjects = array();

        foreach ($admissionSubjects as $subject_id => $is_additional) {
            if ($is_additional == 0) {
                $compSubjects[] = $subject_id;
            }
            if ($is_additional == 1) {
                $addiSubjects[] = $subject_id;
            }
        }

        $tocSubjects = array();
        if (@$adm_type != 4) {
            $tocSubjects = TocMark::where("student_id", $student_id)->pluck("subject_id")->toArray();
        }

        $examSubjects["comExamSubjects"] = array_diff($compSubjects, $tocSubjects);
        $examSubjects["addiExamSubjects"] = array_diff($addiSubjects, $tocSubjects);
        $examSubjects["examSubjectsCount"] = $examSubjectsCount;

        return $examSubjects;
    }

    public function _getExamSubjectsList($student_id = null)
    {
        $condtions = null;
        $result = array();
        $mainTable = "exam_subjects";
        // $cacheName = "exam_subjects".$student_id;
        if (!empty($student_id)) {
            $condtions = ['student_id' => $student_id];
        }
        $result = DB::table($mainTable)->where($condtions)
            ->groupBy('exam_subjects.subject_id')
            ->orderBy('exam_subjects.subject_id')
            ->whereNull('exam_subjects.deleted_at')
            // ->pluck(DB::raw('(case when sessional_marks = "999" then "Absent" else sessional_marks end )'),'subject_id');
            ->pluck('sessional_marks', 'subject_id');
        return $result;
    }

    public function _getSessionalExamSubjectsList($student_id = null)
    {
        $condtions = null;
        $result = array();
        $mainTable = "sessional_exam_subjects";
        // $cacheName = "sessional_exam_subjects".$student_id;
        if (!empty($student_id)) {
            $condtions = ['student_id' => $student_id];
        }
        $result = DB::table($mainTable)->where($condtions)
            ->groupBy('sessional_exam_subjects.subject_id')
            ->orderBy('sessional_exam_subjects.subject_id')
            ->whereNull('sessional_exam_subjects.deleted_at')
            // ->pluck(DB::raw('(case when sessional_marks = "999" then "Absent" else sessional_marks end )'),'subject_id');
            ->pluck('sessional_marks', 'subject_id');
        return $result;
    }

    public function showPassFailFieldToc($adm_type = null, $stream = null, $board_id = null)
    {
        $response = 0;
        $query = DB::table('toc_validations');
        $query->where('adm_type', '=', $adm_type);
        // if(in_array($board_id,array(81,56)) && $adm_type!=3){
        if (in_array($board_id, array(81))) {
            $query->where('board_id', '=', $board_id);
        } else {
            $query->where('board_id', '=', '');
        }
        $result = $query->first();
        //@dd($result);

        $fldP = "is_year_of_passing_required_stream" . $stream;
        $fldF = "is_year_of_failing_required_stream" . $stream;
        if (@$result->$fldP == 1) {
            $response = 1; //1 = pass field
        }
        if (@$result->$fldF == 1) {
            $response = 0;  //0 = fail field
        }
        return $response;
    }

    public function _getJanAadharDetails($janAadharNumber = null)
    {
        $url = 'https://api.sewadwaar.rajasthan.gov.in/app/live/Janaadhaar/Prod/Service/Info/Fetch?client_id=7c3853c4-34d6-42d2-a97c-dfe0ea1df951';
        $curl = curl_init();
        if (strpos($janAadharNumber, '-') !== false) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '<root>
				<Info>
					<janaadhaarId></janaadhaarId>
					<enrId>' . $janAadharNumber . '</enrId>
					<aadharId></aadharId>
					<scheme>HEADM</scheme>
					<infoFlg>PFE</infoFlg>
					<authMode>AOTP</authMode>
					<dateTime>' . date("d-m-Y h:m:s") . '</dateTime>
					</Info>
				</root>',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/xml'
                ),
            ));
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '<root>
				<Info>
					<janaadhaarId>' . $janAadharNumber . '</janaadhaarId>
					<enrId></enrId>
					<aadharId></aadharId>
					<scheme>HEADM</scheme>
					
					<infoFlg>PFE</infoFlg>
					<authMode>AOTP</authMode>
					<dateTime>' . date("d-m-Y h:m:s") . '</dateTime>
					</Info>
				</root>',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/xml'
                ),
            ));
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $result = json_decode($json, true);
        curl_close($curl);
        return $result;
    }

    public function _settingJanAadharDetails($response = null, $member_number = null, $total_member = null, $jan_id = null)
    {
        $memberDetails = array();
        if ($response['cmsg'] != 110) {
            $counter = 0;
            $memberAddress = @$response['family']['familydetail'];
            $members = $response['personalInfo']['member'];
            // echo $member_number;
            // echo "<br>";
            // echo "<pre>";
            // print_r($jan_id);
            //aadhar

            if ($total_member == 1) {
                $v = $members;

                $addressEng = $ward = $isMinority = $village = $pin = $caste = $isRural =
                $districtName = $category = $block_city = null;
                if (@$memberAddress) {
                    $addressEng = @$memberAddress['addressEng'];
                    $ward = @$memberAddress['ward'];
                    $districtName = @$memberAddress['districtName'];
                    $block_city = @$memberAddress['block_city'];
                    $isRural = @$memberAddress['isRural'];
                    $village = @$memberAddress['village'];
                    $pin = @$memberAddress['pin'];

                    $isMinority = @$memberAddress['isMinority'];
                    $caste = @$memberAddress['caste'];
                    $category = @$memberAddress['category'];
                }
                $fld = "addressEng";
                $memberDetails["Address"][$fld] = $$fld;
                $fld = "ward";
                $memberDetails["Address"][$fld] = $$fld;
                $fld = "districtName";
                $memberDetails["Address"][$fld] = $$fld;
                $fld = "block_city";
                $memberDetails["Address"][$fld] = $$fld;
                $fld = "isRural";
                $memberDetails["Address"][$fld] = "";//$$fld;
                $fld = "village";
                $memberDetails["Address"][$fld] = $$fld;
                $fld = "pin";
                $memberDetails["Address"][$fld] = $$fld;

                $fld = "isMinority";
                $memberDetails["Application"][$fld] = $$fld;
                $fld = "caste";
                $memberDetails["Application"][$fld] = $$fld;
                $fld = "category";
                $memberDetails["Application"][$fld] = $$fld;

                $fld = "jan_mid";
                $memberDetails["Application"][$fld] = $v[$fld];
                $fld = "jan_mid";
                $memberDetails["Application"][$fld] = $v[$fld];
                $fld = "hof_jan_m_id";
                $memberDetails["Application"][$fld] = $v[$fld];
                $fld = "nameEng";
                $memberDetails["Student"]['name'] = $v[$fld];
                $memberDetails["Application"]['student_name'] = $v[$fld];


                $fld = "fnameEng";
                $memberDetails["Student"]['father_name'] = $v[$fld];
                $fld = "mnameEng";
                $memberDetails["Student"]['mother_name'] = $v[$fld];
                $fld = "dob";
                $memberDetails["Student"][$fld] = $v[$fld];
                $fld = "mobile";
                $memberDetails["Student"][$fld] = '';
                $fld = "janaadhaarId";
                $memberDetails["Application"]['jan_aadhar_number'] = $v[$fld];
                $fld = "aadhar";
                $memberDetails["Application"][$fld] = $v[$fld];


                $fld = "bankName";
                $memberDetails["bank_details"][$fld] = $v[$fld];
                $fld = "ifsc";
                $memberDetails["bank_details"][$fld] = $v[$fld];
                $fld = "bankBranch";
                $memberDetails["bank_details"][$fld] = $v[$fld];
                $fld = "acc";
                $memberDetails["bank_details"][$fld] = $v[$fld];
                $fld = "gender";
                if ($v[$fld] == 'Male') {
                    $memberDetails["Student"]['gender_id'] = 1;
                }
                if ($v[$fld] == 'Female') {
                    $memberDetails["Student"]['gender_id'] = 2;
                }
                $fld = "age";
                $memberDetails["Application"][$fld] = $v[$fld];

            } else {

                if (isset($members) && !empty($members)) {
                    foreach ($members as $k => $v) {
                        if (@$v['jan_mid'] == "0") {
                            $v['jan_mid'] = $v['hof_jan_m_id'];
                        }
                        if (@$v['jan_mid'] != $jan_id) {
                            continue;
                        }

                        $addressEng = $ward = $isMinority = $village = $pin = $caste = $isRural =
                        $districtName = $category = $block_city = null;
                        if (@$memberAddress) {
                            $addressEng = @$memberAddress['addressEng'];
                            $ward = @$memberAddress['ward'];
                            $districtName = @$memberAddress['districtName'];
                            $block_city = @$memberAddress['block_city'];
                            $isRural = @$memberAddress['isRural'];
                            $village = @$memberAddress['village'];
                            $pin = @$memberAddress['pin'];

                            $isMinority = @$memberAddress['isMinority'];
                            $caste = @$memberAddress['caste'];
                            $category = @$memberAddress['category'];
                        }
                        $fld = "addressEng";
                        $memberDetails["Address"][$fld] = $$fld;
                        $fld = "ward";
                        $memberDetails["Address"][$fld] = $$fld;
                        $fld = "districtName";
                        $memberDetails["Address"][$fld] = $$fld;
                        $fld = "block_city";
                        $memberDetails["Address"][$fld] = $$fld;
                        $fld = "isRural";
                        $memberDetails["Address"][$fld] = $$fld;
                        $fld = "village";
                        $memberDetails["Address"][$fld] = $$fld;
                        $fld = "pin";
                        $memberDetails["Address"][$fld] = $$fld;

                        $fld = "isMinority";
                        $memberDetails["Application"][$fld] = $$fld;
                        $fld = "caste";
                        $memberDetails["Application"][$fld] = $$fld;
                        $fld = "category";
                        $memberDetails["Application"][$fld] = $$fld;


                        $fld = "jan_mid";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        $fld = "jan_mid";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        $fld = "hof_jan_m_id";
                        if (isset($v[$fld])) {
                            $memberDetails["Application"][$fld] = $v[$fld];
                        }

                        $fld = "nameEng";
                        $memberDetails["Student"]['name'] = $v[$fld];
                        $fld = "email";
                        $memberDetails["Student"][$fld] = @$v[$fld];
                        $memberDetails["Application"]['student_name'] = $v[$fld];


                        $fld = "fnameEng";
                        $memberDetails["Student"]['father_name'] = $v[$fld];
                        $fld = "mnameEng";
                        $memberDetails["Student"]['mother_name'] = $v[$fld];
                        $fld = "dob";
                        $memberDetails["Student"][$fld] = $v[$fld];

                        $fld = "mobile";
                        $memberDetails["Student"][$fld] = "";
                        if (!empty($v[$fld])) {
                            $fld = "mobile";
                            $memberDetails["Student"][$fld] = @$v[$fld];
                        }


                        $fld = "janaadhaarId";
                        $memberDetails["Application"]['jan_aadhar_number'] = $v[$fld];
                        $fld = "aadhar";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        $fld = "bankName";
                        $memberDetails["bank_details"][$fld] = $v[$fld];
                        $fld = "ifsc";
                        $memberDetails["bank_details"][$fld] = $v[$fld];
                        $fld = "bankBranch";
                        $memberDetails["bank_details"][$fld] = $v[$fld];
                        $fld = "acc";
                        $memberDetails["bank_details"][$fld] = $v[$fld];

                        $fld = "gender";
                        if ($v[$fld] == 'Male') {
                            $memberDetails["Student"]['gender_id'] = 1;
                        }
                        if ($v[$fld] == 'Female') {
                            $memberDetails["Student"]['gender_id'] = 2;
                        }
                        $fld = "age";
                        $memberDetails["Application"][$fld] = $v[$fld];
                        break;
                    }
                }
            }
        }

        return $memberDetails;
    }

    public function tocValidations($request, $board_id = null, $adm_type = null, $toc_submit_subject = null)
    {
        $max = 0;
        $min = 0;
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';

        if (empty($request->is_toc)) {
            $fld = 'is_toc';
            $errMsg = 'Please select mandatory field "Whether you are applying for Transfer of Credit".';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }

        if ($isValid == true && empty($request->board)) {
            $fld = 'board';
            $errMsg = 'Please select mandatory board.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }

        if ($isValid == true && empty($request->year_fail) && empty($request->year_pass)) {
            $fld = 'year_fail';
            $errMsg = 'Please select mandatory year field.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }

        if ($isValid == true && empty($request->roll_no)) {
            $fld = 'roll_no';
            $errMsg = 'Please select mandatory roll no field.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }

        if ($isValid == true && $toc_submit_subject == 0) {
            $fld = 'subject_id';
            $errMsg .= 'Please select minimum 1 subject toc';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }

        if ($isValid == true && !empty($request->toc_subject)) {
            $sbuject_id_arr = array();
            foreach ($request->toc_subject as $k => $v) {
                if (!empty($v['subject_id'])) {
                    $sbuject_id_arr[] = $v['subject_id'];
                }
            }
            $isUniqueSubects = $this->checkIsUniqueSubject($sbuject_id_arr);
            if (!$isUniqueSubects) {
                $fld = 'subject_id';
                $errMsg = 'Please select unique subjects for toc.';
                $errors = $errMsg;
                $validator->getMessageBag()->add($fld, $errMsg);
                $isValid = false;
            }
        }

        if ($isValid == true && !empty($request->toc_subject)) {
            foreach ($request->toc_subject as $k => $v) {
                $isPracticalSubject = $this->checkIsPracticalSubject($v['subject_id']);

                if ($isPracticalSubject) {
                    if ($request->course == '12') {
                        if (!empty($v['subject_id']) && (empty($v['theory']) || empty($v['practical']) || empty($v['total']))) {
                            $fld = 'subject_id';
                            $errMsg .= 'Please enter theory and practical marks into selected toc subject at sr. no ' . ($k + 1);
                            $errors = $errMsg;
                            $validator->getMessageBag()->add($fld, $errMsg);
                            $isValid = false;
                        }
                    }

                } else {
                    if (!empty($v['subject_id']) && (empty($v['theory']) || empty($v['total']))) {
                        $fld = 'subject_id';
                        $errMsg .= 'Please enter theory marks into selected toc subject at sr. no ' . ($k + 1);
                        $errors = $errMsg;
                        $validator->getMessageBag()->add($fld, $errMsg);
                        $isValid = false;
                    }

                    if (!empty($v['practical'])) {
                        $fld = 'subject_id';
                        $errMsg .= 'You cant enter marks into practical section selected toc subject at sr. no ' . ($k + 1);
                        $errors = $errMsg;
                        $validator->getMessageBag()->add($fld, $errMsg);
                        $isValid = false;
                    }
                }
            }
        }


        if ($isValid == true && !empty($request->toc_subject)) {
            foreach ($request->toc_subject as $k => $v) {
                if (($v['theory'] + $v['practical']) > 100) {
                    $fld = 'subject_id';
                    $errMsg .= 'Total marks should be less than or equal 100 at subject. no ' . ($k + 1);
                    $errors = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }


            }
        }
        // max marks validation according to subject and board combination
        if ($isValid == true && !empty($request->toc_subject)) {
            foreach (@$request->toc_subject as $k => $v) {


                $theoryMaxMarks = DB::table('toc_subject_masters')
                    ->where('board_id', '=', $request->board)
                    ->where('subject_id', '=', $v['subject_id'])
                    ->where('type', 'like', "%TH_MAX%")
                    ->where('type', '!=', "TH_MAX_RATIO")
                    ->first();

                $practicalMaxMarks = DB::table('toc_subject_masters')
                    ->where('board_id', '=', $request->board)
                    ->where('subject_id', '=', $v['subject_id'])
                    ->where('type', 'like', "%PR_MAX%")->first();

                /* change by lokendar singh  */
                /*
					if( !empty($theoryMaxMarks->value) && !empty($practicalMaxMarks->value) &&  @$v['practical'] != null && @$v['theory'] != null && $request->course=='12' && (@$v['theory'] > @$theoryMaxMarks->value || (@$v['practical'] > @$practicalMaxMarks->value)) ){
						$fld = 'subject_id';
						$errMsg .= 'Marks should be less than or equal ( Theory Max Marks : '.@$theoryMaxMarks->value.' & Practical Max Marks : '.@$practicalMaxMarks->value.' ) at subject. no '. ($k+1);
						$errors = $errMsg;
						$validator->getMessageBag()->add($fld, $errMsg);
						$isValid = false;
					}
                */
                if (!empty($theoryMaxMarks->value) && (@$v['theory'] > @$theoryMaxMarks->value)) {
                    $fld = 'subject_id';
                    $errMsg .= 'Marks should be less than or equal ( Theory Max Marks : ' . @$theoryMaxMarks->value . ' ) at subject. no ' . ($k + 1);
                    $errors = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }
                if (!empty($practicalMaxMarks->value) && (@$v['practical'] > @$practicalMaxMarks->value)) {
                    $fld = 'subject_id';
                    $errMsg .= 'Marks should be less than or equal ( Practical Max Marks : ' . @$practicalMaxMarks->value . ' ) at subject. no ' . ($k + 1);
                    $errors = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }
                /* Min marks validation start */

                $gtmin = DB::table('toc_subject_masters')
                    ->where('board_id', '=', $request->board)
                    ->where('subject_id', '=', $v['subject_id'])
                    ->where('type', 'like', "%GT_MIN%")->first();
                if ($request->board == '81') {

                    $total_min = @$gtmin->value;
                } else {
                    $total_min = '33';
                }

                $theoryMinMarks = DB::table('toc_subject_masters')
                    ->where('board_id', '=', $request->board)
                    ->where('subject_id', '=', $v['subject_id'])
                    ->where('type', 'like', "%TH_MIN%")->first();

                $practicalMinMarks = DB::table('toc_subject_masters')
                    ->where('board_id', '=', $request->board)
                    ->where('subject_id', '=', $v['subject_id'])
                    ->where('type', 'like', "%PR_MIN%")->first();

                if ($request->course == 10) {
                    if (!empty($total_min) && !empty(@$v['total']) && @$v['total'] < $total_min) {
                        $fld = 'subject_id';
                        $errMsg .= 'Total Marks should be grater than or equal(total Marks:' . $total_min . ')' . 'at subject. no' . ($k + 1);
                        $errors = $errMsg;
                        $validator->getMessageBag()->add($fld, $errMsg);
                        $isValid = false;
                    }
                }

                if ($request->course == '12') {
                    /*    change by lokendar singh */
                    /*
							if( !empty($theoryMinMarks->value) && !empty($practicalMinMarks->value) && @$v['practical'] != null && @$v['theory'] != null  &&  (@$v['theory'] < @$theoryMinMarks->value || (@$v['practical'] < @$practicalMinMarks->value)) ){

								$fld = 'subject_id';
								$errMsg .= 'Marks should be grater than or equal ( Theory Min Marks : '.@$theoryMinMarks->value.' & Practical Min Marks : '.@$practicalMinMarks->value.' ) at subject. no '. ($k+1);
								$errors = $errMsg;
								$validator->getMessageBag()->add($fld, $errMsg);
								$isValid = false;
							}
                        */
                    if (!empty($theoryMinMarks->value) && @$v['theory'] && (@$v['theory'] < @$theoryMinMarks->value)) {
                        $fld = 'subject_id';
                        $errMsg .= 'Marks should be grater than or equal ( Theory Min Marks : ' . @$theoryMinMarks->value . ' ) at subject. no ' . ($k + 1);
                        $errors = $errMsg;
                        $validator->getMessageBag()->add($fld, $errMsg);
                        $isValid = false;
                    }
                    if (!empty($practicalMinMarks->value) && @$v['practical'] && (@$v['practical'] < @$practicalMinMarks->value)) {
                        $fld = 'subject_id';
                        $errMsg .= 'Marks should be grater than or equal ( Practical Min Marks : ' . @$practicalMinMarks->value . ' ) at subject. no ' . ($k + 1);
                        $errors = $errMsg;
                        $validator->getMessageBag()->add($fld, $errMsg);
                        $isValid = false;
                    }
                }
                /* Min marks validation end */
            }
        }
        // max marks validation according to subject and board combination

        if ($isValid == true) {
            $max = 2;
            $min = 1;
            $masterrecord = DB::table('toc_validations')->where('board_id', $board_id)->where('adm_type', $adm_type)->first();
            if (!empty($masterrecord)) {
                $max = $masterrecord->max_subject_count;
                $min = $masterrecord->min_subject_count;
            }

            // echo "adm_type : ".$adm_type."</br>board_id : ".$board_id."</br>toc_submit_subject : ".$toc_submit_subject."</br>"."min : ".$min."</br>"."max : ".$max;  die;
            if (($min <= $toc_submit_subject) && ($toc_submit_subject <= $max)) {
                $isValid = true;
            } else {
                $isValid = false;
                $errors = 'Please select TOC should be between min & max allowed subject (Min Subject : ' . $min . ') and ' . '(Max Subject : ' . $max . ')';
            }
        }

        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;
        return $response;
    }

    public function checkIsPracticalSubject($subject_id = null, $board_id = null)
    {

        $condtions = null;
        $result = false;
        $condtions['subject_id'] = $subject_id;
        $condtions['board_id'] = $board_id;
        $mainTable = "toc_subject_masters";
        $cacheName = "IsPracticalSubject_" . $subject_id;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->where('type', 'like', "%PR_MAX%")->first('value');
                if (@$result->value > 0) {
                    @$result = true;
                } else {
                    @$result = false;
                }
                return $result;
            });
        }
        return $result;
    }

    public function _getSuppMaxSubjectAllowedNumber($student_id = null)
    {
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        $exam_year = CustomHelper::_get_selected_sessions();
        $minuesValue = 0;
        if ($exam_month == 1) {
            $minuesValue = 2;
        } else if ($exam_month == 2) {
            $minuesValue = 3;
        }
        $stuudentDetails = Student::join('applications', 'applications.student_id', '=', 'students.id')
            ->where('students.id', $student_id)
            ->where('students.adm_type', 1)
            ->where('students.course', 12)
            ->where('applications.year_pass', "<=", ($exam_year - $minuesValue))
            ->where('applications.pre_qualification', 10)
            ->first();
        $allowedMaxSubject = 7;
        if (@$stuudentDetails) {
        } else {
            $allowedMaxSubject = 4;
        }
        return $allowedMaxSubject;
    }

    public function ExamSubjectValidation($request)
    {
        $min = 0;

        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';

        if (!isset($request->subject_id)) {
            $fld = 'subject_id';
            $errMsg .= 'Please select minimum 1 exam subject';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }

        if ($isValid && isset($request->student_id)) {
            $exam_year = CustomHelper::_get_selected_sessions();
            $estudent_id = $student_id = $request->student_id;
            $student_id = Crypt::decrypt($student_id);
            $stuudentDetails = Student::join('applications', 'applications.student_id', '=', 'students.id')
                ->where('students.id', $student_id)
                ->where('students.adm_type', 1)
                ->where('students.course', 12)
                ->where('applications.year_pass', ($exam_year - 1))
                ->where('applications.pre_qualification', 10)
                ->get()->toArray();


            if (@$stuudentDetails) {

                $toc_subjects = TocMark::where('student_id', $student_id)->count();
                $totalSubjects = count($request->subject_id);

                $subjectcount = 4;
                if ($toc_subjects > 0) {
                    $subjectcount = $subjectcount - $toc_subjects;
                }

                if ($totalSubjects > $subjectcount) {
                    $fld = 'subject_id';
                    $errMsg .= 'Fifth subject not allowed.Please select maximum ' . $subjectcount . ' subjects.';
                    $errors = $errMsg;
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                }

                // print_r($totalSubjects);
                // print_r($subjectcount);
                // print_r($errors);
                // die;
            }
        }

        if ($isValid == true && !empty($request->subject_id)) {
            $sbuject_id_arr = array();
            foreach ($request->subject_id as $v) {
                if (!empty($v)) {
                    $sbuject_id_arr[] = $v;
                }
            }
            $isUniqueSubects = $this->checkIsUniqueSubject($sbuject_id_arr);
            if (!$isUniqueSubects) {
                $fld = 'subject_id';
                $errMsg = 'Please select unique subjects for Exam Subject.';
                $errors = $errMsg;
                $validator->getMessageBag()->add($fld, $errMsg);
                $isValid = false;
            }
        }

        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;
        return $response;
    }

    public function PersonalDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;
        $Student = new Student; /// create model object

        // dd($Student->rulesapplicationandstudent);
        if (@$request->is_dgs_student && $request->is_dgs_student == 1) {
            unset($Student->rulesapplicationandstudent['aadhar_number']);
        }

        $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent, $Student->message);
        if ($request->course == 12 && $request->are_you_from_rajasthan == 1) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent12InRajasthan);
        } else if ($request->course == 12 && $request->are_you_from_rajasthan == 1 && $request->disability != 10) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent12InRajasthandisability);
        } else if ($request->course == 12 && $request->are_you_from_rajasthan != 1 && $request->disability != 10) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent12OutOfRajasthandisability);
        } else if ($request->course == 12 && $request->are_you_from_rajasthan != 1) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent12OutOfRajasthan);
        } else if ($request->course == 10 && $request->are_you_from_rajasthan == 1 && $request->disability != 10) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent10InRajasthandisability);
        } else if ($request->course == 10 && $request->are_you_from_rajasthan == 1) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent10InRajasthan);
        } else if ($request->course == 10 && $request->are_you_from_rajasthan != 1 && $request->disability != 10) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudent10outRajasthandisability);
        }


        if (@$request->email) {
            $validators[] = Validator::make($request->all(), $Student->rulesapplicationandstudentEmail);
        }
        if (@$validators) {
            foreach (@$validators as $key => $validator) {
                if ($validator->fails()) {
                    $response[$key]['isValid'] = false;
                    $response[$key]['errors'] = $validator->messages();
                    $response[$key]['validator'] = $validator;
                }
            }
        }


        if ($response == false) {
            $student_id = Crypt::decrypt($request->sid);
            if (@$request->aadhar_number && @$student_id) {
                $exam_year = CustomHelper::_get_selected_sessions();
                $count = 0;
                $count = Student::Join('applications', 'applications.student_id', '=', 'students.id')
                    ->where('students.exam_year', $exam_year)
                    ->whereNotIn('students.id', array($student_id))
                    ->where('applications.aadhar_number', @$request->aadhar_number)
                    ->count();

                if (@$count > 0) {
                    $fld = 'aadhar_number';
                    $errMsg = 'Aadhar number already exist.';
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $response[$key]['isValid'] = false;
                    $response[$key]['errors'] = collect($errMsg);
                    $response[$key]['validator'] = $validator;
                }
            }


            /* Start same combination CHECK */
            $field = "course";
            $checkstudentrecord[$field] = @$request->$field;
            $field = "adm_type";
            $checkstudentrecord[$field] = @$request->$field;
            $field = "stream";
            $checkstudentrecord[$field] = @$request->$field;
            $field = "ssoid";
            $checkstudentrecord[$field] = @$request->$field;
            $isAlreadyMapped = 0;


            if (@$checkstudentrecord['ssoid'] && @$checkstudentrecord['course'] && @$checkstudentrecord['stream'] && @$checkstudentrecord['adm_type']) {

                if (@$checkstudentrecord['ssoid'] && !empty($checkstudentrecord['ssoid'])) {
                    $isAlreadyMapped = Student::
                    where("course", @$checkstudentrecord['course'])
                        ->where("ssoid", $checkstudentrecord['ssoid'])
                        ->whereNotIn('id', array($student_id))
                        ->where("stream", @$checkstudentrecord['stream'])
                        ->where("adm_type", @$checkstudentrecord['adm_type'])
                        ->count();
                } else {
                }
                if (@$isAlreadyMapped > 0) {
                    $key = 'course';
                    $errMsg .= 'You are already mapped with given course,stream and admission type combination.';
                    $isValid = false;
                    $response[$key]['isValid'] = false;
                    $response[$key]['errors'] = $validator->getMessageBag()->add($key, $errMsg);
                    $response[$key]['validator'] = $validator;
                }
            }
            /* End Same combination CHECK */


            if (@$request->ssoid && @$student_id) {
                $ssoid = $request->ssoid;
                $custom_component_obj = new CustomComponent;
                $table_name = 'students';
                $checkssoidallreadyaccessCount = $custom_component_obj->_checkssoidallreadyaccess($table_name, $student_id, $ssoid);
                if (@$checkssoidallreadyaccessCount > 0) {
                    $fld = 'ssoid';
                    $errMsg = 'SSOID already exist.';
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $response[$key]['isValid'] = false;
                    $response[$key]['errors'] = collect($errMsg);
                    $response[$key]['validator'] = $validator;
                }
            }

        }

        return $response;
    }

    public function UserDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;
        if ($request->type == 1) {
            $User = new User; /// create model object
            $validators[] = Validator::make($request->all(), $User->uersmakerules, $User->uersmakerulesmessage);
        } elseif ($request->type == 2) {
            $User = new User; /// create model object
            $validators[] = Validator::make($request->all(), $User->uerseditmakerules, $User->uersmakerulesmessage);
        }
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function SuppSubjectdocumentDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $Supplementary = new Supplementary; /// create model object
        if ($request->course == 12) {
            if (@$request->sec_marksheet_doc) {
                if (!($request->size_sec_marksheet_doc_hidden >= 10 && $request->size_sec_marksheet_doc_hidden <= 100)) {
                    $fld = $request->size_sec_marksheet_doc_hidden;
                    $errMsg = 'कृपया अपलोड किए गए दस्तावेज़ का आकार 10 केबी से 100 केबी होना चाहिए।(Please upload document size should be 10 kb to 100 kb.)';
                    $errors = $errMsg;
                    $isValid = false;
                }
            }
            if (@$request->marksheet_doc) {
                if (!($request->size_marksheet_doc_hidden >= 10 && $request->size_marksheet_doc_hidden <= 100)) {
                    $fld = $request->size_marksheet_doc_hidden;
                    $errMsg = 'कृपया अपलोड किए गए दस्तावेज़ का आकार 10 केबी से 100 केबी होना चाहिए।(Please upload document size should be 10 kb to 100 kb.)';
                    $errors = $errMsg;
                    $isValid = false;
                }
            }
        } elseif ($request->course == 10) {
            if (@$request->size_marksheet_doc_hidden) {
                if (!($request->size_marksheet_doc_hidden >= 10 && $request->size_marksheet_doc_hidden <= 100)) {
                    $fld = $request->size_marksheet_doc_hidden;
                    $errMsg = 'कृपया अपलोड किए गए दस्तावेज़ का आकार 10 केबी से 100 केबी होना चाहिए।(Please upload document size should be 10 kb to 100 kb.)';
                    $errors = $errMsg;
                    $isValid = false;
                }
            }
        }

        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        return $response;
    }

    public function UserDEODetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;
        $User = new User; /// create model object
        $validators[] = Validator::make($request->all(), $User->uersdeomakerules);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function RegistrationDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $inputs = $request->all();
        $validators = null;
        $Student = new Student; /// create model object

        $validators[] = Validator::make($request->all(), $Student->rulesRegistration, $Student->registrationmessage);

        if ($request->are_you_from_rajasthan == 1) {
            $validators[] = Validator::make($request->all(), $Student->rulesRegistrationareyoufromrajasthan);
        }

        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $isValid = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }


        /* Start same combination CHECK */
        $field = "course";
        $checkstudentrecord[$field] = @$request->$field;
        $field = "adm_type";
        $checkstudentrecord[$field] = @$request->$field;
        $field = "stream";
        $checkstudentrecord[$field] = @$request->$field;
        $isAlreadyMapped = 0;
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            $authSSOID = Auth::guard('student')->user()->ssoid;
            $field = "ssoid";
            $checkstudentrecord[$field] = @$authSSOID;
        }

        if ($isValid && @$checkstudentrecord['ssoid'] && @$checkstudentrecord['course'] && @$checkstudentrecord['stream'] && @$checkstudentrecord['adm_type']) {

            if (@$authSSOID && !empty($authSSOID)) {
                $isAlreadyMapped = Student::
                where("course", @$checkstudentrecord['course'])
                    ->where("ssoid", $authSSOID)
                    ->where("stream", @$checkstudentrecord['stream'])
                    ->where("adm_type", @$checkstudentrecord['adm_type'])
                    ->count();
            } else {
            }
            if (@$isAlreadyMapped > 0) {
                $key = 'course';
                $errMsg .= 'You are already mapped with given course,stream and admission type combination.';
                $isValid = false;
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->getMessageBag()->add($key, $errMsg);
                $response[$key]['validator'] = $validator;
            }
        }
        /* End Same combination CHECK */
        return $response;
    }

    public function DGSRegistrationDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $inputs = $request->all();
        $validators = null;
        $Student = new Student; /// create model object

        $validators[] = Validator::make($request->all(), $Student->rulesRegistration, $Student->registrationmessage);

        if ($request->are_you_from_rajasthan == 1) {
            $validators[] = Validator::make($request->all(), $Student->rulesRegistrationareyoufromrajasthan);
        }

        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $isValid = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }


        /* Start same combination CHECK */
        $field = "course";
        $checkstudentrecord[$field] = @$request->$field;
        $field = "adm_type";
        $checkstudentrecord[$field] = @$request->$field;
        $field = "stream";
        $checkstudentrecord[$field] = @$request->$field;
        $isAlreadyMapped = 0;
        $role_id = @Session::get('role_id');
        if ($role_id == Config::get('global.student')) {
            $authSSOID = Auth::guard('student')->user()->username;
            $field = "username";
            $checkstudentrecord[$field] = @$authSSOID;
        }

        if ($isValid && @$checkstudentrecord['username'] && @$checkstudentrecord['course'] && @$checkstudentrecord['stream'] && @$checkstudentrecord['adm_type']) {

            if (@$authSSOID && !empty($authSSOID)) {
                $isAlreadyMapped = Student::
                where("course", @$checkstudentrecord['course'])
                    ->where("username", $authSSOID)
                    ->where("stream", @$checkstudentrecord['stream'])
                    ->where("adm_type", @$checkstudentrecord['adm_type'])
                    ->count();
            } else {
            }

            if (@$isAlreadyMapped > 0) {
                $key = 'course';
                $errMsg .= 'You are already mapped with given course,stream and admission type combination.';
                $isValid = false;
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->getMessageBag()->add($key, $errMsg);
                $response[$key]['validator'] = $validator;
            }
        }
        /* End Same combination CHECK */

        return $response;
    }

    public function isChangeInFormData($model = null, $request = null, $master = null)
    {
        $delete_tbl_arr = array();
        $response = false;
        if ($model == 'Student') {
            if ($request->course != $master->course || $request->adm_type != $master->adm_type || $request->stream != $master->stream) {
                $response = true;
            }

        } else if ($model == 'AdmissionSubject') {
            $old_subject = array();
            if (!empty($master)) {
                foreach ($master as $key => $value) {
                    $old_subject[] = $value['subject_id'];
                }
            }
            if (!empty($request) && !empty($old_subject) && count(array_diff($request, $old_subject)) > 0) {
                $response = true;
            }

        } else if ($model == 'Toc') {
            $new_request = array();

            if (!empty($request)) {
                foreach ($request as $key => $value) {
                    if (!empty($value['subject_id'])) {
                        $new_request[] = $value['subject_id'];
                    }
                }
            }

            $old_subject = array();
            if (!empty($master)) {
                foreach ($master as $key => $value) {
                    $old_subject[] = $value['subject_id'];
                }
            }
            if (!empty($new_request) && !empty($old_subject) && count(array_diff($new_request, $old_subject)) > 0) {
                $response = true;
            }
        } else if ($model == 'ExamSubject') {
            $old_subject = array();
            if (!empty($master)) {
                foreach ($master as $key => $value) {
                    $old_subject[] = $value['subject_id'];
                }
            }
            if (!empty($request) && !empty($old_subject) && count(array_diff($request, $old_subject)) > 0) {
                $response = true;
            }
        }
        return $response;
    }

    public function deleteDataStudentId($student_id = null, $model = null)
    {
        $delete_tbl_arr = array();
        $response = false;

        if ($model == 'Student') {
            $delete_tbl_arr = array('admission_subjects', 'toc', 'toc_marks', 'exam_subjects', 'student_fees');
        } else if ($model == 'AdmissionSubject') {
            $delete_tbl_arr = array('toc', 'toc_marks', 'exam_subjects', 'student_fees');
            Application::where('student_id', $student_id)->update(['is_toc_marked' => null, 'toc' => null]);
        } else if ($model == 'Toc') {
            $delete_tbl_arr = array('exam_subjects', 'student_fees');
        } else if ($model == 'ExamSubject') {
            $delete_tbl_arr = array('student_fees');
        }
        //22092023
        /* by pass student fee not deleted in case of academic/devloper login start */
        $role_id = Session::get('role_id');
        $super_admin_id = Config::get("global.super_admin_id");
        $developer_admin = Config::get("global.developer_admin");
        if ($role_id == $super_admin_id || $role_id == $developer_admin) {
            $indexCheck = array_search('student_fees', $delete_tbl_arr);
            if ($indexCheck > -1) {
                unset($delete_tbl_arr[$indexCheck]);
            }
        } else {
        }
        /* by pass student fee not deleted in case of academic/devloper login end */


        if (!empty($delete_tbl_arr) && !empty($student_id)) {
            foreach ($delete_tbl_arr as $tbl) {
                DB::table($tbl)->where('student_id', $student_id)->delete();
            }
            $response = true;
        }
        return $response;
    }

    public function _getBoardNRReportData($course = null, $stream = null, $exam_year = null)
    {

        $subject_lists = $this->subjectList($course);
        $sqlStudent = "SELECT ecd.ai_code,ecd.ecenter10,ecd.ecenter12,ecd.cent_name,es.subject_id,sub.subject_code,sub.name,count( es.id ) as count FROM rs_exam_subjects es
			inner JOIN rs_subjects sub ON sub.id = es.subject_id
			LEFT JOIN rs_students s ON s.id = es.student_id
			LEFT JOIN rs_examcenter_details ecd ON ecd.ai_code = s.ai_code 
			where s.course =  " . $course . " and s.stream =  " . $stream . " and s.exam_year =  " . $exam_year . " GROUP BY es.subject_id,ecd.ai_code;";
        //dd($subject_lists);
        $resultStudent = DB::select($sqlStudent);
        $arr = null;
        $counter = 0;
        foreach ($resultStudent as $k => $v) {
            $fld = "ai_code";
            $ai_code = $v->$fld;
            $arr[$ai_code][$fld] = $v->$fld;
            $fld = "ecenter" . $course;
            $arr[$ai_code][$fld] = $v->$fld;
            $fld = "cent_name";
            $arr[$ai_code][$fld] = $v->$fld;
            $fld = "subject_code";
            $subCodeVar = $v->$fld;
            $fld = "count";
            $arr[$ai_code]["Sub_" . $subCodeVar] = $v->$fld;
            $counter++;
        }
        $finalArray['students'] = $arr;
        $sqlSupplementary = "SELECT ecd.ai_code,ecd.ecenter10,ecd.ecenter12,ecd.cent_name,ss.subject_id,sub.subject_code,sub.name,count( ss.id ) as count FROM rs_supplementary_subjects ss
				inner JOIN rs_subjects sub ON sub.id = ss.subject_id
				LEFT JOIN rs_students s ON s.id = ss.student_id
				LEFT JOIN rs_examcenter_details ecd ON ecd.ai_code = s.ai_code 
				where s.course =  " . $course . " and s.stream =  " . $stream . " and s.exam_year =  " . $exam_year . " GROUP BY ss.subject_id,ecd.ai_code;";
        $resultSupplementary = DB::select($sqlSupplementary);
        $arr = null;
        $counter = 0;
        foreach ($resultSupplementary as $k => $v) {
            $fld = "ai_code";
            $ai_code = $v->$fld;
            $arr[$ai_code][$fld] = $v->$fld;
            $fld = "ecenter" . $course;
            $arr[$ai_code][$fld] = $v->$fld;
            $fld = "cent_name";
            $arr[$ai_code][$fld] = $v->$fld;
            $fld = "subject_code";
            $subCodeVar = $v->$fld;
            $fld = "count";
            $arr[$ai_code]["Sub_" . $subCodeVar] = $v->$fld;
            $counter++;
        }
        $finalArray['supplementary'] = $arr;
        return @$finalArray;
    }

    public function getStudentPdfDetails($student_id = null)
    {
        // $master = Student::with('admission_subject','toc_subject','exam_subject')->where('id',$student_id)->first();

        $masterTemp = Student::where('id', $student_id)->first();
        $exam_year = $masterTemp->exam_year;
        $exam_month = $masterTemp->exam_month;
        $master = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject')->with('exam_subject', function ($query) use ($exam_year, $exam_month) {
            $query->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $exam_month);
        })->where('id', $student_id)->first();


        $subject_list = $this->subjectList();
        $output['admissionSubjectDetails'] = array();
        $output['tocSubjectDetails'] = array();
        $output['examSubjectDetails'] = array();

        foreach (@$master->admission_subject as $k => $v) {
            $extraFlag = null;
            if ($v['is_additional'] == 1) {
                $extraFlag = " (Additional)";
            }
            $output['admissionSubjectDetails'][$k]['subject_id'] = @$subject_list[$v['subject_id']] . $extraFlag;
            $output['admissionSubjectDetails'][$k]['is_additional'] = @$v['is_additional'];
        }

        foreach (@$master->toc_subject as $k => $v) {
            $output['tocSubjectDetails'][$k]['subject_id'] = @$subject_list[$v['subject_id']];
            $output['tocSubjectDetails'][$k]['theory'] = @$v['theory'];
            $output['tocSubjectDetails'][$k]['practical'] = @$v['practical'];
            $output['tocSubjectDetails'][$k]['total_marks'] = @$v['total_marks'];
        }


        foreach (@$master->exam_subject as $k => $v) {
            $extraFlag = null;
            if ($v['is_additional'] == 1) {
                $extraFlag = " (Additional)";
            }
            $output['examSubjectDetails'][$k]['subject_id'] = @$subject_list[$v['subject_id']] . $extraFlag;
        }
        return $output;

    }

    public function getsuppStudentPdfDetails($student_id = null)
    {

        $master = Student::with('admission_subject', 'toc_subject', 'exam_subject')->where('id', $student_id)->first();
        $subject_list = $this->subjectList();
        $output['admissionSubjectDetails'] = array();
        $output['tocSubjectDetails'] = array();
        $output['examSubjectDetails'] = array();

        if (@$master->admission_subject) {
            foreach (@$master->admission_subject as $k => $v) {
                $output['admissionSubjectDetails'][$k]['subject_id'] = @$subject_list[$v['subject_id']];
                $output['admissionSubjectDetails'][$k]['is_additional'] = @$v['is_additional'];
            }
        }
        if (@$master->toc_subject) {
            foreach (@$master->toc_subject as $k => $v) {
                $output['tocSubjectDetails'][$k]['subject_id'] = @$subject_list[$v['subject_id']];
            }
        }
        if (@$master->exam_subject) {
            foreach (@$master->exam_subject as $k => $v) {
                $output['examSubjectDetails'][$k]['subject_id'] = @$subject_list[$v['subject_id']];
            }
        }


        return $output;

    }

    public function masterFilterDetails($type = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($type)) {
            $condtions = ['status' => 1, 'type' => $type];
        }
        $mainTable = "report_filters";
        $cacheName = $mainTable . "_" . $type;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $fields = ['field_table_name', 'label', 'field', 'input_type'];
                $result = DB::table($mainTable)->where($condtions)->get($fields);
                return $result;
            });
        }
        return $result;
    }

    public function getSubjectIdByPracticalTheory($practicaL_type = null)
    {
        $condtions = null;
        $result = array();

        if ($practicaL_type != null) {
            $condtions['practicaL_type'] = $practicaL_type;
        }
        $mainTable = "subjects";
        $cacheName = "subjects_id_" . $practicaL_type;
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->whereNull('deleted_at')->where($condtions)->orderBy('id', 'ASC')->pluck('id', 'id');
                return $result;
            });
        }
        return $result;
    }

    public function getSubjectByCoursePracticalTheory($course = null, $practicaL_type = null)
    {
        $condtions = null;
        $result = array();
        if ($course != null) {
            $condtions['deleted'] = 0;
        }
        if ($course != null) {
            $condtions['course'] = $course;
        }
        if ($practicaL_type != null) {
            $condtions['practicaL_type'] = $practicaL_type;
        }
        $mainTable = "subjects";
        $cacheName = "subjects_name_code_" . $course . "_" . $practicaL_type;
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->whereNull('deleted_at')->where($condtions)->orderBy('subject_code', 'ASC')->pluck('name', 'id');
                return $result;
            });
        }
        return $result;
    }

    public function _getCONVTocMarkTheory($board = null, $subid = null, $marks = null, $practicalNewConvMarks = null, $practicalMarks = null)
    {
        $theryMarks = $marks + ($practicalMarks - $practicalNewConvMarks);
        return $theryMarks;
    }

    public function _getCONVTocMarkPractical($board = null, $subid = null, $marks = null, $theory = null)
    {
        $subject_code = $this->getSubjectCode($subid);
        $getData = DB::table('toc_subject_masters')
            ->where('board_id', '=', $board)
            ->where('subject_code', '=', $subject_code)
            ->where('type', 'like', "%PR_MAX%")->first();

        $getTHMaxData = DB::table('toc_subject_masters')
            ->where('board_id', '=', $board)
            ->where('subject_code', '=', $subject_code)
            ->where('type', 'like', "%TH_MAX%")->first();

        $getRSOSData = DB::table('toc_subject_masters')
            ->where('board_id', '=', 81)
            ->where('subject_code', '=', $subject_code)
            ->where('type', 'like', "%PR_MAX%")->first();

        $outputResult = 0;
        if (@$getData->value && @$getRSOSData->value && $getData->value > 0 && $getRSOSData->value > 0) {
            $outputResult = ($marks * $getRSOSData->value) / $getData->value;
        }
        if (@$getData->value && @$getRSOSData->value && $getData->value == 0 && $getRSOSData->value > 0) {

            $outputResult = ($theory * $getRSOSData->value) / $getTHMaxData->value;
        }

        return round($outputResult, 0);
    }

    public function getSubjectCode($subject_id = null)
    {
        $condtions = null;
        $result = array();
        if ($subject_id != null) {
            $condtions = ['id' => $subject_id, 'deleted' => 0];
        }

        $mainTable = "subjects";
        $cacheName = "Subjects_" . $subject_id;
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->get(['subject_code', 'id'])->first();
                return $result->subject_code;
            });
        }
        return $result;
    }

    public function _getRoleRoute($role_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($role_id)) {
            $condtions = ['role_id' => $role_id];
        }
        $mainTable = "role_routes";
        $cacheName = $mainTable . "_" . $role_id;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $fields = ['id', 'route', 'role_id'];
                $result = DB::table($mainTable)->where($condtions)->first($fields);
                return $result;
            });
        }
        return $result;
    }

    public function _isStudentFormLockAndSubmit($student_id = null)
    {
        $masterrecord = Application::where('student_id', $student_id)->first();
        $fld = "locksumbitted";
        $isLockAndSubmit = false;
        $role_id = Session::get('role_id');

        if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 1 && !in_array($role_id, array(71, 58, 59))) {
            $isLockAndSubmit = true;
        }
        return $isLockAndSubmit;
    }

    public function _isCheckStudentFormLockAndSubmit($student_id = null)
    {
        $masterrecord = Application::where('student_id', $student_id)->first();

        $fld = "locksumbitted";
        $isLockAndSubmit = false;
        $devloper_admin = Config::get('global.developer_admin');
        $super_admin = Config::get('global.super_admin');
        $role_id = Session::get('role_id');

        if (in_array($role_id, array($devloper_admin))) { //,$super_admin
            $isLockAndSubmit = false;
        } elseif (in_array($role_id, array($super_admin))) {
            $isLockAndSubmit = true;
        } else {
            if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 1) {
                $isLockAndSubmit = true;
            }
        }

        return $isLockAndSubmit;
    }

    /*public function _getBserCONVTocMarkTheory($board=null,$subid=null,$marks=null,$practicalNewConvMarks=null){
		$theryMarks = $marks - $practicalNewConvMarks;
	    return $theryMarks;
    }*/

    public function _isCheckrevalStudentFormLockAndSubmit($reval_id = null)
    {

        $masterrecord = RevalStudent::where('id', $reval_id)->first();
        $fld = "locksumbitted";
        $isLockAndSubmit = false;
        $devloper_admin = Config::get('global.developer_admin');
        $super_admin = Config::get('global.super_admin');
        $role_id = Session::get('role_id');
        if (in_array($role_id, array($devloper_admin, $super_admin))) {
            $isLockAndSubmit = false;
        } else {
            if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 1) {
                $isLockAndSubmit = true;
            }
        }

        return $isLockAndSubmit;
    }


    /* public function _getBserCONVTocMarkPractical($board=null,$subid=null,$marks=null){
		$subject_code = $this->getSubjectCode($subid);
		$getData = DB::table('toc_subject_masters')
		->where('board_id','=',$board)
		->where('subject_code','=',$subject_code)
		->where('type','like',"%PR_MAX%")->first();

		$getRSOSData = DB::table('toc_subject_masters')
		->where('board_id','=',81)
		->where('subject_code','=',$subject_code)
		->where('type','like',"%PR_MAX%")->first();

		$outputResult = 0;
        if(@$getRSOSData->value && $getData->value == 0 && $getRSOSData->value > 0){
            $outputResult = ($marks * $getRSOSData->value)/100;
        }

		return round($outputResult,0);
    }*/

    public function _isStudentFormLockAndSubmitForSessional($student_id = null)
    {
        $masterrecord = Application::where('student_id', $student_id)->first();
        $fld = "locksumbitted";
        $isLockAndSubmit = false;
        if ($masterrecord[$fld] == 1) {
            $isLockAndSubmit = true;
        }
        return $isLockAndSubmit;
    }

    public function _isSuppFormLockAndSubmit($student_id = null)
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        $masterrecord = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->first();

        $fld = "locksumbitted";
        $isLockAndSubmit = false;
        $role_id = Session::get('role_id');
        if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 1 && !in_array($role_id, array(71))) {
            $isLockAndSubmit = true;
        }
        return $isLockAndSubmit;
    }

    public function _isItiStudent($student_id = null)
    {
        $masterrecord = Student::where('id', $student_id)->first();

        $fld = "adm_type";
        $isItiStudent = false;
        if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 5) {
            $isItiStudent = true;
        }
        return $isItiStudent;
    }

    public function _isPartAdmissionStudent($student_id = null)
    {
        $masterrecord = Student::where('id', $student_id)->first();

        $fld = "adm_type";
        $isPartAdmissionStudent = false;
        if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 3) {
            $isPartAdmissionStudent = true;
        }
        return $isPartAdmissionStudent;
    }

    public function _isImprovementStudent($student_id = null)
    {
        $masterrecord = Student::where('id', $student_id)->first();

        $fld = "adm_type";
        $isImprovementStudent = false;
        if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 4) {
            $isImprovementStudent = true;
        }
        return $isImprovementStudent;
    }

    public function isValidAPISessionalMarks($inputs = null)
    {
        $isValid = true;
        $response = null;
        $errors = null;
        $validator = Validator::make([], []);

        if (!empty(@$inputs)) {
            $fld = 'subject_id';
            $counter = 1;
            $errMsg = null;
            $subject_list = $this->subjectList();
            $subject_id = @$inputs[$fld];
            $obtained_marks = @$inputs['obtained_marks'];
            $custom_component_obj = new CustomComponent;
            $isDateClosed = $custom_component_obj->_checkSessionalMarksAPIEntryAllowOrNotAllow();

            if ($subject_id != null) {
                if ($isDateClosed != true) {
                    $errMsg .= 'Sessional marks entries date has been closed.Please contact for more details.';
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                    $errors[$fld] = $errMsg;
                }
            }

            if ($isValid && $subject_id != null) {
                if ($obtained_marks == null) {
                    $errMsg .= 'Please enter all mandatory subject marks details. Subjects  ';
                    if (isset($subject_list[$subject_id])) {
                        $errMsg .= @$subject_list[$subject_id] . " ";
                    }
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                    $errors[$fld] = $errMsg;
                }
            }


            $maxMarks = $this->getSessionalSubjectMaxMarks();
            $minMarks = $this->getSessionalSubjectMinMarks();
            $counter2 = 1;
            $errMsg2 = null;

            if ($isValid) {
                $v2 = @$inputs['obtained_marks'];
                $k2 = @$subject_id;
                if ($v2 != null && $v2 != "AB") {
                    $maxValue = @$maxMarks[$k2];
                    $minValue = @$minMarks[$k2];
                    $minValue = 0;//as per aashish
                    if ($v2 > $maxValue || $v2 < $minValue) {
                        if ($counter2 == 1) {
                            $errMsg2 .= 'Please enter valid maximum and minimum marks below given subjects  ';
                        }
                        $errMsg2 .= "\n " . $counter2 . ". " . @$subject_list[$k2] . "[ Maximum = " . $maxValue . " " . " &  Minimum = " . $minValue . "] ";
                        $validator->getMessageBag()->add($fld, $errMsg2);
                        $isValid = false;
                        $errors[$fld] = $errMsg2;
                        $counter2++;
                    }
                }
            }
        }

        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;
        return $response;
    }

    public function getSessionalSubjectMaxMarks()
    {
        $condtions = null;
        $result = array();
        $mainTable = "subjects";
        $cacheName = "sessional_subjects_max_marks_";

        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->get()->pluck('sessional_max_marks', 'id');
                return $result;
            });
        }
        return $result;
    }

    public function getSessionalSubjectMinMarks()
    {
        $condtions = null;
        $result = array();
        $mainTable = "subjects";
        $cacheName = "sessional_subjects_min_marks_";

        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->get()->pluck('sessional_min_marks', 'id');
                return $result;
            });
        }
        return $result;
    }

    public function isValidSessionalMarks($inputs = null)
    {
        $isValid = true;
        $response = null;
        $errors = null;
        $validator = Validator::make([], []);

        if (!empty(@$inputs)) {
            $fld = 'subject_id';
            $counter = 1;
            $errMsg = null;
            $subject_list = $this->subjectList();
            foreach (@$inputs[$fld] as $k => $v) {
                if ($v == null) {
                    if ($counter == 1) {
                        $errMsg .= 'Please enter all mandatory subject marks details. Subjects  ';
                    }
                    $errMsg .= $subject_list[$k] . " ";
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $isValid = false;
                    $errors[$fld] = $errMsg;
                    $counter++;
                }
            }

            $maxMarks = $this->getSessionalSubjectMaxMarks();
            $minMarks = $this->getSessionalSubjectMinMarks();
            $counter2 = 1;
            $errMsg2 = null;

            if ($isValid) {
                foreach (@$inputs[$fld] as $k2 => $v2) {
                    if ($v2 != null && $v2 != "AB") {
                        $maxValue = $maxMarks[$k2];
                        $minValue = $minMarks[$k2];
                        $minValue = 0;//as per aashish
                        if ($v2 > $maxValue || $v2 < $minValue) {
                            if ($counter2 == 1) {
                                $errMsg2 .= 'Please enter valid maximum and minimum marks below given subjects  ';
                            }
                            $errMsg2 .= "\n " . $counter2 . ". " . $subject_list[$k2] . "[ Maximum = " . $maxValue . " " . " &  Minimum = " . $minValue . "] ";
                            $validator->getMessageBag()->add($fld, $errMsg2);
                            $isValid = false;
                            $errors[$fld] = $errMsg2;
                            $counter2++;
                        } else {

                            continue;
                        }
                    } else {
                        continue;
                    }

                }
            }
        }
        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;
        return $response;
    }

    public function _sortArray($tableData = null)
    {
        $newArr = array();
        $counter = 1;
        foreach ($tableData as $key => $value) {
            if (@$value['order']) {
                $newArr[$value['order']] = $value;
            } else {
                $newArr[$counter] = $value;
            }
            $counter++;
        }
        ksort($newArr);
        return $newArr;
    }

    public function getSupplementaryStudentRequriedDocument($student_id = null)
    {
        $student = Student::where('students.id', $student_id)->first('course');
        if (@$student->course == 12) {
            $documentInput["sec_marksheet_doc"] = "Upload 10th Passed Marksheet " . config('global.starMark');
            $documentInput['label']["sec_marksheet_doc_label"] = "Please upload 10th passed valid marksheet same  as 10th passing year filled in the form. " . config('global.starMark');
        }
        $documentInput["marksheet_doc"] = "Upload Failed Marksheet " . config('global.starMark');
        //$documentInput['label']["marksheet_doc_label"] = " (Marksheet) : Last valid marksheet upload ".config('global.starMark');
        $documentInput['label']["marksheet_doc_label"] = "Please upload last Failed marksheet " . config('global.starMark');
        return $documentInput;
    }

    public function _vijaySirgetLateFeeAmount($stream = null, $gender_id = null, $student_id = null)
    {
        $lateFeeExtraMarginDays = 0;
        $lateFee = 0;
        if (!empty($student_id) && $student_id > 0) {
            $studentsDetails = Student::where('students.id', '=', $student_id)
                ->join('applications', 'applications.student_id', '=', 'students.id')
                ->first();

            $studentLockedDate = $studentsDetails->locksubmitted_date;

            if (@$studentsDetails->locksumbitted && @$studentsDetails->locksubmitted_date) {
                if ($studentsDetails->fee_paid_amount == null) {
                    $masterMarginDays = ExamLateFeeDate::where('stream', $stream)
                        ->where('gender_id', $gender_id)
                        ->where('from_date', '<=', $studentLockedDate)
                        ->where('to_date', '>=', $studentLockedDate)
                        ->where('is_supplementary', '=', "")
                        ->first();

                    $toDateTempFinal = null;
                    $lateFeeExtraMarginDays = $masterMarginDays->latefee_extra_days;
                    $toDateTemp = $masterMarginDays->to_date;
                    if ($lateFeeExtraMarginDays > 0) {
                        $toDateTempFinal = date('Y-m-d', strtotime($toDateTemp . ' +' . $lateFeeExtraMarginDays . ' day'));
                    }


                    $date1 = date_create(Carbon::now());
                    $date2 = date_create($toDateTempFinal);
                    if ($date1 >= $date2) {
                        $CurrentSlotlatefees = ExamLateFeeDate::where('stream', $stream)
                            ->where('gender_id', $gender_id)
                            ->where('from_date', '<=', Carbon::now())
                            ->where('to_date', '>=', Carbon::now())
                            ->where('is_supplementary', '=', "")
                            ->first();
                        $lateFee = $CurrentSlotlatefees->late_fee;

                    }
                    // if(@$masterMarginDays->latefee_extra_days){
                    // 	$lateFeeExtraMarginDays = $masterMarginDays->latefee_extra_days;
                    // }
                }
            }
        }
        // $currentDate = Carbon::now();
        // if($lateFeeExtraMarginDays > 0 ){
        // 	$currentDate->subDays($lateFeeExtraMarginDays);
        // }
        // $toDate = $currentDate;


        // $master = ExamLateFeeDate::where('stream',$stream)
        // 	->where('gender_id',$gender_id)
        // 	->where('from_date', '<=', Carbon::now())
        // 	->where('to_date', '>=', $toDate)
        // 	->where('is_supplementary', '=', "")
        // 	->first();


        if (@$lateFee) {
            return $lateFee;
        } else {
            return $lateFee;
        }


        // if(!@$master->late_fee){
        // 	return 0;
        // }
        // return @$master->late_fee;
    }

    public function _RohitgetLateFeeAmount($stream = null, $gender_id = null, $student_id = null)
    {
        $lateFeeExtraMarginDays = 0;
        if (!empty($student_id) && $student_id > 0) {
            $studentsDetails = Student::where('students.id', '=', $student_id)
                ->join('applications', 'applications.student_id', '=', 'students.id')
                ->first();

            if (@$studentsDetails->locksumbitted && @$studentsDetails->locksubmitted_date) {
                $studentLocked = $studentsDetails->locksubmitted_date;
                if ($studentsDetails->fee_paid_amount == null) {
                    $masterMarginDays = ExamLateFeeDate::where('stream', $stream)
                        ->where('gender_id', $gender_id)
                        ->where('from_date', '<=', Carbon::now())
                        ->where('to_date', '>=', Carbon::now())
                        ->where('is_supplementary', '=', "")
                        ->first();
                    // dd($masterMarginDays);
                    if (@$masterMarginDays->latefee_extra_days) {
                        $lateFeeExtraMarginDays = $masterMarginDays->latefee_extra_days;
                    }
                }
            }
        }
        $currentDate = Carbon::now();
        if ($lateFeeExtraMarginDays > 0) {
            $currentDate->subDays($lateFeeExtraMarginDays);
        }

        $toDate = $currentDate;
        $master = ExamLateFeeDate::where('stream', $stream)
            ->where('gender_id', $gender_id)
            ->where('from_date', '<=', Carbon::now())
            ->where('to_date', '>=', $toDate)
            ->where('is_supplementary', '=', "")
            ->first();
        if (!@$master->late_fee) {
            return 0;
        }
        return @$master->late_fee;
    }

    public function _correctgetLateFeeAmount($stream = null, $gender_id = null)
    {
        $master = ExamLateFeeDate::where('stream', $stream)
            ->where('gender_id', $gender_id)
            ->where('from_date', '<=', Carbon::now())
            ->where('to_date', '>=', Carbon::now())
            ->where('is_supplementary', '=', "")
            ->first();
        if (!@$master->late_fee) {
            return 0;
        }
        return @$master->late_fee;
    }

    public function _getSuppLateFeeAmount($stream = null, $gender_id = null)
    {
        $master = ExamLateFeeDate::where('stream', $stream)
            ->where('gender_id', $gender_id)
            ->where('from_date', '<=', Carbon::now())
            ->where('to_date', '>=', Carbon::now())
            ->where('is_supplementary', '=', 1)
            ->first();
        if (!@$master->late_fee) {
            return 0;
        }
        return @$master->late_fee;
    }

    public function getSupplementaryStudentDetails($student_id = null)
    {
        $exam_year = CustomHelper::_get_selected_sessions();

        if (@$exam_year) {

        } else {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }

        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');

        $current_folder_year = $this->getCurrentYearFolderName();
        $master = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject', 'exam_subject')->where('id', $student_id)->first();
        $supplementaries = Supplementary::where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('student_id', $student_id)->first();
        @$changerequeststudent = SuppChangeRequestStudents::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('supp_id', @$supplementaries->id)->orderBy('id', 'desc')->first('id');

        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);
        $combo_name = 'supp_verfication_status';
        $supp_verfication_status = $this->master_details($combo_name);
        $yesno[""] = "No";
        $rsos_years = $this->rsos_years();
        $checkchangerequestsssupplementariesAllowOrNotAllow = $this->_checkchangerequestssupplementariesAllowOrNotAllow();
        $subject_list = $this->subjectList();
        $combo_name = 'student_supplementary_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_id;
        $type = ('supplementary_documents');
        if (!empty(@$supplementaries->update_supp_change_requests_challan_tid)) {
            @$labelchangerequest = "आपकी शेष फीस का भुगतान कर दिया गया है( Your Remaining fees Paid )";
        } else {
            @$labelchangerequest = "शेष फीस का भुगतान करना होगा(Remaining fees need to be pay )";
        }

        //$sec_fieldname = encrypt(@$supplementaries->sec_marksheet_doc);
        $sec_fieldname = @$supplementaries->sec_marksheet_doc;
        // $fieldname = encrypt(@$supplementaries->marksheet_doc);
        $fieldname = (@$supplementaries->marksheet_doc);
        $routename = 'documentdownload';
        $suppfees = Supplementary::where('student_id', $student_id)
            ->where('exam_year', $exam_year)
            ->where('exam_month', $exam_month)
            ->first();

        $changerequertoldsuppfees = SuppChangeRequertOldStudentFees::where('student_id', $student_id)
            ->where('supp_student_change_request_id', @$changerequeststudent->id)->where('supp_id', @$supplementaries->id)
            ->first('total_fees');

        /* Com. subjects start */
        //$mastersupp = SupplementarySubject::where('student_id',$student_id)->where('is_additional_subject','<>', '',)->get();
        /* Com. subjects end */

        /* Already Passed subjects start */
        //$mastersuppexamsubject = ExamSubject::where('student_id',$student_id)->where('final_result','P',)->get()->toArray();
        /* Already Passed subjects end */

        /* Additional subjects start */
        //$master_subject_details1 = SupplementarySubject::where('student_id',$student_id)->where('is_additional_subject','IS NULL', null,)->get()->toArray();
        /* Additional subjects end */

        /* Com. subjects start */
        $mastersupp = Supplementary::
        join('supplementary_subjects', "supplementaries.id", "supplementary_subjects.supplementary_id")
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            //->whereNotNull('supplementary_subjects.is_additional_subject')
            ->where('supplementary_subjects.is_additional_subject', 1)
            // ->groupBy('supplementaries.student_id')
            ->orderBy('supplementaries.id', 'desc')
            ->get();


        /* Com. subjects end */

        /* Already Passed subjects start */
        $mastersuppexamsubjects = ExamSubject::where('student_id', $student_id)
            ->where('final_result', 'P')->latest('exam_year')->first('exam_year');

        if (@$mastersuppexamsubjects->exam_year && !is_null($mastersuppexamsubjects->exam_year)) {
            $mastersuppexamsubject = ExamSubject::where('student_id', $student_id)
                ->where('final_result', 'P')->where('exam_year', $mastersuppexamsubjects->exam_year)->get()->toArray();
        }

        /* Already Passed subjects end */
        /* Additional subjects start */
        $master_subject_details1 = Supplementary::
        join('supplementary_subjects', "supplementaries.id", "supplementary_subjects.supplementary_id")
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->where('supplementary_subjects.is_additional_subject', 'IS NULL', null)
            // ->groupBy('supplementaries.student_id')
            ->orderBy('supplementaries.id', 'desc')
            ->get()
            ->toArray();

        /* Additional subjects end */

        if (!is_null(@$mastersuppexamsubject) && !is_null(@$master_subject_details1)) {
            $result = array_merge(@$mastersuppexamsubject, @$master_subject_details1);
        }
        // echo "test"; die;

        $subject_list = $this->subjectList();

        $output['personalDetails'] = array(
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन संख्या (Enrollment No)",
                "value" => @$master->enrollment
            ),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            "mobile" => array(
                "fld" => "mobile",
                "label" => "मोबाइल (Mobile Number)",
                "value" => @$master->mobile
            ),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth)",
                "value" => date('d-m-Y', strtotime(@$master->dob)),
            ),
            "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$master->course
            ),
            "stream" => array(
                "fld" => "stream",
                "label" => "स्ट्रीम (Stream)",
                "value" => @$stream_id[@$master->stream]
            ),
            "adm_type" => array(
                "fld" => "adm_type",
                "label" => "प्रवेश प्रकार (Admission Type)",
                "value" => @$adm_types[@$master->adm_type]
            ),
            "ssoid" => array(
                "fld" => "ssoid",
                "label" => "एसएसओ (SSO)",
                "value" => @$master->ssoid
            ),
            "is_self_filled" => array(
                "fld" => "is_self_filled",
                "label" => "विद्यार्थी द्वारा भरा गया?(Filled in by the student?)",
                "value" => @$yesno[@$supplementaries->is_self_filled]
            ),
            "is_eligible" => array(
                "fld" => "is_eligible",
                "label" => "विद्यार्थी पात्र है(Is Eligible)",
                "value" => @$yesno[@$supplementaries->is_eligible]
            )
        );


        if (@$master->course == 12) {
            $output['personalDetails']['pre_qualification'] = array(
                "fld" => "pre_qualification ",
                "label" => "पूर्व योग्यता (Pre Qualification)",
                "value" => @$pre_qualifi[@$master->application->pre_qualification]
            );
            $output['personalDetails']['year_pass'] = array(
                "fld" => "year_pass ",
                "label" => "वर्ष पास (Year Pass)",
                "value" => @$rsos_years[@$master->application->year_pass]
            );
        }


        $output['FeesDetails'] = array(
            "subject_change_fees" => array(
                "fld" => "subject_change_fees",
                "label" => "विषय परिवर्तन शुल्क (Subject Change Fees)",
                "value" => @$suppfees->subject_change_fees
            ),
            "exam_fees" => array(
                "fld" => "exam_fees",
                "label" => "परीक्षा शुल्क(Exam Fees)",
                "value" => @$suppfees->exam_fees
            ),
            "practical_fees" => array(
                "fld" => "practical_fees",
                "label" => "प्रायौगिक शुल्क(Practical Fees)",
                "value" => @$suppfees->practical_fees
            ),
            "forward_fees" => array(
                "fld" => "forward_fees",
                "label" => "अग्रेषण शुल्क(Forwarding Fees)",
                "value" => @$suppfees->forward_fees
            ),
            "online_fees" => array(
                "fld" => "online_fees",
                "label" => "ऑनलाइन सेवा शुल्क(Online Services Fees)",
                "value" => @$suppfees->online_fees
            ),
            "late_fees" => array(
                "fld" => "late_fees",
                "label" => "विलम्ब शुल्क(Late Fees)",
                "value" => @$suppfees->late_fees
            ),
            "final_fees" => array(
                "fld" => "total_fees",
                "label" => "कुल शुल्क (Total Fees)",
                "value" => @$suppfees->total_fees
            )
        );

        if (@$checkchangerequestsssupplementariesAllowOrNotAllow == 'true' && @$supplementaries->supp_student_change_requests == 2) {
            $output['FeesDetails']["previous_fees"] = array(
                "fld" => "total_fees",
                "label" => "पिछली फीस का भुगतान किया गया( Previous fees paid)",
                "value" => @$changerequertoldsuppfees->total_fees
            );

            $output['FeesDetails']["remaining_fees"] = array(
                "fld" => "total_fees",
                "label" => @$labelchangerequest,
                "value" => @$suppfees->total_fees - @$changerequertoldsuppfees->total_fees
            );
        }


        if (!empty($result)) {
            foreach (@$result as $k => $v) {
                $subName = null;
                if (@$v['final_result'] && (@$v['final_result'] == "P" || @$v['final_result'] == "PASS" || @$v['final_result'] == "p")) {
                    $subName = "<span style='color:green;font-size:12px;'>" . @$subject_list[$v['subject_id']] . " (PASS)</span>";
                } else {
                    $subName = "<span style='color:red;font-size:12px;'>" . @$subject_list[$v['subject_id']] . "</span>";
                }
                $subName = @$subject_list[$v['subject_id']];

                $output['compulsorySubjectDetails'][] = array(
                    "fld" => "subject_id",
                    "label" => " (Subject  " . ($k + 1) . ")",
                    "value" => $subName
                );
            }
        }

        if (!empty($mastersupp)) {
            foreach (@$mastersupp as $k => $v) {
                $output['additionalSubjectDetails'][] = array(
                    "fld" => "subject_id",
                    "label" => " (Subject " . ($k + 1) . ")",
                    "value" => @$subject_list[@$v['subject_id']]
                );
            }
        }

        $lockedStatus = "No";
        if (@$suppfees->locksumbitted) {
            $lockedStatus = "Yes";
        }

        if (@$suppfees->locksubmitted_date) {
            $output['LockSubmttedDetails'] = array(
                "locksumbitted" => array(
                    "fld" => "locksumbitted ",
                    "label" => "लॉक और सबमिट किया गया है ? (Is Lock & Submitted)",
                    "value" => @$lockedStatus
                ),
                "locksubmitted_date" => array(
                    "fld" => "locksubmitted_date",
                    "label" => "लॉक और जमा करने की तिथि (Lock & Submitted Date)",
                    "value" => Carbon::createFromFormat('Y-m-d H:i:s', @$suppfees->locksubmitted_date)->format('d-m-Y H:i A')
                ),
            );
        }

        if (@$suppfees->submitted) {
            $output['TransactionDetails'] = array(
                "challan_tid" => array(
                    "fld" => "challan_tid ",
                    "label" => " चालान संख्या (Challan Number)",
                    "value" => @$suppfees->challan_tid
                ),
                "submitted" => array(
                    "fld" => "submitted",
                    "label" => "शुल्क जमा करने की तिथि( Fees Submitted Date)",
                    "value" => Carbon::createFromFormat('Y-m-d H:i:s', @$suppfees->submitted)->format('d-m-Y H:i A')
                ),
            );
        }

        $documents = $this->getSupplementaryStudentRequriedDocuments($student_id, $master->course);

        if (@$exam_year) {

        } else {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
        }
        $supplementaryDetails = Supplementary::with('SupplementarySubject')
            ->where('student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->first();

        //public/supplementary_documents/year/month/student_id
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        if (@$documents) {
            foreach (@$documents as $k => $v) {
                $tempFieldName = $fieldname;
                if ($k == 'marksheet_doc') {
                    $tempFieldName = $fieldname;
                }
                if ($k == 'sec_marksheet_doc') {
                    $tempFieldName = $sec_fieldname;
                }

                $output['documentDetails'][$k] = array(
                    "fld" => $k,
                    "label" => $v,
                    "value" => "<a title='Click here to verify " . @$v . "' target='_blank' href=" . url('public' . "/" . $type . "/" . $current_folder_year[$exam_year] . "/" . $exam_month . "/" . @$studentDocumentPath . "/" . $tempFieldName . "/") . "><span class='material-icons'>link</span></a>"
                    // "value" => "<a target='_blank' title='Click here to verify " . @$v ."' href=". url('public/'.$studentDocumentPath . "/" . @$supplementaryDetails->$k . "/" . ""   ). "><span class='material-icons'>link</span></a>"
                );
            }
        }


        $verificationDocuments = DB::table('supplementary_verification_documents')->where('supplementary_id', $supplementaryDetails->id)->orderBy('id', 'DESC')->first(['id', 'supp_doc', 'sec_marksheet_doc']);
        $combo_name = 'student_supplementary_document_path';
        $student_document_path = $this->master_details($combo_name);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $current_folder_year = $this->getCurrentYearFolderName();
        $studentDocumentPath = $student_document_path[2] . $current_folder_year . '/' . $exam_month . '/' . $student_id . '/';

        //$path = public_path("Supplementarypdf/" . $masterExamYear."/". $masterExamMonth. "/".$masterCourse ."/");

        $output['verificationDocumentDetails'] = array();
        if ($master->course == 12) {
            if (@$verificationDocuments->sec_marksheet_doc) {
                $output['verificationDocumentDetails']['sec_marksheet_doc'] = array(
                    "fld" => 'sec_marksheet_doc',
                    "label" => 'Clarification for 10th Passed Marksheet Document',
                    "value" => "<a title='Click here to verify document' target='_blank' href=" . url('public' . "/" . $studentDocumentPath . @$verificationDocuments->sec_marksheet_doc) . "><span class='material-icons'>link</span></a>"
                );
            }
        }

        if (@$verificationDocuments->supp_doc) {
            $output['verificationDocumentDetails']['supp_doc'] = array(
                "fld" => 'supp_doc',
                "label" => 'Clarification for Last Fail Marksheet Document',
                "value" => "<a title='Click here to verify document' target='_blank' href=" . url('public' . "/" . $studentDocumentPath . @$verificationDocuments->supp_doc) . "><span class='material-icons'>link</span></a>"
            );
        }


        $output = array(
            "verificationDocumentDetails" => array(
                "seciontLabel" => "सत्यापन दस्तावेज़ विवरण (Verification Document Details )",
                "data" => @$output['verificationDocumentDetails']
            ),
            "documentDetails" => array(
                "seciontLabel" => "मूल दस्तावेज़ विवरण ( Original Document Details )",
                "data" => @$output['documentDetails']
            ),
            "personalDetails" => array(
                "seciontLabel" => "व्यक्तिगत विवरण  (Personal Details)",
                "data" => @$output['personalDetails']
            ),
            "compulsorySubjectDetails" => array(
                "seciontLabel" => "अनिवार्य विषय विवरण  (Compulsory Subjects Details)",
                "data" => @$output['compulsorySubjectDetails']
            ),
            "additionalSubjectDetails" => array(
                "seciontLabel" => "अतिरिक्त विषय विवरण  (Additional Subject Details)",
                "data" => @$output['additionalSubjectDetails']
            ),
            "FeesDetails" => array(
                "seciontLabel" => "शुल्क विवरण  (Fees Details)",
                "data" => @$output['FeesDetails']
            ),
            "LockSubmttedDetails" => array(
                "seciontLabel" => "लॉक और सबमिट किए गए विवरण  ( Lock & Submitted  Details )",
                "data" => @$output['LockSubmttedDetails']
            ),
            "TransactionDetails" => array(
                "seciontLabel" => "लेनदेन का विवरण ( Transaction Details )",
                "data" => @$output['TransactionDetails']
            ),

        );

        if (Session::get('role_id') == Config::get('global.examination_department') || Session::get('role_id') == Config::get('global.aicenter_id')) {

            if (Session::get('role_id') == Config::get('global.aicenter_id')) {
                if ($supplementaries->is_aicenter_verify == 4) {
                    //unset($output['documentDetails']);
                } else {
                    //unset($output['verificationDocumentDetails']);
                }
            }
            if (Session::get('role_id') == Config::get('global.examination_department')) {
                if ($supplementaries->is_department_verify == 4) {
                    //unset($output['documentDetails']);
                } else {
                    //unset($output['verificationDocumentDetails']);
                }
            }
        } else {
            if ($supplementaries->is_aicenter_verify == 4 || $supplementaries->is_department_verify == 4) {
                unset($output['documentDetails']);
            } else {
                unset($output['verificationDocumentDetails']);
            }
        }


        return $output;
    }

    public function getCurrentYearFolderName()
    {
        $exam_years = CustomHelper::_get_selected_sessions();
        if (@$exam_years) {

        } else {
            $exam_years = Config::get('global.form_supp_current_admission_session_id');
        }
        $combo_name = 'current_folder_year';
        $current_folder_years = $this->master_details($combo_name);
        return $current_folder_year = @$current_folder_years[@$exam_years];
    }

    public function rsos_years()
    {
        $result = array();
        $mainTable = "rsos_years";
        $cacheName = "rsos_years";
        Cache::forget($mainTable);
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($mainTable) {
                $result = DB::table($mainTable)
                    ->orderBy('yearstext', 'asc')
                    ->where('status', 2)
                    ->pluck('yearstext', 'id');
                return $result;
            });
        }
        return $result;
    }

    public function _checkchangerequestssupplementariesAllowOrNotAllow()
    {
        $objController = new Controller();
        $combo_name = 'change_request_supplementaries_start_date';
        $combo_name2 = 'change_request_supplementaries_end_date';
        $change_request_supplementaries_start_date_arr = $objController->master_details($combo_name);
        $change_request_supplementaries_end_date = $objController->master_details($combo_name2);
        if (strtotime(date("Y-m-d H:i:s")) >= strtotime($change_request_supplementaries_start_date_arr[1]) && strtotime(date("Y-m-d H:i:s")) <= strtotime($change_request_supplementaries_end_date[1])) {
            $isValid = true;
        } else {
            $isValid = false;
        }
        return $isValid;
    }

    public function getSupplementaryStudentRequriedDocuments($student_id = null, $course = null)
    {
        if ($course == 12) {
            $documentInput["sec_marksheet_doc"] = "10th Passed Marksheet Document";
        }
        $documentInput["marksheet_doc"] = "Last Fail Marksheet Document";

        return $documentInput;
    }

    public function getBanksMaster($status = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($status)) {
            $condtions['status'] = $status;
        }
        $mainTable = "bank_masters";
        $cacheName = $mainTable . "_" . $status;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {

                $result = DB::table($mainTable)->where($condtions)->limit('100')->orderBy('sort')->get()->pluck('name', 'name');

                return $result;
            });
        }
        return $result;
    }

    public function _getRoles($status = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($status)) {
            $condtions['status'] = $status;
        }
        $mainTable = "roles";
        $cacheName = $mainTable . "_" . $status;
        Cache::forget($cacheName);

        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {

                $result = DB::table($mainTable)->where($condtions)->orderBy('sort')->get()->pluck('name', 'id');

                return $result;
            });
        }
        return $result;
    }

    public function _zipAndDownload($folder_path, $zip_file_name)
    {
        $zip = new ZipArchive();
        $zip->open($zip_file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if (is_dir($folder_path)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder_path));

            foreach ($files as $name => $file) {
                // We're skipping all subfolders
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();

                    // extracting filename with substr/strlen
                    $relativePath = substr($filePath, strlen($folder_path) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        } else {

        }
        return $zip_file_name;
    }


    public function _paymentencrypt($toBeEncryptString, $encryption_key)
    {
        $key = hash("sha256", $encryption_key);
        $iv = md5($encryption_key);
        $method = 'AES-256-CBC';
        return openssl_encrypt($toBeEncryptString, $method, hex2Bin($key), 0, hex2Bin($iv));
    }

    public function _paymentdecrypt($toBeDecryptString, $encryption_key)
    {
        $key = hash("sha256", $encryption_key);
        $iv = md5($encryption_key);
        $method = 'AES-256-CBC';
        return openssl_decrypt($toBeDecryptString, $method, hex2Bin($key), 0, hex2Bin($iv));
    }

    public function _paymentgetresponse($string = null)
    {
        $result = array();
        if (!empty($string)) {
            $asArr = explode('::', $string);
            foreach ($asArr as $val) {
                $tmp = explode('=', $val);
                $result[$tmp[0]] = str_replace($tmp[0] . '=', '', $val);
            }
        }
        return $result;
    }


    public function _sendSupplementaryLockSubmittedMessage($student_id)
    {
        $student = Supplementary::
        leftJoin('students', 'students.id', '=', 'supplementaries.student_id')
            ->leftJoin('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
            ->where('supplementaries.student_id', "=", $student_id)
            ->first(['supplementaries.*', 'students.mobile', 'students.enrollment', 'supp_student_fees.total']);

        $fld = "enrollment";
        $$fld = @$student->$fld;
        $fld = "mobile";
        $$fld = @$student->$fld;
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $application_fee = 0;
        if (isset($student->total) && $student->total > 0) {
            $application_fee = $student->total;
        }
        $sms = null;
        if ($application_fee > 0) {
            $sms = 'Dear Applicant, Your supplementary application has been registered successfully with enrollment number ' . $enrollment . '. Please pay fees Rs.' . $application_fee . ' by clicking on ' . $paymentUrl . ' to complete your supplementary application.-RSOS,GoR';
        }

        if (@$mobile) {
            return $this->_sendSMS($mobile, $sms);
        }

        return false;

    }

    public function _sendPaymentSubmitMessage($student_id)
    {
        $student = Supplementary::
        leftJoin('students', 'students.id', '=', 'supplementaries.student_id')
            ->leftJoin('supp_student_fees', 'supp_student_fees.student_id', '=', 'supplementaries.student_id')
            ->where('supplementaries.student_id', "=", $student_id)
            ->get(['supplementaries.*', 'students.mobile', 'supp_student_fees.total'])
            ->first();

        $fld = "enrollment";
        $$fld = $student->$fld;
        $fld = "mobile";
        $$fld = $student->$fld;
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $application_fee = 0;
        if (isset($student->total) && $student->total > 0) {
            $application_fee = $student->total;
        }
        $sms = null;
        if ($application_fee > 0) {
            $sms = 'Dear Applicant, Your application has been registered successfully with enrollment number ' . $enrollment . '.-RSOS,GoR';
        }
        if (@$mobile) {
            return $this->_sendSMS($mobile, $sms);
        }

        return false;

    }

    public function _sendRevalPaymentSubmitMessage($reval_id = null)
    {
        $student = RevalStudent::
        leftJoin('students', 'students.id', '=', 'reval_students.student_id')
            ->where('reval_students.id', $reval_id)
            ->first(['reval_students.*', 'students.mobile', 'students.enrollment', 'reval_students.total_fees']);

        $fld = "enrollment";
        $$fld = @$student->$fld;
        $fld = "mobile";
        $$fld = @$student->$fld;
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $application_fee = 0;
        if (isset($student->total) && $student->total > 0) {
            $application_fee = $student->total;
        }
        $sms = null;
        if ($application_fee > 0) {
            $sms = 'Dear Applicant, Your application has been registered successfully with enrollment number ' . $enrollment . '.-RSOS,GoR';
        }
        if (@$mobile) {
            return $this->_sendSMS($mobile, $sms);
        }

        return false;

    }


    public function _sendSSOMappMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        $student = Student::with('application', 'studentfee')
            ->where('students.id', $student_id)
            ->whereNull('students.ssoid')
            ->first();
        $fld = "enrollment";
        $$fld = @$student->$fld;
        $fld = "mobile";
        $$fld = @$student->$fld;
        // $paymentUrl = route('admission_fee_payment');
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $application_fee = 0;
        if (isset($student->ssoid) && !empty($student->ssoid)) {
            return false;
        }
        $sms = null;
        $studentName = @$student->name;
        $enrollment = @$student->enrollment;
        $date = "28/01/2024";
        if (@$enrollment && @$mobile) {
            $sms = "प्रिय " . $studentName . " नामांकन संख्या " . $enrollment . " आपके RSOS आवेदन को SSOID से मैप (जोड़े) करने के लिए rsosadmission.rajasthan.gov.in पर जाकर प्रक्रिया अनुसार मैप (जोड़े) करें अथवा अपने संदर्भ केन्द्र पर जाकर दिनांक " . $date . " तक अनिवार्य रुप से मैप (जोड़े) कराएं अन्यथा आवेदन निरस्त हो सकता है| - RSOS,GoR";
            $templateID = "1107170505696985578";
            return $this->_sendSMS($mobile, $sms, $templateID);
        } else {
            return false;
        }
    }

    public function _sendEnrollmentGeneratedSubmittedMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        $student = Student::with('application', 'studentfee')
            ->where('students.id', $student_id)
            ->first();

        $fld = "enrollment";
        $$fld = $student->$fld;
        $fld = "mobile";
        $$fld = $student->$fld;
        // $paymentUrl = route('admission_fee_payment');
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $application_fee = 0;
        if (isset($student->studentfee->total) && $student->studentfee->total > 0) {
            $application_fee = $student->studentfee->total;
        }
        $sms = null;
        // if($application_fee > 0){
        $sms = 'Dear Applicant, Your application has been registered successfully with enrollment number ' . $enrollment . '.-RSOS,GoR';
        // }
        // echo $sms;die;

        return $this->_sendSMS($mobile, $sms);
    }

    public function checkIsAlreadyPresentCombination($studentarray = null)
    {
        $countAlreadyPresentDetails = Student::join('applications', 'applications.student_id', '=', 'students.id')
            ->where("students.ai_code", @$studentarray['ai_code'])
            ->where('students.course', @$studentarray['course'])
            ->where("students.name", @$studentarray['name'])
            ->where("students.father_name", @$studentarray['father_name'])
            ->where("students.dob", @$studentarray['dob'])
            ->whereNotNull("applications.id")
            ->where("students.gender_id", @$studentarray['gender_id'])
            ->where("students.adm_type", @$studentarray['adm_type'])
            ->where("students.exam_year", @$studentarray['exam_year'])
            ->where("students.exam_month", @$studentarray['exam_month'])
            ->count();
        return $countAlreadyPresentDetails;
    }

    public function checkIsAlreadyPresentForOutOfRajasthanCombination($studentarray = null)
    {
        $countAlreadyPresentDetails = Student::join('applications', 'applications.student_id', '=', 'students.id')
            ->where("students.ai_code", @$studentarray['ai_code'])
            ->where('students.course', @$studentarray['course'])
            ->whereNotNull("applications.id")
            ->where("students.adm_type", @$studentarray['adm_type'])
            ->where("students.exam_year", @$studentarray['exam_year'])
            ->where("students.exam_month", @$studentarray['exam_month'])
            ->count();
        return $countAlreadyPresentDetails;
    }

    public function _samplesendSMS($mobile, $sms)
    {
        $curl = curl_init();
        $client_id = 'e6bc53b8-14c4-4501-b8f7-1abd83faf77e';
        $CURLOPT_POSTFIELDS = array();
        $CURLOPT_POSTFIELDS['UniqueID'] = 'HGHTCH_EDU_SMS';
        $CURLOPT_POSTFIELDS['username'] = 'HighEduSms';
        $CURLOPT_POSTFIELDS['password'] = 'Ed#MsmDt_0o1';
        $CURLOPT_POSTFIELDS['serviceName'] = 'eSanchar Send SMS Request';
        $CURLOPT_POSTFIELDS['language'] = 'ENG';
        $CURLOPT_POSTFIELDS['message'] = $sms;
        $CURLOPT_POSTFIELDS['mobileNo'] = array();
        $CURLOPT_POSTFIELDS['mobileNo'][] = $mobile;

        //echo json_encode($CURLOPT_POSTFIELDS); exit;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sewadwaar.rajasthan.gov.in/app/live/eSanchar/Prod/api/OBD/CreateSMS/Request?client_id=$client_id",
            //CURLOPT_URL => "https://api.sewadwaar.rajasthan.gov.in/app/live/eSanchar/Prod/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($CURLOPT_POSTFIELDS),
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json",
                "username: HighEduSms",
                "password: Ed#MsmDt_0o1"
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);


        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // "cURL Error #:" . $err;
        } else {
            // $response;
        }
        // }
        return $response;

    }

    public function _CallSSO($url, $data = false, $method = '')
    {
        $curl = curl_init();
        $url = $url;

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl);
        curl_error($curl);
        if ($result === false) {
            echo 'Curl error: ' . curl_error($curl);
            die;
        } else {
            //echo 'Operation completed without any errors';
        }

        curl_close($curl);

        return $result;
    }

    public function getStudentCountAiCodeWise($formId = null, $ai_code = null)
    {
        $conditions = Session::get($formId . '_conditions');
        $count = 0;
        $count = Student::
        where($conditions)
            ->where('students.ai_code', $ai_code)
            ->count();
        return $count;
    }

    public function getStudentLockAndSubmitCountAiCodeWise($formId = null, $ai_code = null)
    {
        $conditions = Session::get($formId . '_conditions');
        $count = 0;
        $count = Student::Join('applications', 'applications.student_id', '=', 'students.id')
            ->where($conditions)
            ->where('students.ai_code', $ai_code)
            ->where('applications.locksumbitted', "=", 1)
            ->count();
        return $count;
    }

    public function getStudentNotLockAndSubmitCountAiCodeWise($total = 0, $locked = 0)
    {
        return $total - $locked;
    }

    public function getStudentFeePaidCountAiCodeWise($formId = null, $ai_code = null)
    {
        $conditions = Session::get($formId . '_conditions');
        $count = 0;
        $count = Student::
        where($conditions)
            ->where('students.ai_code', $ai_code)
            ->where('students.submitted', "!=", "")
            ->count();
        return $count;
    }

    public function _getorgStudentDetailedFee($student_id = null)
    {
        $studentdata = Student::with('application')->where('id', $student_id)->first();
        $condtions = null;
        $result = array();
        $mainTable = "fee_structures";

        $fld = "adm_type";
        $$fld = $studentdata->$fld;
        $fld = "course";
        $$fld = $studentdata->$fld;
        $fld = "gender_id";
        $$fld = $studentdata->$fld;
        $fld = "category_a";
        $$fld = '';
        $fld = "is_wdp_wpp";
        $$fld = 0;
        $fld = "disability";
        $$fld = 0;

        $cacheName = "StudentDetailedFeeMaster_" . $adm_type . "_ " . $course . "_  " . $gender_id . "_ " . $disability . "_ " . $is_wdp_wpp;
        if ($studentdata->application->category_a == 7) {
            $fld = "is_jail_inmates";
            $$fld = 1;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            if ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
                if ($studentdata->are_you_from_rajasthan == 2) {
                    $fld = "is_out_of_rajasthan";
                    $$fld = 2;
                    $fld = "is_out_of_rajasthan";
                    if (!empty($fld)) {
                        $condtions[$fld] = $$fld;
                    }
                }
            }
        } elseif ($studentdata->adm_type == 5 || $studentdata->adm_type == 2 || $studentdata->adm_type == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }

        } elseif ($studentdata->adm_type == 1 && $studentdata->application->category_a == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && $studentdata->application->category_a == 2) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && $studentdata->application->category_a == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && $studentdata->application->category_a == 3) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && $studentdata->application->category_a == 6) {
            if ($studentdata->application->category_a == 6) {
                $fld = "is_wdp_wpp";
                $$fld = 1;
            } else {
                $fld = "is_wdp_wpp";
                $$fld = 0;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && $studentdata->application->category_a == 6) {
            if ($studentdata->application->category_a == 6) {
                $fld = "is_wdp_wpp";
                $$fld = 1;
            } else {
                $fld = "is_wdp_wpp";
                $$fld = 0;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 4 && $studentdata->application->disability == 10) {
            if ($studentdata->application->disability == 10) {
                $fld = "disability";
                $$fld = 0;
            } else {
                $fld = "disability";
                $$fld = 1;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 && $studentdata->application->disability == 10) {
            if ($studentdata->application->disability == 10) {
                $fld = "disability";
                $$fld = 0;
            } else {
                $fld = "disability";
                $$fld = 1;
            }
            $fld = "is_jail_inmates";
            $$fld = 0;
            $fld = "is_jail_inmates";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        } elseif ($studentdata->adm_type == 1 || $studentdata->adm_type == 4) {
            $fld = "is_jail_inmates";
            $$fld = 0;
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "adm_type";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "course";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "gender_id";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "disability";
            $$fld = 1;
            $fld = "disability";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
            $fld = "is_wdp_wpp";
            $$fld = 0;
            $fld = "is_wdp_wpp";
            if (!empty($fld)) {
                $condtions[$fld] = $$fld;
            }
        }

        if ($studentdata->are_you_from_rajasthan == 2 && $studentdata->gender_id == 2) {
            $result = DB::table('fee_structures')->where($condtions)->first();
        } else {
            if ($studentdata->adm_type == 1 && $studentdata->application->category_a == 2) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 4 && $studentdata->application->category_a == 2) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 1 && $studentdata->application->category_a == 3) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else if ($studentdata->adm_type == 4 && $studentdata->application->category_a == 3) {
                $result = DB::table('fee_structures')->whereRaw("category_a like '%" . $studentdata->application->category_a . "%'")->where($condtions)->first();
            } else {
                $result = DB::table('fee_structures')->where($condtions)->first();
            }
        }
        return $result;
    }

    public function _getorgFeeDetailsForDispaly($studentorgDetailedFees = null, $student_id = null)
    {
        $result = array();
        $orgvariableFees = 0;
        $baseFees = ((@$studentorgDetailedFees->pay_registration_fees) +
            (@$studentorgDetailedFees->pay_forward_fees) +
            (@$studentorgDetailedFees->pay_online_services_fees));
        $orgbaseFees = ((@$studentorgDetailedFees->org_registration_fees) +
            (@$studentorgDetailedFees->org_forward_fees) +
            (@$studentorgDetailedFees->org_online_services_fees));
        $orgtocSubjects = TocMark::where("student_id", $student_id)->count();
        $orgstudentpracticaltypesubject = ExamSubject::join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
            ->where('subjects.practical_type', 1)->where('exam_subjects.student_id', $student_id)
            ->count();
        $orgpartadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
        $orgaddSubjectCount = ExamSubject::where("student_id", $student_id)->where("is_additional", 1)->count();

        $fld = 'org_add_sub_fees';
        if (@$studentorgDetailedFees->$fld) {
            $$fld = (@$studentorgDetailedFees->$fld * @$orgaddSubjectCount);
            $orgvariableFees = @$orgvariableFees + (@$studentorgDetailedFees->$fld * @$orgaddSubjectCount);
        } else {
            $$fld = (@$studentDetailedFees->$fld);
            $orgvariableFees = $orgvariableFees + (@$studentorgDetailedFees->$fld);
        }
        $fld = 'org_toc_fees';
        if (@$studentorgDetailedFees->$fld) {
            $$fld = (@$studentorgDetailedFees->$fld * @$orgtocSubjects);
            $orgvariableFees = @$orgvariableFees + (@$studentorgDetailedFees->$fld * @$orgtocSubjects);
        } else {
            $$fld = (@$studentDetailedFees->$fld);
            $orgvariableFees = $orgvariableFees + (@$studentorgDetailedFees->$fld);
        }
        $fld = 'org_practical_fees';
        if (@$studentorgDetailedFees->$fld) {
            $$fld = (@$studentorgDetailedFees->$fld * @$orgstudentpracticaltypesubject);
            $orgvariableFees = @$orgvariableFees + (@$studentorgDetailedFees->$fld * @$orgstudentpracticaltypesubject);
        } else {
            $$fld = (@$studentDetailedFees->$fld);
            $orgvariableFees = $orgvariableFees + (@$studentorgDetailedFees->$fld);
        }
        $fld = 'org_exam_fees';
        if (@$studentorgDetailedFees->$fld) {
            $$fld = (@$studentorgDetailedFees->$fld * @$orgpartadmintionstudent);
            $orgvariableFees = @$orgvariableFees + (@$studentorgDetailedFees->$fld * @$orgpartadmintionstudent);
        } else {
            $$fld = (@$studentDetailedFees->$fld);
            $orgvariableFees = $orgvariableFees + (@$studentorgDetailedFees->$fld);
        }


        $studentdata = Student::with('application')->where('id', $student_id)->first();
        if ($studentdata->adm_type == 4) {
            $tocSubjects = 0;
        } else {
            $tocSubjects = TocMark::where("student_id", $student_id)->count();
        }

        $studentpracticaltypesubject = ExamSubject::join('subjects', 'subjects.id', '=', 'exam_subjects.subject_id')
            ->where('subjects.practical_type', 1)->where('exam_subjects.student_id', $student_id)
            ->count();
        if ($studentdata->adm_type == 4) {
            $addSubjectCount = 0;
        } else {
            $addSubjectCount = ExamSubject::where("student_id", $student_id)->where("is_additional", 1)->count();
        }
        //addtional subject from admission subject table
        $tocSubjectCount = $tocSubjects;
        $readmExamFeeCount = 0;
        $variableFees = 0;
        if ($studentdata->adm_type == 3) {
            $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
        } elseif ($studentdata->adm_type == 2) {
            $partadmintionstudent = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
            $tocSubjects = TocMark::where("student_id", $student_id)->count();
            $partadmintionstudent1 = $partadmintionstudent;
        } elseif ($studentdata->adm_type == 2 && tocSubjects == 0) {
            $partadmintionstudent1 = ExamSubject::where('student_id', $student_id)->where('is_additional', 0)->count();
        }
        $practicalSubjectCount = $studentpracticaltypesubject;
        $finalFees = 0;
        $fld = 'pay_add_sub_fees';
        if (@$studentorgDetailedFees->$fld) {
            $$fld = (@$studentorgDetailedFees->$fld * @$addSubjectCount);
            $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld * @$addSubjectCount);
        } else {
            $$fld = (@$studentDetailedFees->$fld);
            $variableFees = $variableFees + (@$studentorgDetailedFees->$fld);
        }

        $fld = 'pay_toc_fees';
        if (@$studentorgDetailedFees->$fld) {
            $$fld = (@$studentorgDetailedFees->$fld * @$tocSubjectCount);
            $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld * @$tocSubjectCount);
        } else {
            $$fld = (@$studentDetailedFees->$fld);
            $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld);
        }

        $fld = 'pay_practical_fees';
        if (@$studentorgDetailedFees->$fld) {
            $$fld = (@$studentorgDetailedFees->$fld * @$practicalSubjectCount);
            $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld * @$practicalSubjectCount);
        } else {
            $$fld = (@$studentDetailedFees->$fld);
            $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld);
        }
        if ($studentdata->adm_type == 5 || $studentdata->adm_type == 1) {
            $fld = 'pay_exam_fees';
            if (@$studentorgDetailedFees->$fld) {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld);
            } else {
                $$fld = (@$studentorgDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld);
            }
        } elseif ($studentdata->adm_type == 3) {
            $fld = 'pay_exam_fees';
            if (@$studentorgDetailedFees->$fld) {
                $$fld = (@$studentorgDetailedFees->$fld * $partadmintionstudent);
                $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld * $partadmintionstudent);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld);
            }
        } elseif ($studentdata->adm_type == 2) {
            $fld = 'pay_exam_fees';
            if (@$studentorgDetailedFees->$fld) {
                $$fld = (@$studentorgDetailedFees->$fld * $partadmintionstudent1);
                $variableFees = @$variableFees + (@$studentorgDetailedFees->$fld * $partadmintionstudent1);
            } else {
                $$fld = (@$studentDetailedFees->$fld);
                $variableFees = @$variableFees + (@$studentDetailedFees->$fld);
            }
        }
        $studentDetails = Student::where('id', $student_id)->first();
        if ($studentdata->application->category_a == 7) {
            $lateFees = 0;
        } else {
            $lateFees = $this->_getLateFeeAmount(@$studentDetails->stream, @$studentDetails->gender_id);
        }


        $finalFees = (float)@$baseFees + (float)@$variableFees + (float)@$lateFees;

        $orgfinalFees = (float)@$orgbaseFees + (float)@$orgvariableFees + (float)@$lateFees;

        $result['registration_fees'] = @$studentorgDetailedFees->pay_registration_fees;
        $result['forward_fees'] = @$studentorgDetailedFees->pay_forward_fees;
        $result['online_services_fees'] = @$studentorgDetailedFees->pay_online_services_fees;
        $result['add_sub_fees'] = @$pay_add_sub_fees;
        $result['toc_fees'] = @$pay_toc_fees;
        $result['practical_fees'] = @$pay_practical_fees;
        $result['readm_exam_fees'] = @$pay_exam_fees;
        $result['baseFee'] = @$baseFees;
        $result['variableFees'] = @$variableFees;
        $result['late_fees'] = @$lateFees;
        $result['final_fees'] = @$finalFees;
        $result['org_registration_fees'] = @$studentorgDetailedFees->org_registration_fees;
        $result['org_forward_fees'] = @$studentorgDetailedFees->pay_forward_fees;
        $result['org_online_services_fees'] = @$studentorgDetailedFees->pay_online_services_fees;
        $result['org_add_sub_fees'] = @$org_add_sub_fees;
        $result['org_toc_fees'] = @$org_toc_fees;
        $result['org_practical_fees'] = @$org_practical_fees;
        $result['org_readm_exam_fees'] = @$org_exam_fees;
        $result['orgbaseFees'] = @$baseFees;
        $result['orgvariableFees'] = @$variableFees;
        $result['orglate_fees'] = @$lateFees;
        $result['orgfinalFees'] = @$orgfinalFees;
        return $result;
    }

    public function getSubjectDetailForHallTicketSupp($student_id = null, $type = null)
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        if (empty($exam_year)) {
            $exam_year = Config::get('global.student_admit_card_download_exam_year');
        }
        $exam_month = Config::get('global.defaultStreamId');

        $subjectCodes = SupplementarySubject::where("student_id", $student_id)->where("exam_year", $exam_year)->where("exam_month", $exam_month)->pluck('subject_id', 'subject_id');
        $list = $this->getSubjectCodeWithDetailByEnrollmentSupp($subjectCodes, $type);
        return $list;
    }

    public function getSubjectCodeWithDetailByEnrollmentSupp($subjectCodes = null, $type = null)
    {

        $exam_year = CustomHelper::_get_selected_sessions();
        if (empty($exam_year)) {
            $exam_year = Config::get('global.student_admit_card_download_exam_year');
        }
        $stream = Config::get("global.defaultStreamId");

        if ($type == 'api') {
            $exam_year = Config::get('global.student_admit_card_download_exam_year');
            $stream = Config::get('global.student_admit_card_download_exam_month');
        }
        $data = Subject::select(
            'subjects.name', 'subjects.practical_type', 'subjects.course', 'subjects.id', 'subjects.subject_code',
            'timetables.exam_date', 'timetables.exam_time_start', 'timetables.exam_time_end', 'practicaltimetables.exam_date_start', 'practicaltimetables.exam_date_end'
        )
            ->leftJoin("timetables", function ($join) use ($exam_year, $stream) {
                $join->on("timetables.subjects", "=", "subjects.id");
                $join->whereRaw("(rs_timetables.exam_year = " . $exam_year . " )");
                $join->whereRaw("(rs_timetables.stream = " . $stream . " )");
            })
            ->leftJoin("practicaltimetables", function ($join) use ($exam_year, $stream) {
                $join->on("practicaltimetables.subjects", "=", "subjects.id");
                $join->whereRaw("(rs_practicaltimetables.exam_year = " . $exam_year . " )");
                $join->whereRaw("(rs_practicaltimetables.stream = " . $stream . " )");
            })
            ->whereIn('subjects.id', $subjectCodes)
            ->orderByRaw("rs_timetables.exam_date, rs_practicaltimetables.exam_date_start")
            ->get();
        $list = array();

        if (!empty($data)) {

            foreach ($data as $key => $subject) {

                $paperType = 'T';
                $list[$paperType][$subject->subject_code]['id'] = $subject->id;
                $list[$paperType][$subject->subject_code]['name'] = $subject->name;
                $list[$paperType][$subject->subject_code]['exam_date'] = $subject->exam_date;
                $list[$paperType][$subject->subject_code]['exam_time_start'] = $subject->exam_time_start;
                $list[$paperType][$subject->subject_code]['exam_time_end'] = $subject->exam_time_end;
                if ($subject->practical_type == 1) {
                    $paperType = 'P';
                    $list[$paperType][$subject->subject_code]['id'] = $subject->id;
                    $list[$paperType][$subject->subject_code]['name'] = $subject->name;
                    $list[$paperType][$subject->subject_code]['exam_date_start'] = $subject->exam_date_start;
                    $list[$paperType][$subject->subject_code]['exam_date_end'] = $subject->exam_date_end;
                }
            }
        }


        return $list;
    }

    //

    public function getSubjectDetailForHallTicket($student_id = null, $type = null)
    {

        $subjectCodes = ExamSubject::where("student_id", $student_id)->pluck('subject_id', 'subject_id');

        $list = $this->getSubjectCodeWithDetailByEnrollmentSupp($subjectCodes, $type);
        return $list;
    }

    public function getAllSuppSubject($student_id = null)
    {
        $all_supp_exam_subject_arr = array();
        $all_supp_exam_subject_data = ExamSubject::where('student_id', $student_id)->get()->toArray();
        if (isset($all_supp_exam_subject_data) && !empty($all_supp_exam_subject_data)) {
            foreach ($all_supp_exam_subject_data as $all_supp_exam_subject) {
                $all_supp_exam_subject_arr[] = $all_supp_exam_subject['subject_id'];
            }
        }
        return $all_supp_exam_subject_arr;
    }

    public function districtNameById($district_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($district_id)) {
            $condtions = ['id' => $district_id];
        }
        $mainTable = "districts";
        $cacheName = "districts";

        return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name', 'id');

        if (!empty($district_id)) {
            $cacheName = "districts_name_id_" . $district_id;
        }
        if (Cache::has($cacheName)) { //Cache::forget($mainTable);
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                return $result = DB::table($mainTable)->where($condtions)->orderBy('name', 'asc')->get()->pluck('name', 'id');
            });
        }
        return $result;
    }

    public function MappingExaminerValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;
        $users = new User; /// create model object
        $validators[] = Validator::make($request->all(), $users->mapping_examiner, $users->mapping_examiner_messages);

        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function AllotingCopiesExaminerValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $theory_custom_component_obj = new ThoeryCustomComponent;
        $alredy_exist_user_data = $theory_custom_component_obj->checkAllotingExaminer($request->marking_absent_student_id, $request->user_id);
        $key = 0;
        if (!empty($alredy_exist_user_data)) {
            $isValid = $response[$key]['isValid'] = false;
            $response[$key]['errors'] = "Selected combination already mapped with selected sso.";
            $response[$key]['validator'] = $validator;
        }

        // $allotingcopies = new AllotingCopiesExaminer; /// create model object
        // $validators[] = Validator::make($request->all(),$allotingcopies->rules,$allotingcopies->message);
        // foreach(@$validators as $key => $validator){
        // 	if ($validator->fails()) {
        // 		$response[$key]['isValid'] = false;
        // 		$response[$key]['errors'] = $validator->messages();
        // 		$response[$key]['validator'] = $validator;
        // 	}
        // }
        return $response;
    }

    public function getSubjectDetailForSupp($student_id = null)
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.supp_current_exam_month_id');
        $suppId = Supplementary::where("student_id", $student_id)->where("exam_year", $exam_year)->where("exam_month", $exam_month)->pluck('id', 'id');
        $subjectCodes = SupplementarySubject::where("supplementary_id", $suppId)->pluck('subject_id', 'subject_id');
        $list = $this->getSubjectCodeWithDetailByEnrollmentSupps($subjectCodes);
        return $list;
    }

    public function getSubjectCodeWithDetailByEnrollmentSupps($subjectCodes = null)
    {

        $exam_year = Config::get('global.current_admission_session_id');
        $stream = Config::get("global.defaultStreamId");

        $data = Subject::select(
            'subjects.name', 'subjects.course', 'subjects.id', 'subjects.subject_code')
            ->whereIn('subjects.id', $subjectCodes)
            ->get();

        $list = array();

        if (!empty($data)) {
            foreach ($data as $key => $subject) {
                // $paperType = 'T';
                $list[$subject->subject_code]['id'] = $subject->id;
                $list[$subject->subject_code]['name'] = $subject->name;
                // if($subject->practical_type == 1){
                // 	$paperType = 'P';
                // 	$list[$paperType][$subject->subject_code]['subject_type'] = 'P';
                // }
                // else{
                // 	$list[$paperType][$subject->subject_code]['subject_type'] = 'T';
                // }
            }
        }


        return $list;
    }

    public function markingAbsentStudentValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $allreadyExistData = null;
        $theory_Custom_component = new ThoeryCustomComponent;
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');

        if (@$request->examcenter_detail_id && @$request->course_id && @$request->subject_id) {
            $allreadyExistData = $theory_Custom_component->checkMarkingAbsent(@$request->examcenter_detail_id, @$request->course_id, @$request->subject_id, @$exam_year, $exam_month);
        }
        $key = 0;
        if (!empty($allreadyExistData)) {
            $isValid = $response[$key]['isValid'] = false;
            $response[$key]['errors'] = "Selected Marking Absent Combination already exists.";
            $response[$key]['validator'] = $validator;
        }


        return $response;
    }

    public function _checkFormAllowedOrNot($stream = null, $gender_id = 0)
    {
        $response = array();
        $status = false;
        $msg = now() . " Date & Time not allowed!";
        $info = null;
        if (@$stream) {
            $sql = "select * from rs_exam_late_fee_dates where stream = " . $stream;
            if ($gender_id > 0) {
                $sql .= "  and gender_id = " . $gender_id . "";
            }
            $sql .= " and NOW() BETWEEN from_date AND to_date ";
            $sql .= " limit 1;";

            $dates = DB::select($sql);

            if (@$dates[0]) {
                $status = true;
                $msg = now() . " Date allowed!";
                $info = @$dates[0];
            }
        }

        $whiteListIp = $this->_getWhiteListCheckAllow();
        if ($whiteListIp) {
            $status = true;
        }
        $response['status'] = $status;
        $response['msg'] = $msg;
        $response['info'] = $info;
        // dd($whiteListIp);
        return json_encode($response);
    }

    public function _getWhiteListCheckAllow()
    {
        $showStatus = false;
        $request_client_ip = Config::get('global.request_client_ip');
        $whiteListMasterIps = Config::get("global.whiteListMasterIps");
        // echo $request_client_ip;
        // echo "<br>";

        if (isset($request_client_ip) && !empty($request_client_ip) && isset($whiteListMasterIps) && !empty($whiteListMasterIps)) {
            if (in_array(@$request_client_ip, @$whiteListMasterIps)) {
                $showStatus = true;
            }
        }
        return $showStatus;
    }

    public function _isAllowMacAddress()
    {
        $ip = Config::get('global.CURRENT_IP');
        $allowedMacAddressList = Config::get('global.allowedMacAddressList');

        $currentMacAddress = shell_exec("arp -a " . $ip);
        $currentMacAddress = substr($currentMacAddress, 100, 26);
        $currentMacAddress = trim($currentMacAddress);
        $status = false;
        if (in_array($currentMacAddress, $allowedMacAddressList)) {
            $status = true;
        }
        return @$status;
    }

    public function _getAllowedStreams($gender_id = 0, $is_supplementary = null)
    {
        $response = array();
        $status = false;
        $msg = now() . " Date&Time not allowed!";
        $info = null;

        $sql = "select stream from rs_exam_late_fee_dates where ";
        if ($gender_id > 0) {
            $sql .= "  and gender_id = " . $gender_id . " and ";
        }
        $sql .= " NOW() BETWEEN from_date AND to_date ";
        if ($is_supplementary != null) {
            $sql .= " and  is_supplementary = 1 ";
        } else {
            $sql .= " and is_supplementary != 1  ";
        }
        $sql .= " group by stream;";


        $streams = DB::select($sql);

        if (@$streams[0]) {
            $status = true;
            $msg = now() . " allowed!";
            $info = @$streams;
        }

        $whiteListIp = $this->_getWhiteListCheckAllow();
        if ($whiteListIp) {
            $status = true;
            $tempsql = "select stream from rs_exam_late_fee_dates where ";
            if ($gender_id > 0) {
                $tempsql .= "  and gender_id = " . $gender_id . " and ";
            }
            //$tempsql .=  " NOW() BETWEEN from_date AND to_date ";
            if ($is_supplementary != null) {
                $tempsql .= "   is_supplementary = 1 ";
            } else {
                $tempsql .= "  is_supplementary != 1  ";
            }
            $tempsql .= " group by stream;";
            $msg = null;
            $info = DB::select($tempsql);
        }
		else {
	
            /*$combo_name = 'registration_paused_without_date_check';
            $registration_paused_without_date_check = $this->master_details($combo_name);
            //dd($registration_paused_without_date_check[1]);
            if (@$registration_paused_without_date_check[1] && $registration_paused_without_date_check[1] == 1) {
                $response = array();
                $status = false;
                $msg = now() . " Date&Time not allowed!";
                $info = null;
            }*/
        }
		
        $response['status'] = $status;
        $response['msg'] = $msg;
        $response['info'] = $info;
         //dd($response);
        return json_encode($response);
    }

    public function _checkPaymentAllowedOrNot($stream = null, $gender_id = 0)
    {
        $response = array();
        $status = false;
        $msg = now() . " Date & Time not allowed!";
        $info = null;
        if (@$stream) {
            $sql = "select * from rs_after_locked_exam_late_fee_dates where stream = " . $stream;
            if ($gender_id > 0) {
                $sql .= "  and gender_id = " . $gender_id . "";
            }
            $sql .= " and NOW() BETWEEN from_date AND to_date ";
            $sql .= " limit 1;";

            // @dd($sql);
            $dates = DB::select($sql);
            if (isset($dates[0]) && !empty($dates[0])) {
                $status = true;
                $msg = now() . " Date allowed!";
                $info = @$dates[0];
            }
        }

        $custom_component_obj = new CustomComponent();
        $isAdminStatus = $custom_component_obj->_checkIsAdminRole();
        if ($isAdminStatus) {
            $status = true;
        }

        // $status = true;
        $response['status'] = $status;
        $response['msg'] = $msg;
        $response['info'] = $info;
        // dd($response);
        return json_encode($response);
    }

    public function UpdatePastdataValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $pastdata = new Pastdata; /// create model object
        $validators[] = Validator::make($request->all(), $pastdata->updaterules, $pastdata->message);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function updateStudentDetailPrintValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $studentdata = new Student; /// create model object
        $validators[] = Validator::make($request->all(), $studentdata->updatedetailsprintrules, $studentdata->updatedetailsprintmessage);
        if ($request->course == '12') {
            $validators[] = Validator::make($request->all(), $studentdata->rulesapplicationandstudent12OutOfRajasthan);
        }


        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    // Student Updation logs Function

    public function _getCheckAllowSuppForm()
    {
        $showStatus = false;
        $combo_name = 'supp_form_allowed_ips';
        $supp_form_allowed_ips = $this->master_details($combo_name);
        if (isset($supp_form_allowed_ips[1]) && $supp_form_allowed_ips[1] != null && $supp_form_allowed_ips[1] != '' && !empty($supp_form_allowed_ips[1])) {
            $request_client_ip = Config::get('global.request_client_ip');
            $arr = $supp_form_allowed_ips->toArray();
            if (@$arr[1]) {
                $supp_form_allowed_ips = json_decode($arr[1], true);
            }
            if (in_array($request_client_ip, $supp_form_allowed_ips)) {
                $showStatus = true;
            }
        } else {
            $showStatus = true;
        }
        return $showStatus;
    }

    public function _getCheckAllowToCheckResult()
    {
        $showStatus = false;
        $combo_name = 'provisonal_result_allowed_ips';
        $provisonal_result_allowed_ips = $this->master_details($combo_name);
        if (isset($provisonal_result_allowed_ips[1]) && $provisonal_result_allowed_ips[1] != null && $provisonal_result_allowed_ips[1] != '' && !empty($provisonal_result_allowed_ips[1])) {
            $request_client_ip = Config::get('global.request_client_ip');
            $arr = $provisonal_result_allowed_ips->toArray();
            if (@$arr[1]) {
                $provisonal_result_allowed_ips = json_decode($arr[1], true);
            }
            if (in_array($request_client_ip, $provisonal_result_allowed_ips)) {
                $showStatus = true;
            }
        } else {
            $showStatus = true;
        }
        return $showStatus;
    }

    // Student Updation logs Function

    public function _getCheckAllowSessionaldata()
    {
        $showStatus = false;
        $combo_name = 'sessiona_result_allowed_ips';
        $provisonal_result_allowed_ips = $this->master_details($combo_name);
        if (isset($provisonal_result_allowed_ips[1]) && $provisonal_result_allowed_ips[1] != null && $provisonal_result_allowed_ips[1] != '' && !empty($provisonal_result_allowed_ips[1])) {
            $request_client_ip = Config::get('global.request_client_ip');
            $arr = $provisonal_result_allowed_ips->toArray();
            if (@$arr[1]) {
                $provisonal_result_allowed_ips = json_decode($arr[1], true);
            }
            if (in_array($request_client_ip, $provisonal_result_allowed_ips)) {
                $showStatus = true;
            }
        } else {
            $showStatus = true;
        }
        return $showStatus;
    }

    public function finalsresultupdatevalidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $exam_results = new ExamResult;; /// create model object
        $validators[] = Validator::make($request->all(), $exam_results->rules, $exam_results->message);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function studentSubjectsDataValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $exam_subjects = new ExamSubject; /// create model object
        $validators[] = Validator::make($request->all(), $exam_subjects->rules, $exam_subjects->message);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function studentaddsubjectValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $exam_subjects = new ExamSubject; /// create model object
        $validators[] = Validator::make($request->all(), $exam_subjects->addsubjectarules, $exam_subjects->addsubjectmessage);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function AicnterDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;
        if ($request->type == 1) {
            $AicenterDetail = new AicenterDetail; /// create model object
            $validators[] = Validator::make($request->all(), $AicenterDetail->uersmakerules, $AicenterDetail->useraicenterrulemessage);
        } elseif ($request->type == 2) {
            $AicenterDetail = new AicenterDetail; /// create model object
            $validators[] = Validator::make($request->all(), $AicenterDetail->uerseditmakerules, $AicenterDetail->useraicenterrulemessage);
        }
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function MyProfileAicnterDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;

        $AicenterDetail = new AicenterDetail; /// create model object
        $validators[] = Validator::make($request->all(), $AicenterDetail->myprofilemakerules, $AicenterDetail->useraicenterrulemessage);

        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function _getCheckAllowToCheckRevisedResult()
    {
        $showStatus = false;
        $combo_name = 'provisonal_revised_result_allowed_ips';
        $provisonal_result_allowed_ips = $this->master_details($combo_name);

        if (isset($provisonal_result_allowed_ips[1]) && $provisonal_result_allowed_ips[1] != null && $provisonal_result_allowed_ips[1] != '' && !empty($provisonal_result_allowed_ips[1])) {
            $request_client_ip = Config::get('global.request_client_ip');
            $arr = $provisonal_result_allowed_ips->toArray();
            if (@$arr[1]) {
                $provisonal_result_allowed_ips = json_decode($arr[1], true);
            }
            if (in_array($request_client_ip, $provisonal_result_allowed_ips)) {
                $showStatus = true;
            }
        } else {
            $showStatus = true;
        }
        return $showStatus;
    }

    public function _redirectForWhiteList()
    {
        $ip = config("global.request_client_ip");
        $whiteListMasterIps = Config::get("global.whiteListMasterIps");
        if (!in_array($ip, $whiteListMasterIps)) {
            Redirect::to('/')->send()->with('error', 'Failed! You are not allowed for supplementary form.');
        }
        return true;
    }

    public function _getSortingFields($filters = null)
    {
        $sortingField = null;
        $k = 0;
        if (isset($filters) && !empty($filters)) {
            foreach ($filters as $value) {
                $sortingField[$k]['fld'] = $value['fld'];
                $sortingField[$k]['dbtbl'] = $value['dbtbl'];
                $sortingField[$k]['field_same'] = $value['dbtbl'] . "_" . $value['fld'];
                $sortingField[$k]['placeholder'] = $value['placeholder'] . " Ascending";
                $sortingField[$k]['orderby'] = 'asc';
                $k++;
                $sortingField[$k]['fld'] = $value['fld'];
                $sortingField[$k]['dbtbl'] = $value['dbtbl'];
                $sortingField[$k]['placeholder'] = $value['placeholder'] . " Descending";
                $sortingField[$k]['field_same'] = $value['dbtbl'] . "_" . $value['fld'];
                $sortingField[$k]['orderby'] = 'desc';
                $k++;
            }
        }
        return $sortingField;
    }

    public function _setSortingArrayFields($inputValue = null, $sortingField = null)
    {
        $orderByRaw = null;
        $inputs['sorting'] = $inputValue;

        if (isset($inputs['sorting']) && !empty($inputs['sorting'])) {
            $totalCount = count($inputs['sorting']);
            $counterTemp = 0;
            foreach (@$inputs['sorting'] as $ok => $ov) {
                $counterTemp++;
                if (!empty($sortingField[$ov]['dbtbl']) && isset($sortingField[$ov]) && !empty($sortingField[$ov])) {
                    if ($counterTemp == $totalCount) {
                        $commna = " ";
                    } else {
                        $commna = ", ";
                    }
                    $orderByRaw = $orderByRaw . "rs_" . $sortingField[$ov]['dbtbl'] . "." . $sortingField[$ov]['fld'] . " " . $sortingField[$ov]['orderby'] . $commna;
                }
            }
        }
        return $orderByRaw;
    }

    public function roleandpermission()
    {
        $role_id = Session::get('role_id');
        $rolehaspermissions = DB::table('role_has_permissions')->where('role_id', $role_id)->pluck('permission_id');
        $permissions = DB::table('permissions')->whereIn('id', $rolehaspermissions)->pluck('name')->toarray();
        return $permissions;
    }

    public function _checkIsRouteExists($route_name = null)
    {
        if (!empty($route_name) && Route::has($route_name)) {
            return true;
        }
        return false;
    }

    public function queryDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $modelObj = new ReportMasterQuery;  /// create model object
        $inputs = $request->all();
        if ($inputs['is_sql'] == 1 && @$inputs['type'] == 3) {
            $validators[] = Validator::make($inputs, $modelObj->query, $modelObj->querymessage);
        } else if ($inputs['is_sql'] == 2 && @$inputs['type'] == 3) {
            $validators[] = Validator::make($inputs, $modelObj->querys, $modelObj->querymessage);
        } else if ($inputs['is_sql'] == 1 && @$inputs['type'] == 4) {
            $validators[] = Validator::make($inputs, $modelObj->querynotpermissions, $modelObj->querymessage);
        } else if ($inputs['is_sql'] == 2 && @$inputs['type'] == 4) {
            $validators[] = Validator::make($inputs, $modelObj->querysnotpermissions, $modelObj->querymessage);
        } else {
            $validators[] = Validator::make($inputs, $modelObj->queryss, $modelObj->querymessage);
        }
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $isValid = $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            } else {
                $inputs['permissions'] = "_sql_" . $inputs['permissions'];
                $isPermissionExists = $this->_checkIsPermissionExists($inputs['permissions']);

                if ($isPermissionExists) {
                    $fld = 'permissions';
                    $errMsg = 'Entered permission already taken.';
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $response[$key]['isValid'] = false;
                    $response[$key]['errors'] = $validator->messages();
                    $response[$key]['validator'] = $validator;
                }

                $isValidSqlResponse = $this->_checkIsValidSql($inputs['sql']);
                if (!$isValidSqlResponse['status'] && !empty($isValidSqlResponse['errors'])) {
                    $fld = 'sql';
                    $errMsg = 'SQL Error : ' . $isValidSqlResponse['errors'];
                    $validator->getMessageBag()->add($fld, $errMsg);
                    $response[$key]['isValid'] = false;
                    $response[$key]['errors'] = $validator->messages();
                    $response[$key]['validator'] = $validator;
                }
            }
        }
        return $response;
    }

    public function _checkIsPermissionExists($permissions = null)
    {
        $count = 1;
        if (!empty($permissions)) {
            $count = Permission::where('name', $permissions)->count();
        }


        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function _checkIsValidSql($query = null)
    {
        $status = false;
        $errors = null;
        if (!empty($query)) {
            // Get connection object and set the charset
            $host = Config::get('global.DB_HOST');
            $user = Config::get('global.DB_USERNAME');
            $pass = Config::get('global.DB_PASSWORD');
            $name = Config::get('global.DB_DATABASE');
            $conn = mysqli_connect($host, $user, $pass, $name);

            $result = mysqli_query($conn, $query);
            if (isset($result) && !empty($result)) {

            } else {
                $status = false;
                $errors = mysqli_error($conn);
            }

        }
        $response = array("status" => $status, 'errors' => $errors);
        return $response;
    }

    public function UpdatePastsubjectdataValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $pastdata = new Pastdata; /// create model object
        $validators[] = Validator::make($request->all(), $pastdata->final_result, $pastdata->message1);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function oldSetUpdatedRoleBasedSessionYear($role_id = null)
    {
        $form_session_changed = Config::get('global.form_session_changed');
        $practical_role = Config::get('global.practicalexaminer');
        $allowpeviousyear = Config::get('global.allowOnlyPreviousYears');
        if (in_array($role_id, $form_session_changed)) {
            $current_admission_session_id_for_year_selection = Config::get('global.form_current_admission_session_id');
        } else if (in_array($role_id, $allowpeviousyear)) {
            $current_admission_session_id_for_year_selection = config::get('global.previous_year');
        } else {
            $current_admission_session_id_for_year_selection = Config::get('global.form_current_admission_session_id');
        }

        Session::put("current_admission_sessions", $current_admission_session_id_for_year_selection);
        return true;
    }

    public function _AgainOldsetEnrollmentAndIsEligiable($student_id = null)
    {
        $enrollment = null;

        if (@$student_id) {
            $student = Student::where('id', $student_id)->first();
            if (@$student->enrollment) {
                return $student->enrollment;
            }
            $student_code = $this->_getStCode($student->stream, $student->course, $student->ai_code);
            $enrollment = $this->_generateEnrollment($student->stream, $student->course, $student->ai_code);
            $studentenrollmentnum = ['is_eligible' => 1, 'enrollment' => $enrollment, 'student_code' => $student_code];
            $studentenrollmentnum = Student::where('id', $student_id)->update($studentenrollmentnum);

            $applicationarray = ['enrollment' => $enrollment];
            $applicationarray = Application::where('student_id', $student_id)->update($applicationarray);
            //$smsStatus = $this->_sendLockSubmittedMessage($student_id);
        }
        return $enrollment;
    }

    public function _getStCode($stream = null, $course = null, $aicode = null)
    {
        $stcode = '';
        $endcode = '';
        if ($stream == 1 && $course == 10) {
            $stcode = 2000;
            $endcode = 3000;
        }
        if ($stream == 1 && $course == 12) {
            $stcode = 3000;
            $endcode = 4000;
        }
        if ($stream == 2 && $course == 10) {
            $stcode = 4000;
            $endcode = 5000;
        }
        if ($stream == 2 && $course == 12) {
            $stcode = 5000;
            $endcode = 6000;
        }
        $academicyear_id = config("global.form_admission_academicyear_id");
        $mainTable = "students";
        $scode = DB::table($mainTable)
            ->select('student_code')
            ->where('ai_code', '=', $aicode)
            ->where('student_code', '>', $stcode)
            ->where('student_code', '<', $endcode)
            ->where('exam_year', '=', $academicyear_id)
            ->max('student_code');
        $stucode = '';
        if (isset($scode) && $scode > 0) {
            $stucode = $scode + 1;
        } else {
            $stucode = $stcode + 1;
        }
        return $stucode;
    }

    public function _generateEnrollment($stream = null, $course = null, $aicode = null)
    {
        $stucode = $this->_getStCode($stream, $course, $aicode);

        $admission_enrolment_year_slug = config("global.form_admission_enrolment_year_slug");
        if (strlen($aicode) == 4) {
            $enrollmentno = '0' . $aicode . $admission_enrolment_year_slug . $stucode;
        } else {
            $enrollmentno = $aicode . $admission_enrolment_year_slug . $stucode;
        }
        return $enrollmentno;
    }

    public function _setEnrollmentAndIsEligiable($student_id = null)
    {

        $enrollment = null;
        if (@$student_id) {
            $student = Student::where('id', $student_id)->first();
            $is_change_enrollment = @$student->is_change_enrollment;
            if (@$student->last_enrollment_before_change_req && $is_change_enrollment == 0) {
                $enrollment = @$student->last_enrollment_before_change_req;
                $studentenrollmentnum = ['is_eligible' => 1, 'enrollment' => $enrollment];
            } else {
                $student_code = $this->_getStCode($student->stream, $student->course, $student->ai_code);
                $enrollment = $this->_generateEnrollment($student->stream, $student->course, $student->ai_code);
                $studentenrollmentnum = ['is_eligible' => 1, 'enrollment' => $enrollment, 'student_code' => $student_code];
            }
            $studentenrollmentnum = Student::where('id', $student_id)->update($studentenrollmentnum);
            $applicationarray = ['enrollment' => $enrollment];
            $applicationarray = Application::where('student_id', $student_id)->update($applicationarray);
            $smsStatus = $this->_sendLockSubmittedMessage($student_id);
        }
        return $enrollment;
    }

    public function _sendLockSubmittedMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        $student = Student::with('application', 'studentfee')
            ->where('students.id', $student_id)
            ->first();
        $fld = "enrollment";
        $$fld = $student->$fld;
        $fld = "mobile";
        $$fld = $student->$fld;
        // $paymentUrl = route('admission_fee_payment');
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $application_fee = 0;
        if (isset($student->studentfee->total) && $student->studentfee->total > 0) {
            $application_fee = $student->studentfee->total;
        }
        $sms = null;
        if ($application_fee > 0) {
            $sms = 'Dear Applicant, Your application has been registered successfully. Please pay admission fees Rs.' . $application_fee . ' by clicking on URL  ' . $paymentUrl . ' to complete your admission application.-RSOS,GoR';
        }
        return $this->_sendSMS($mobile, $sms);
    }

    public function _settingChangeReqEnrollment($student_id = null, $is_change_enrollment = null)
    {
        $status = false;
        // if(@$is_change_enrollment){
        $studentDetails = Student::where('id', $student_id)->first();
        $last_enrollment_before_change_req = $studentDetails->enrollment;
        $count_change_enrollment = $studentDetails->count_change_enrollment;
        if (@$is_change_enrollment) {
            $count_change_enrollment++;
        }
        $enrollment = null;
        $studentenrollmentnum = [
            'count_change_enrollment' => $count_change_enrollment,
            'is_eligible' => null,
            'enrollment' => $enrollment,
            'last_enrollment_before_change_req' => $last_enrollment_before_change_req
        ];
        $studentenrollmentnum = Student::where('id', $student_id)->update($studentenrollmentnum);
        $applicationarray = ['enrollment' => $enrollment];
        $applicationarray = Application::where('student_id', $student_id)->update($applicationarray);
        $status = true;
        // }
        return $status;
    }

    public function subjectsDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        if ($request->practical_type == 1) {
            $modelObj = new Subject;  /// create model object
            $validators[] = Validator::make($request->all(), $modelObj->rulesapplicationandstudent);
        } elseif ($request->practical_type == 0) {
            $modelObj = new Subject;  /// create model object
            $validators[] = Validator::make($request->all(), $modelObj->rulesapplicationandstudents);
        }
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function checkAllowToUpdateFinalLockOrPayment($exam_year = null, $exam_month = null)
    {
        $status = false;
        $role_id = @Session::get('role_id');
        $super_admin_id = Config::get("global.super_admin_id");
        $developer_admin = Config::get("global.developer_admin");
        $status = null;
        if ($role_id == $super_admin_id || $role_id == $developer_admin) {
            $status = true;
        } else {
            /* Not allowed for last year students */
            $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
            $current_exam_month_id = Config::get("global.form_current_exam_month_id");
            $form_current_allowed_exam_month_id = Config::get("global.form_current_allowed_exam_month_id");
            if ($exam_year == $current_admission_session_id && in_array($exam_month, array(1, 2))) {
                $status = true;
            } else {

            }
            /* Not allowed for last year students */
        }
        return $status;
    }

    public function _isValidStudentSubjectsCount($student_id = null)
    {
        $status = true;
        $challanIdExistCount = ExamSubject::where('student_id', $student_id)->whereNull('deleted_at')->distinct('subject_id')->orderBy('exam_year', 'DESC')->orderBy('exam_month', 'DESC')->count();

        if ($challanIdExistCount > 7) {
            $status = false;
        }
        return $status;
    }

    public function _updateGraceMarksThPr($id = null, $type = 't', $increesMarks = 1)
    {
        $tempData = PrepareExamSubject::where('prepare_exam_subjects.id', '=', $id)->first();
        $practical_marks = $tempData->practical_marks;
        $theory_marks = $tempData->theory_marks;
        $course = $tempData->course;
        if ($course == 10) {
            if ($theory_marks == 999) {
                $datasave['practical_marks'] = $practical_marks + $increesMarks;
                $datasave['is_grace_marks_given'] = 5;
            } else {
                $datasave['theory_marks'] = $theory_marks + $increesMarks;
                $datasave['is_grace_marks_given'] = 6;
            }
        } else if ($course == 12) {
            if ($type == 't') {
                $datasave['theory_marks'] = $theory_marks + $increesMarks;
                $datasave['is_grace_marks_given'] = 8;
            } else if ($type == 'p') {
                $datasave['practical_marks'] = $practical_marks + $increesMarks;
                $datasave['is_grace_marks_given'] = 7;
            }
        }
        PrepareExamSubject::where('prepare_exam_subjects.id', '=', $id)->update($datasave);
        return true;
    }

    public function _checkUnderMaintance()
    {
        $routeArray = app('request')->route()->getAction();
        $controllerAction = class_basename($routeArray['controller']);
        list($controller, $action) = explode('@', $controllerAction);
        $action = Route::currentRouteName();
        $item = $controller . "_" . $action;
        $items = array("LandingController_under_Maintenance");
        $whiteListMasterIps = Config::get("global.whiteListMasterIps");
        $ip = Config::get("global.request_client_ip");
        $allowStatus = false;

        if (in_array($ip, $whiteListMasterIps)) {
            $allowStatus = true;
        } else {
            if (in_array($item, $items)) {
                $allowStatus = true;
            }
        }
        return $allowStatus;
    }

    public function _getBoardResult($course = null, $roll_number = null, $year = null)
    {
        $response = false;
        $course = '12';
        $roll_number = '2608607';
        $year = '2023';
        if ($course == null || $roll_number == null || $year == null) {
        } else {
            $url = 'http://dceapp.rajasthan.gov.in/boarddata.asmx/GetBoardData?board=RBSC&year=' . $year . '&rollno=' . $roll_number . '&Class=' . $course;


            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);

            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                // "cURL Error #:" . $err;
            } else {
                // $response;
            }
        }
        return $response;
        // http://dceapp.rajasthan.gov.in/boarddata.asmx/GetBoardData?board=RBSC&year=2017&rollno=1309044&Class=10
        // http://dceapp.rajasthan.gov.in/boarddata.asmx/GetBoardData?board=RBSC&year=2023&rollno=2608607&Class=12
    }

    function XMLtoJSON($xml)
    {
        $xml = file_get_contents($xml);    // gets XML content from file
        $xml = str_replace(array("\n", "\r", "\t"), '', $xml);    // removes newlines, returns and tabs

        // replace double quotes with single quotes, to ensure the simple XML function can parse the XML
        $xml = trim(str_replace('"', "'", $xml));
        $simpleXml = simplexml_load_string($xml);

        return stripslashes(json_encode($simpleXml));    // returns a string with JSON object
    }

    public function _getCheckAllowToCheckSupp()
    {
        $showStatus = false;
        $combo_name = 'supp_register_payment_allowed_ips';
        $supp_register_payment_allowed_ips = $this->master_details($combo_name);

        if (isset($supp_register_payment_allowed_ips[1]) && $supp_register_payment_allowed_ips[1] != null && $supp_register_payment_allowed_ips[1] != '' && !empty($supp_register_payment_allowed_ips[1])) {
            $request_client_ip = Config::get('global.request_client_ip');
            $arr = $supp_register_payment_allowed_ips->toArray();
            if (@$arr[1]) {
                $supp_register_payment_allowed_ips = json_decode($arr[1], true);
            }
            if (in_array($request_client_ip, $supp_register_payment_allowed_ips)) {
                $showStatus = true;
            }
        } else {
            $showStatus = true;
        }
        if ($showStatus == false) {
            $combo_name = 'supp_form_aicenter_level_allow_or_not';
            $supp_form_aicenter_level_allow_or_not = $this->master_details($combo_name);
            if (@$supp_form_aicenter_level_allow_or_not[1]) {
                $showStatus = true;
            } else {
                $showStatus = false;
            }
        }
        return $showStatus;
    }

    public function BooksRequrementDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $PublicationBook = new PublicationBook; /// create model object
        if (@$request->medium == 1) {
            $validators[] = Validator::make($request->all(), $PublicationBook->booksrequrementmakehindirules, $PublicationBook->booksrequrementhindimessage);
        } else {
            $validators[] = Validator::make($request->all(), $PublicationBook->booksrequrementmakerules, $PublicationBook->booksrequrementrulemessage);
        }
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        $key = 0;
        if (!empty($request->hindi_required_book_count) && !empty($request->hindi_auto_student_count) && @$request->hindi_required_book_count > @$request->hindi_auto_student_count) {
            $fld = 'hindi_required_book_count';
            $errMsg = 'Hindi required count not grater than the enrollment count.';
            $validator->getMessageBag()->add($fld, $errMsg);
            $response[$key]['isValid'] = false;
            $response[$key]['errors'] = $validator->messages();
            $response[$key]['validator'] = $validator;
        }

        if (!empty($request->english_required_book_count) && !empty($request->english_auto_student_count) && @$request->english_required_book_count > @$request->english_auto_student_count) {
            $fld = 'english_required_book_count';
            $errMsg = 'English required count not grater than the enrollment count.';
            $validator->getMessageBag()->add($fld, $errMsg);
            $response[$key]['isValid'] = false;
            $response[$key]['errors'] = $validator->messages();
            $response[$key]['validator'] = $validator;
        }
        $bookid = null;
        if (@$request->publicbookid) {
            $bookid = $request->publicbookid;
        }
        $bookRequirementCustomComponent = new BookRequirementCustomComponent();
        $dataesxists = $bookRequirementCustomComponent->bookDataAllReadyExists(@$request->course, @$request->subject_id, @$request->ai_code, null, @$request->subject_volume_id, $bookid);
        if (count(@$dataesxists) != 0) {
            $fld = 'subject_id';
            $errMsg = 'Selected Combination already Exists.';
            $validator->getMessageBag()->add($fld, $errMsg);
            $response[$key]['isValid'] = false;
            $response[$key]['errors'] = $validator->messages();
            $response[$key]['validator'] = $validator;
        }

        return $response;
    }

    public function setPagintorPerPageLimit($pagevalue = null)
    {
        $defaultPageLimit = 20;
        // if(@$pagevalue){
        // 	$defaultPageLimit = $pagevalue;
        // }else{
        // 	$defaultPageLimit = config("global.defaultPageLimit");
        // }
        $defaultPageLimit = 150;

        Session::put("defaultPageLimit", $defaultPageLimit);
        $defaultPageLimit = session::get("defaultPageLimit");

        Config::set('global.defaultPageLimit', $defaultPageLimit);
        return $defaultPageLimit;
    }

    public function AjaxSelfsRegistrationValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $student = new Student; /// create model object
        $validators[] = Validator::make($request->all(), $student->studentself, $student->studentselfmessage);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function AjaxallreadystudentValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $PageDetail = new PageDetail; /// create model object
        $validators[] = Validator::make($request->all(), $PageDetail->rules, $PageDetail->message);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function genFixcodeCenter($isFirst = false)
    {
        $isNextRoundRequire = false;
        $fld = "fixcode"; //$fld = "fixcode";
        $alreadyListFirst = ExamcenterDetail::whereNotNull($fld)->first([$fld, 'id']);

        if (@$alreadyListFirst->id) {

        } else {
            if (@$isFirst) {
                $x = 2; // Amount of digits
                $min = 101;
                $max = 999;
                $min = pow(10, $x);
                $max = pow(10, $x + 1) - 1;
                $gen = rand($min, $max);
                $saveData[$fld] = $gen;
                $nullFixcode = ExamcenterDetail::whereNull($fld)->first([$fld, 'id']);
                ExamcenterDetail::where('id', @$nullFixcode->id)->update($saveData);
            }
        }
        $nullList = ExamcenterDetail::whereNull($fld)->pluck($fld, 'id');
        if (count($nullList) > 0) {
            foreach (@$nullList as $id => $value) {
                $alreadyList = ExamcenterDetail::whereNotNull($fld)->pluck($fld, 'id');
                $x = 2; // Amount of digits
                $min = 101;
                $max = 999;
                $min = pow(10, $x);
                $max = pow(10, $x + 1) - 1;
                $gen = rand($min, $max);
                $alreadyListArr = $alreadyList->toArray();
                if (in_array($gen, $alreadyListArr)) {
                    $isNextRoundRequire = true;
                    continue;
                } else {
                    $saveData[$fld] = $gen;
                    ExamcenterDetail::where('id', $id)->update($saveData);
                }
            }
        } else {
            return $isNextRoundRequire;
        }
        $nullFixcode = ExamcenterDetail::whereNull($fld)->first([$fld, 'id']);
        if (@$nullFixcode->id && !empty($nullFixcode->id)) {
            //$this->genFixcodeCenter(false);
            $isNextRoundRequire = true;
        }
        if ($isNextRoundRequire == true) {
            $isNextRoundRequire = $this->genFixcodeCenter(true);
        }
        return $isNextRoundRequire;
    }

    public function ajaxUpdateSsoDetilasValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;

        $validators[] = Validator::make($request->all(), [
            'ssoid' => 'required',
        ],
            [
                'ssoid.required' => 'SSO is required',
            ]);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }

        if (@$request->ssoid && @$request->student_id) {
            $student_id = $request->student_id;
            $ssoid = $request->ssoid;
            $course = $request->course;
            $stream = $request->exam_month;
            $custom_component_obj = new CustomComponent;
            $table_name = 'students';
            $checkssoidallreadyaccessCount = $custom_component_obj->_checkssoidallreadyaccess($table_name, $student_id, $ssoid, $course, $stream);
            if (@$checkssoidallreadyaccessCount > 0) {
                $fld = 'ssoid';
                $errMsg = 'SSOID already exist.';
                $validator->getMessageBag()->add($fld, $errMsg);
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = collect($errMsg);
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }

    public function _sendOTPToStudent($student_id = null)
    {
        $status = false;
        $student = Student::where('id', $student_id)->first();
        if (@$student) {
            $otp = random_int(100000, 999999);
            $updateStudent['otp'] = $otp;
            Student::where('id', $student_id)->update($updateStudent);
            $sms = "Dear Applicant, Please verify your mobile number. The OTP is :" . $otp . ". - RSOS,GoR";
            $templateID = "1007609835962970505";
            $this->_sendSMS($student->mobile, $sms, $templateID);
            $status = true;
        }
        return $status;
    }

    public function _verifyOnlyMobileStudentOTP($student_id = null, $otp = null)
    {
        $status = false;
        $student = Student::where('id', $student_id)->where('otp', $otp)->first();
        if (@$student) {
            $is_otp_verified = 1;
            $updateStudent['is_otp_verified'] = $is_otp_verified;
            Student::where('id', $student_id)->update($updateStudent);
            $status = true;
        }
        return $status;
    }

    public function _verifyStudentOTP($student_id = null, $otp = null, $ssoid = null)
    {
        $status = false;
        $student = Student::where('id', $student_id)->where('otp', $otp)->first();
        if (@$student) {
            $updateStudent['ssoid'] = $ssoid;
            $password = Hash::make('123456789');
            $updateStudent['password'] = $password;
            Student::where('id', $student_id)->update($updateStudent);
            $status = true;
        }
        return $status;
    }

    public function _getOnlyNumbers($str)
    {
        $res = preg_replace("/[^0-9]/", "", $str);
        return $res;
    }

    public function _removeSpecialChar($str)
    {
        $res = str_replace(array('\'', '"', ',', '-', ' ', '\"', ';', '<', '>'), '', $str);
        return $res;
    }

    public function _removeSpecialCharOtherThenSpace($str)
    {
        $res = null;
        if (@$str) {
            $res = str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ']', '[', '\'', '"', ',', '-', '\"', ';', '<', '>', '.', '`', '  '), '', $str);
        }
        return $res;
    }

    public function _updateStudentAllotmentLog($table_primary_id = null)
    {
        $status = false;
        if (!empty($table_primary_id)) {
            $table_datas = StudentAllotment::where("center_allotment_id", "=", $table_primary_id)->get();
            if (@$table_datas) {
                $table_datas = $table_datas->toArray();
            }
            if (!empty($table_datas)) {
                foreach ($table_datas as $k => $table_data) {
                    if (@$table_data['id']) {
                        $custom_data = $table_data;
                        $custom_data['user_id'] = Auth::user()->id;
                        $custom_data['ip'] = Config::get('global.request_client_ip');
                        $custom_data['deleted'] = date("Y-m-d H:i:s");
                        DB::table('student_allotments_logs')->insert($custom_data);
                        $status = true;
                    }
                }
            }
        }
        return $status;
    }

    public function _updateAicenterSittingMappedLog($table_primary_id = null)
    {
        $status = false;
        if (!empty($table_primary_id)) {
            $table_data = AicenterSittingMapped::where("id", "=", $table_primary_id)->first();
            if (@$table_data) {
                $table_data = $table_data->toArray();
            }
            if (!empty($table_data)) {
                if (@$table_data['id']) {
                    $custom_data = $table_data;
                    $custom_data['user_id'] = Auth::user()->id;
                    $custom_data['ip'] = Config::get('global.request_client_ip');
                    $custom_data['deleted'] = date("Y-m-d H:i:s");
                    DB::table('aicenter_sitting_mappeds_logs')->insert($custom_data);
                    $status = true;
                }
            }
        }
        return $status;
    }

    /* Center allomtnet process after deleted logs start */

    public function _updateCenterAllotmentLog($table_primary_id = null)
    {
        $status = false;
        if (!empty($table_primary_id)) {
            $table_data = CenterAllotment::where("id", "=", $table_primary_id)->first();
            if (@$table_data) {
                $table_data = $table_data->toArray();
            }
            if (!empty($table_data)) {
                if (@$table_data['id']) {
                    $custom_data = $table_data;
                    $custom_data['user_id'] = Auth::user()->id;
                    $custom_data['ip'] = Config::get('global.request_client_ip');
                    $custom_data['deleted'] = date("Y-m-d H:i:s");
                    DB::table('center_allotments_logs')->insert($custom_data);
                    $status = true;
                }
            }
        }
        return $status;
    }

    public function _getListBanksName()
    {
        $condtions = null;
        $result = array();
        $mainTable = "bank_masters";
        $cacheName = "_getListBanksNameUpdateFinal";
        // Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {

                $result = DB::table($mainTable)->whereNotNull('BANK_ID')->whereNotNull('BANK_NAME')->whereNull('deleted_at')->groupBy('BANK_ID')->orderBy('BANK_NAME')->pluck('BANK_NAME', 'BANK_ID');

                if (@$result) {
                    return $result->toArray();
                }
                return $result;
            });
        }
        return $result;
    }

    public function _getListBanksIfscCode($bank_id = null, $state_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($bank_id)) {
            $condtions['BANK_ID'] = $bank_id;
        }
        if (!empty($state_id)) {
            $condtions['state_id'] = $state_id;
        }
        $mainTable = "bank_masters";
        $cacheName = "_getListBanksIfscCodeUpdateFinalUA" . $bank_id . $state_id;

        // Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->select(
                    DB::raw("CONCAT( COALESCE(`IFSC_CODE`,''),' - ', COALESCE(`MICR`,''),' - ', COALESCE(`BRANCH`,''),' - ',COALESCE(`BRANCH_ADDRESS`,'')) AS BRANCH_ADDRESS"), 'IFSC_CODE')
                    ->where($condtions)
                    ->pluck('BRANCH_ADDRESS', 'IFSC_CODE');
                if (@$result) {
                    return $result->toArray();
                }
                return $result;
            });
        }
        return $result;
    }

    /* Bank Master Start */

    public function _getListBanksMICRCode($bank_id = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($bank_id)) {
            $condtions = ['BANK_ID' => $bank_id];
        }
        $mainTable = "bank_masters";
        $cacheName = "_getListBanksMICRCode";
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->orderBy('MICR')->pluck('MICR', 'BANK_BRANCH_ID');
                if (@$result) {
                    return $result->toArray();
                }
                return $result;
            });
        }
        return $result;
    }

    public function _getListBanksAddress($BANK_BRANCH_ID = null, $IFSC_CODE = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($BANK_BRANCH_ID)) {
            $condtions[] = ['BANK_BRANCH_ID' => $BANK_BRANCH_ID];
        }
        if (!empty($IFSC_CODE)) {
            $condtions[] = ['IFSC_CODE' => $IFSC_CODE];
        }
        $mainTable = "bank_masters";
        $cacheName = "_getListBanksAddress";
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->orderBy('BRANCH_ADDRESS')->pluck('BRANCH_ADDRESS', 'BANK_BRANCH_ID');
                if (@$result) {
                    return $result->toArray();
                }
                return $result;
            });
        }
        return $result;
    }

    public function my_current_ip()
    {
        $ip = Config::get('global.CURRENT_IP');
        return $ip;
    }

    public function _getBankBranchDetails($IFSC_CODE = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($IFSC_CODE)) {
            $condtions = ['IFSC_CODE' => $IFSC_CODE];
            $mainTable = "bank_masters";
            $cacheName = "_getBankBranchDetails";
            Cache::forget($cacheName);
            if (Cache::has($cacheName)) {
                $result = Cache::get($cacheName);
            } else {
                $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                    $result = DB::table($mainTable)->where($condtions)->whereNull('deleted_at')->orderBy('BRANCH_ADDRESS')->first(['BANK_NAME', 'IFSC_CODE', 'MICR', 'BRANCH_ADDRESS', 'BRANCH']);
                    if (@$result) {
                        return $result;
                    }
                    return $result;
                });
            }
        }
        return $result;
    }

    public function getNotValidAfterDatePaymentRecivedStudentIds()
    {
        $exam_year = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.supp_current_exam_month_id');
        $getmaxdaysglobal = Config::get('global.getMaxNumberOfDays');
        $getmaxdate = DB::table('exam_late_fee_dates')->where('stream', $exam_month)->max('to_date');
        $getmaxdate = date('Y-m-d', strtotime($getmaxdate . ' + ' . $getmaxdaysglobal . ' days'));
        $notValidStudents = DB::table('students')->where('exam_year', $exam_year)
            ->where('exam_month', $exam_month)
            ->whereNotNull('challan_tid')
            ->where('application_fee_date', '>', $getmaxdate)
            ->pluck('id', 'id');
        return $notValidStudents;
    }

    public function reLoginCurrentStudentAfterPayment($student_id = null)
    {
        $password = '123456789';
        $status = false;
        $exam_year = Config::get("global.form_supp_current_admission_session_id");
        $studentrole = Config::get("global.student");

        $Student = Student::find($student_id);
        $currentSsoid = $Student->ssoid;
        $credentials = (['ssoid' => $currentSsoid, 'password' => $password]);
        if (Auth::guard('student')->attempt($credentials)) {
            Session::put("current_student", $Student);
            $student_id = Auth::guard('student')->user()->id;
            Session::put('role_id', $studentrole);
            $studentRoleId = config("global.student");
            $custom_component_obj = new CustomComponent;
            $custom_component_obj->_setCountDownTimerDetails($studentRoleId);
            $status = true;
        }
        return $status;
    }

    /* Bank Master End */

    public function student_doc_rejected_notification($student_id = null)
    {

        $status = true;
        return $status;

        $documentverifications = DocumentVerification::where('student_id', $student_id)->first();
        $status = false;
        if (empty($documentverifications)) {
            $status = false;
        } elseif ($documentverifications->all_document_is_verify == 2) {
            $status = true;
        }
        return $status;
    }

    public function otherThenDocumentsVerificationUserDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $response['isValid'] = true;
        $response['errors'] = null;
        $response['validator'] = null;
        $isAllRejected = false;
        $validator = Validator::make([], []);

        if (count($request->all()) > 0) {
            /* Array setup for custom checkbox start */
            $tempInputs = $request->all();
            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);
            unset($tempInputs['isAllRejected']);
            $inputs = array();
            foreach ($tempInputs as $k => $v) {
                if ($k == "mainitem") {
                    $inputs[$k] = $v;
                    continue;
                } else if (str_contains($k, "_is_verify_remarks")) {
                    $inputs[$k] = $v;
                    continue;
                } else {
                    if (@$v[1] && $v[1] == "on") {
                        $inputs[$k] = 1;
                    } elseif (@$v[2] && $v[2] == "on") {
                        $inputs[$k] = 2;
                    }
                }
            }
            /* Array setup for custom checkbox end */

            $fieldBaseName = "verifier_";
            $role_id = @Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            if ($role_id == $super_admin_id) {
                $fieldBaseName = "dept_";
            }
            $mainitem = @$inputs['mainitem'];
            $documentInput = $this->getStudentRequriedRejectedDocument($mainitem);

            $arrVerifications = $this->getVerificationOtherThenDocInputs();
            // print_r($mainitem); print_r($arrVerifications);

            unset($inputs['mainitem']);
            if (@$mainitem) {
                foreach ($mainitem as $k => $mainItemLbl) {
                    if (!in_array($mainItemLbl, array_keys($arrVerifications))) {
                        continue;
                    }

                    $fldNameVerify = $fieldBaseName . $mainItemLbl . "_is_verify";
                    $fldNameRemarks = $fieldBaseName . $mainItemLbl . "_is_verify_remarks";
                    $fld = 'verify_documents';
                    // dd($inputs);
                    if (@$inputs[$fldNameVerify]) {
                        if ($inputs[$fldNameVerify] == null) {
                            $errMsg = ucfirst(@$arrVerifications[$mainItemLbl]['lbl']) . ' is required.';
                            $errors = $errMsg;
                            $validator->getMessageBag()->add($fld, $errMsg);
                            $isValid = false;
                        } else if ($inputs[$fldNameVerify] == 2) {
                            if (@$inputs[$fldNameRemarks] == null) {
                                $errMsg = ucfirst(@$arrVerifications[$mainItemLbl]['lbl']) . ' Remarks is required.';
                                $errors = $errMsg;
                                $validator->getMessageBag()->add($fld, $errMsg);
                                $isValid = false;
                            }
                            $isAllRejected = encrypt(1);
                        }
                    } else {
                        // dd($mainItemLbl);
                        $errMsg = ucfirst(@$arrVerifications[$mainItemLbl]['lbl']) . ' is required.';
                        $errors = $errMsg;
                        $validator->getMessageBag()->add($fld, $errMsg);
                        $isValid = false;
                    }
                }
            }
            $response['isValid'] = $isValid;
            $response['errors'] = @$validator->messages()->toArray();
            $response['isAllRejected'] = @$isAllRejected;
        }
        return $response;
    }

    public function getStudentRequriedRejectedDocument($documents = null)
    {
        $documentInput = array();

        foreach ($documents as $key => $document) {
            if ($document == "photograph") {
                $documentInput["photograph"] = "Upload Photograph " . config('global.starMark');
                $documentInput['label']["photograph_label"] = "Upload Photograph " . config('global.starMark');
                $documentInput['label']["validation_photograph_label"] = "Photograph ";
            }
            if ($document == "signature") {
                $documentInput["signature"] = "Upload Signature " . config('global.starMark');
                $documentInput['label']["signature_label"] = " Upload Signature  " . config('global.starMark');
                $documentInput['label']["validation_signature_label"] = "Signature ";
            }
            if ($document == "category_a") {
                $documentInput["category_a"] = "Upload DOB Certificate " . config('global.starMark');
                $documentInput['label']["category_a_label"] = "(DOB) : Affidavit(Footpath &amp; Orphan) Or Birth Certificate Or Marksheet/TC Or Seva-Pustika Or Transfer Certificate " . config('global.starMark');
                $documentInput['label']["validation_category_a_label"] = "(DOB) : Affidavit(Footpath &amp; Orphan) Or Birth Certificate Or Marksheet/TC Or Seva-Pustika Or Transfer Certificate ";
            }
            if ($document == "category_b") {
                $documentInput["category_b"] = "Upload Address Proof Certificate " . config('global.starMark');
                $documentInput['label']["category_b_label"] = "(Address Proof) : Addhar Card Or Bhamaashah Card Or Driving License Or Ration Card Or Voter ID Card " . config('global.starMark');
                $documentInput['label']["validation_category_b_label"] = "(Address Proof) : Addhar Card Or Bhamaashah Card Or Driving License Or Ration Card Or Voter ID Card ";
            }
            if ($document == "pre_qualification") {
                $documentInput["pre_qualification"] = "Upload Pre Qualification Certificate " . config('global.starMark');
                $documentInput['label']["pre_qualification_label"] = "Pre Qualification Certificate";
                $documentInput['label']["validation_pre_qualification_label"] = "Pre Qualification Certificate ";
            }
            if ($document == "disability") {
                $documentInput["disability"] = "Upload Disability Certificate " . config('global.starMark');
                $documentInput['label']["disability_label"] = "Disability Certificate ";
                $documentInput['label']["validation_disability_label"] = "Disability Certificate ";
            }

            if ($document == "category_c") {
                $documentInput["category_c"] = "Upload Other-I document Certificate ";
                $documentInput['label']["category_c_label"] = "Other-I document Certificate ";
                $documentInput['label']["validation_category_c_label"] = "Other-I document Certificate ";
            }

            if ($document == "category_d") {
                $documentInput["category_d"] = "Upload Other-II document Certificate ";
                $documentInput['label']["category_d_label"] = "Other-II document Certificate ";
                $documentInput['label']["validation_category_d_label"] = "Other-II document Certificate";
            }
        }
        return @$documentInput;
    }

    public function getVerificationOtherThenDocInputs()
    {
        $arrVerifications["personal"] = array("lbl" => "Personal Details(व्यक्तिगत विवरण)", "file" => "personal_details");
        $arrVerifications["address"] = array("lbl" => "Address Details(पता विवरण)", "file" => "address_details");
        $arrVerifications["bank"] = array("lbl" => "Bank Details(बैंक विवरण)", "file" => "bank_details");
        $arrVerifications["admission_subject"] = array("lbl" => "Admission Subjects Details(प्रवेश विषय विवरण)", "file" => "admission_subjects_details");
        $arrVerifications["exam_subject"] = array("lbl" => "Exam Subjects Details(परीक्षा विषय विवरण)", "file" => "exam_subjects_details");
        $arrVerifications["toc_subject"] = array("lbl" => "TOC Subjects Details(टीओसी विषय विवरण)", "file" => "toc_subjects_details");
        $arrVerifications["fees"] = array("lbl" => "Fees Details(शुल्क विवरण)", "file" => "fees_details");
        return $arrVerifications;
    }

    public function DocumentVerificationUserDetailsValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $response['isValid'] = true;
        $response['errors'] = null;
        $response['validator'] = null;
        $isAllRejected = false;
        $validator = Validator::make([], []);

        if (count($request->all()) > 0) {
            /* Array setup for custom checkbox start */
            $tempInputs = $request->all();
            unset($tempInputs['ajaxRequest']);
            unset($tempInputs['_method']);
            unset($tempInputs['_token']);
            unset($tempInputs['action']);
            unset($tempInputs['isAllRejected']);
            $inputs = array();
            foreach ($tempInputs as $k => $v) {
                if ($k == "mainitem") {
                    $inputs[$k] = $v;
                    continue;
                } else if (str_contains($k, "_is_verify_remarks")) {
                    $inputs[$k] = $v;
                    continue;
                } else {
                    if (@$v[1] && $v[1] == "on") {
                        $inputs[$k] = 1;
                    } elseif (@$v[2] && $v[2] == "on") {
                        $inputs[$k] = 2;
                    }
                }
            }
            /* Array setup for custom checkbox end */

            $fieldBaseName = "verifier_";
            $role_id = @Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            if ($role_id == $super_admin_id) {
                $fieldBaseName = "dept_";
            }
            $mainitem = @$inputs['mainitem'];
            $documentInput = $this->getStudentRequriedRejectedDocument($mainitem);

            $arrVerifications = $this->getVerificationOtherThenDocInputs();
            // print_r($mainitem); print_r($arrVerifications);
            unset($inputs['mainitem']);
            if (@$mainitem) {
                foreach ($mainitem as $k => $mainItemLbl) {
                    if (in_array($mainItemLbl, array_keys($arrVerifications))) {
                        continue;
                    }
                    $fldNameVerify = $fieldBaseName . $mainItemLbl . "_is_verify";
                    $fldNameRemarks = $fieldBaseName . $mainItemLbl . "_is_verify_remarks";
                    $fld = 'verify_documents';
                    if (@$inputs[$fldNameVerify]) {
                        if ($inputs[$fldNameVerify] == null) {
                            $errMsg = ucfirst(@$documentInput['label']["validation_" . $mainItemLbl . "_label"]) . ' is required.';
                            $errors = $errMsg;
                            $validator->getMessageBag()->add($fld, $errMsg);
                            $isValid = false;
                        } else if ($inputs[$fldNameVerify] == 2) {
                            if ($inputs[$fldNameRemarks] == null) {
                                $errMsg = ucfirst(@$documentInput['label']["validation_" . $mainItemLbl . "_label"]) . ' Remarks is required.';
                                $errors = $errMsg;
                                $validator->getMessageBag()->add($fld, $errMsg);
                                $isValid = false;
                            }
                            $isAllRejected = encrypt(1);
                        }
                    } else {
                        // dd($mainItemLbl);
                        $errMsg = ucfirst(@$documentInput['label']["validation_" . $mainItemLbl . "_label"]) . ' is requried.';
                        $errors = $errMsg;
                        $validator->getMessageBag()->add($fld, $errMsg);
                        $isValid = false;
                    }
                }
            }
            $response['isValid'] = $isValid;
            $response['errors'] = @$validator->messages()->toArray();
            $response['isAllRejected'] = @$isAllRejected;
        }
        return $response;
    }

    public function getupdatesessionasubjectnewtable($student_id = null)
    {
        $exam_year = Config::get("global.form_admission_academicyear_id");
        $exam_month = 1;
        $examsubjects = ExamSubject::where('exam_year', $exam_year)->where('student_id', $student_id)->where('exam_month', $exam_month)->get(['remarks', 'student_id', 'enrollment', 'pastdata_id', 'subject_id', 'is_additional', 'final_theory_marks', 'final_practical_marks', 'sessional_marks', 'sessional_marks_reil_result_20', 'sessional_marks_reil_result', 'total_marks', 'final_result', 'exam_year', 'stream', 'exam_month', 'course', 'subject_type', 'adm_type', 'is_sessional_mark_entered', 'is_temp_exam_subject', 'modified_at', 'is_supplementary_subject']);
        if (count($examsubjects) > 0) {
            $sessionalExamSubjects = SessionalExamSubject::where('exam_year', $exam_year)->where('student_id', $student_id)->where('exam_month', $exam_month)->forceDelete();
            foreach ($examsubjects as $student_id) {
                $studentarray = [
                    'remarks' => @$student_id->remarks,
                    'student_id' => @$student_id->student_id,
                    'enrollment' => @$student_id->enrollment,
                    'pastdata_id' => @$student_id->pastdata_id,
                    'subject_id' => @$student_id->subject_id,
                    'is_additional' => @$student_id->is_additional,
                    'final_theory_marks' => @$student_id->final_theory_marks,
                    'final_practical_marks' => @$student_id->final_practical_marks,
                    'sessional_marks' => @$student_id->sessional_marks,
                    'sessional_marks_reil_result_20' => @$student_id->sessional_marks_reil_result_20,
                    'sessional_marks_reil_result' => @$student_id->sessional_marks_reil_result,
                    'total_marks' => @$student_id->total_marks,
                    'final_result' => @$student_id->final_result,
                    'exam_year' => @$student_id->exam_year,
                    'stream' => @$student_id->stream,
                    'exam_month' => @$student_id->exam_month,
                    'course' => @$student_id->course,
                    'subject_type' => @$student_id->subject_type,
                    'adm_type' => @$student_id->adm_type,
                    'is_sessional_mark_entered' => @$student_id->is_sessional_mark_entered,
                    'is_temp_exam_subject' => @$student_id->is_temp_exam_subject,
                    'modified_at' => @$student_id->modified_at,
                    'is_supplementary_subject' => @$student_id->is_supplementary_subject
                ];
                $sessionalExamSubjectinsert = SessionalExamSubject::create($studentarray);
            }
        }
        return true;
    }

    public function _sendTransationDetailsForDBTToJanAadhar($inputs = null)
    {

        $entitlementId = $inputs->entitlementId;
        $entitlementMemId = $inputs->entitlementMemId;
        $janaadhaarId = $inputs->janaadhaarId;


        $janaadhaarMemId = $inputs->janaadhaarMemId;
        $transactionId = $inputs->transactionId;
        $dueTransactionId = $inputs->dueTransactionId;
        $aadharNo = $inputs->aadharNo;
        $paymentAmount = $inputs->paymentAmount;

        $paymentDate = $inputs->paymentDate;
        $paymentDate = date("d/m/Y");

        /*For testing on staging server start */
        //$janaadhaarId = '4576131686'; $janaadhaarMemId = '97198484640';
        //janaadhaarMemId:97198484640,44872500818,61183401890,29846086449,66955556662
        /*For testing on staging server end */

        //prod response <root><requestId>ss$986310240</requestId><cmsg></cmsg><transaction><transactionId>717605</transactionId><isSaved>Y</isSaved><msg></msg></transaction></root>
        //$url = 'https://janapp.rajasthan.gov.in/Service/action/transaction?scheme=SS&';//prod server

        /* $url = 'http://172.21.245.45/Service/action/transaction?scheme=SS&';//test server
		$url .= 'reqXml=%3Croot%3E%3CTransaction%3E%3CentitlementId%3E' . $entitlementId .'%3C/entitlementId%3E%3CentitlementMemId%3E' . $entitlementMemId .'%3C/entitlementMemId%3E%3CjanaadhaarId%3E' . $janaadhaarId .'%3C/janaadhaarId%3E%3CjanaadhaarMemId%3E' . $janaadhaarMemId .'%3C/janaadhaarMemId%3E%3CtransactionId%3E' . $transactionId .'%3C/transactionId%3E%3CdueTransactionId%3E' . $dueTransactionId .'%3C/dueTransactionId%3E%3CaadharNo%3E' . $aadharNo .'%3C/aadharNo%3E%3Ceid%3E%3C/eid%3E%3Cscheme%3EHEADM%3C/scheme%3E%3CbankAccNo%3E%3C/bankAccNo%3E%3Cifsc%3E%3C/ifsc%3E%3Cmicr%3E%3C/micr%3E%3CpaymentAmount%3E' . $paymentAmount .'%3C/paymentAmount%3E%3CpaymentDate%3E' . $paymentDate .'%3C/paymentDate%3E%3Cstatus%3ESuccess%3C/status%3E%3C/Transaction%3E%3C/root%3E'; */

        $domain = "rsosadmission.rajasthan.gov.in";
        if (@$_SERVER['HTTP_HOST']) {
            if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                $domain = $_SERVER['HTTP_HOST'];
            }
        }
        $url = 'http://172.21.245.45/Service/action/transaction?scheme=SS&';//test server

        if ($domain == "rsosadmission.rajasthan.gov.in") {
            $url = 'https://janapp.rajasthan.gov.in/Service/action/transaction?scheme=SS&';//prod server
        }
        $url .= 'reqXml=<root><Transaction><entitlementId>' . $entitlementId . '</entitlementId><entitlementMemId>' . $entitlementMemId . '</entitlementMemId><janaadhaarId>' . $janaadhaarId . '</janaadhaarId><janaadhaarMemId>' . $janaadhaarMemId . '</janaadhaarMemId><transactionId>' . $transactionId . '</transactionId><dueTransactionId>' . $dueTransactionId . '</dueTransactionId><aadharNo>' . $aadharNo . '</aadharNo><eid></eid><bankAccNo></bankAccNo><ifsc></ifsc><micr></micr><paymentAmount>' . $paymentAmount . '</paymentAmount><paymentDate>' . $paymentDate . '</paymentDate><status>Success</status></Transaction></root>';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $result = $json;
        // $result = json_decode($json,true);
        curl_close($curl);
        return $result;
    }


    public function getAdminSupplementaryStudentDetails($student_id = null, $exam_year = null, $exam_month = null)
    {
        $current_folder_year = $this->getCurrentYearFolderName();
        $master = Student::with('application', 'document', 'address', 'admission_subject', 'toc_subject')->with('exam_subject', function ($query) use ($exam_year, $exam_month) {
            $query->whereNull('deleted_at')->where('exam_year', $exam_year)->where('exam_month', $exam_month);
        })->where('id', $student_id)->first();
        $supplementaries = Supplementary::where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('student_id', $student_id)->first();


        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $subject_list = $this->subjectList();
        $combo_name = 'student_supplementary_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = encrypt($student_id);
        $type = ('supplementary');


        $fieldname = encrypt(@$supplementaries->marksheet_doc);
        $routename = 'documentdownload';
        $suppfees = Supplementary::where('student_id', $student_id)
            ->where('exam_year', $exam_year)
            ->where('exam_month', $exam_month)
            ->first();


        /* Com. subjects start */
        //$mastersupp = SupplementarySubject::where('student_id',$student_id)->where('is_additional_subject','<>', '',)->get();
        /* Com. subjects end */

        /* Already Passed subjects start */
        //$mastersuppexamsubject = ExamSubject::where('student_id',$student_id)->where('final_result','P',)->get()->toArray();
        /* Already Passed subjects end */

        /* Additional subjects start */
        //$master_subject_details1 = SupplementarySubject::where('student_id',$student_id)->where('is_additional_subject','IS NULL', null,)->get()->toArray();
        /* Additional subjects end */

        /* Com. subjects start */
        $mastersupp = Supplementary::
        join('supplementary_subjects', "supplementaries.id", "supplementary_subjects.supplementary_id")
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            //->whereNotNull('supplementary_subjects.is_additional_subject')
            ->where('supplementary_subjects.is_additional_subject', 1)
            // ->groupBy('supplementaries.student_id')
            ->orderBy('supplementaries.id', 'desc')
            ->get();
        /* Com. subjects end */

        /* Already Passed subjects start */
        $mastersuppexamsubjects = ExamSubject::where('student_id', $student_id)
            ->where('final_result', 'P')->latest('exam_year')->first('exam_year');

        if (@$mastersuppexamsubjects->exam_year && !is_null($mastersuppexamsubjects->exam_year)) {
            $mastersuppexamsubject = ExamSubject::where('student_id', $student_id)
                ->where('final_result', 'P')->where('exam_year', $mastersuppexamsubjects->exam_year)->get()->toArray();
        }

        /* Already Passed subjects end */
        /* Additional subjects start */
        $master_subject_details1 = Supplementary::
        join('supplementary_subjects', "supplementaries.id", "supplementary_subjects.supplementary_id")
            ->where('supplementaries.student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->where('supplementary_subjects.is_additional_subject', 'IS NULL', null)
            // ->groupBy('supplementaries.student_id')
            ->orderBy('supplementaries.id', 'desc')
            ->get()
            ->toArray();

        /* Additional subjects end */
        // @dd($mastersuppexamsubject);
        if (!is_null(@$mastersuppexamsubject) && !is_null(@$master_subject_details1)) {
            $result = array_merge(@$mastersuppexamsubject, @$master_subject_details1);
        }
        // echo "test"; die;

        $subject_list = $this->subjectList();

        $output['personalDetails'] = array(
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन संख्या (Enrollment No)",
                "value" => @$master->enrollment
            ),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            "mobile" => array(
                "fld" => "mobile",
                "label" => "मोबाइल (Mobile Number)",
                "value" => @$master->mobile
            ),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth)",
                "value" => date('d-m-Y', strtotime(@$master->dob)),
            ),
            "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$master->course
            ),
            "stream" => array(
                "fld" => "stream",
                "label" => "स्ट्रीम (Stream)",
                "value" => @$stream_id[@$master->stream]
            ),
            "adm_type" => array(
                "fld" => "adm_type",
                "label" => "प्रवेश प्रकार (Admission Type)",
                "value" => @$adm_types[@$master->adm_type]
            )

        );


        $output['FeesDetails'] = array(
            "subject_change_fees" => array(
                "fld" => "subject_change_fees",
                "label" => "विषय परिवर्तन शुल्क (Subject Change Fees)",
                "value" => @$suppfees->subject_change_fees
            ),
            "exam_fees" => array(
                "fld" => "exam_fees",
                "label" => "परीक्षा शुल्क(Exam Fees)",
                "value" => @$suppfees->exam_fees
            ),
            "practical_fees" => array(
                "fld" => "practical_fees",
                "label" => "प्रायौगिक शुल्क(Practical Fees)",
                "value" => @$suppfees->practical_fees
            ),
            "forward_fees" => array(
                "fld" => "forward_fees",
                "label" => "अग्रेषण शुल्क(Forwarding Fees)",
                "value" => @$suppfees->forward_fees
            ),
            "online_fees" => array(
                "fld" => "online_fees",
                "label" => "ऑनलाइन सेवा शुल्क(Online Services Fees)",
                "value" => @$suppfees->online_fees
            ),
            "late_fees" => array(
                "fld" => "late_fees",
                "label" => "विलम्ब शुल्क(Late Fees)",
                "value" => @$suppfees->late_fees
            ),
            "final_fees" => array(
                "fld" => "total_fees",
                "label" => "कुल शुल्क (Total Fees)",
                "value" => @$suppfees->total_fees
            )
        );

        if (!empty($result)) {
            foreach (@$result as $k => $v) {
                $subName = null;
                if (@$v['final_result'] && (@$v['final_result'] == "P" || @$v['final_result'] == "PASS" || @$v['final_result'] == "p")) {
                    $subName = "<span style='color:green;font-size:12px;'>" . @$subject_list[$v['subject_id']] . " (PASS)</span>";
                } else {
                    $subName = "<span style='color:red;font-size:12px;'>" . @$subject_list[$v['subject_id']] . "</span>";
                }
                $subName = @$subject_list[$v['subject_id']];

                $output['compulsorySubjectDetails'][] = array(
                    "fld" => "subject_id",
                    "label" => " (Subject  " . ($k + 1) . ")",
                    "value" => $subName
                );
            }
        }

        if (!empty($mastersupp)) {
            foreach (@$mastersupp as $k => $v) {
                $output['additionalSubjectDetails'][] = array(
                    "fld" => "subject_id",
                    "label" => " (Subject " . ($k + 1) . ")",
                    "value" => @$subject_list[@$v['subject_id']]
                );
            }
        }

        $lockedStatus = "No";
        if (@$suppfees->locksumbitted) {
            $lockedStatus = "Yes";
        }

        if (@$suppfees->locksubmitted_date) {
            $output['LockSubmttedDetails'] = array(
                "locksumbitted" => array(
                    "fld" => "locksumbitted ",
                    "label" => "लॉक और सबमिट किया गया है ? (Is Lock & Submitted)",
                    "value" => @$lockedStatus
                ),
                "locksubmitted_date" => array(
                    "fld" => "locksubmitted_date",
                    "label" => "लॉक और जमा करने की तिथि (Lock & Submitted Date)",
                    "value" => Carbon::createFromFormat('Y-m-d H:i:s', @$suppfees->locksubmitted_date)->format('d-m-Y H:i A')
                ),
            );
        }

        if (@$suppfees->submitted) {
            $output['TransactionDetails'] = array(
                "challan_tid" => array(
                    "fld" => "challan_tid ",
                    "label" => " चालान संख्या (Challan Number)",
                    "value" => @$suppfees->challan_tid
                ),
                "submitted" => array(
                    "fld" => "submitted",
                    "label" => "शुल्क जमा करने की तिथि( Fees Submitted Date)",
                    "value" => Carbon::createFromFormat('Y-m-d H:i:s', @$suppfees->submitted)->format('d-m-Y H:i A')
                ),
            );
        }

        $documents = $this->getSupplementaryStudentRequriedDocuments($student_id);
        $supplementaryDetails = Supplementary::with('SupplementarySubject')
            ->where('student_id', $student_id)
            ->where('supplementaries.exam_year', $exam_year)
            ->where('supplementaries.exam_month', $exam_month)
            ->first();

        if (@$documents) {
            foreach (@$documents as $k => $v) {
                $output['documentDetails'][$k] = array(
                    "fld" => $k,
                    "label" => $v,
                    "value" => "<a title='Click here to verify " . @$v . "' href=" . url(@$routename . "/" . $studentDocumentPath . "/" . @$type . "/" . $fieldname . "/") . "><span class='material-icons'>link</span></a>"
                    // "value" => "<a target='_blank' title='Click here to verify " . @$v ."' href=". url('public/'.$studentDocumentPath . "/" . @$supplementaryDetails->$k . "/" . ""   ). "><span class='material-icons'>link</span></a>"
                );
            }
        }


        $output = array(
            "personalDetails" => array(
                "seciontLabel" => "व्यक्तिगत विवरण  (Personal Details)",
                "data" => @$output['personalDetails']
            ),
            "compulsorySubjectDetails" => array(
                "seciontLabel" => "अनिवार्य विषय विवरण  (Compulsory Subjects Details)",
                "data" => @$output['compulsorySubjectDetails']
            ),
            "additionalSubjectDetails" => array(
                "seciontLabel" => "अतिरिक्त विषय विवरण  (Additional Subject Details)",
                "data" => @$output['additionalSubjectDetails']
            ),
            "FeesDetails" => array(
                "seciontLabel" => "शुल्क विवरण  (Fees Details)",
                "data" => @$output['FeesDetails']
            ),
            "documentDetails" => array(
                "seciontLabel" => "दस्तावेज़ विवरण ( Document Details )",
                "data" => @$output['documentDetails']
            ),
            "LockSubmttedDetails" => array(
                "seciontLabel" => "लॉक और सबमिट किए गए विवरण  ( Lock & Submitted  Details )",
                "data" => @$output['LockSubmttedDetails']
            ),
            "TransactionDetails" => array(
                "seciontLabel" => "लेनदेन का विवरण ( Transaction Details )",
                "data" => @$output['TransactionDetails']
            ),

        );

        return $output;
    }

    public function _getEnrollmentListMappedWithSSOId($ssoid = null)
    {
        $enrollments = Student::where("ssoid", $ssoid)
		//->whereNotNull('enrollment')
		->orderBy('id', 'desc')->whereNull('deleted_at')->pluck('enrollment', 'id');
        if (@$enrollments) {
            $enrollments = $enrollments->toArray();
        }
        $temp_student_id = Student::where('ssoid', $ssoid)->orderby('id', 'desc')->first();
        $temp_student_id = @$temp_student_id->id;
        Session::put('temp_student_id', $temp_student_id);
        return $enrollments;
    }

    public function _getEnrollmentListMappedWithUsername($username = null)
    {
        $enrollments = Student::where("username", $username)
		//->whereNotNull('enrollment')
		->orderBy('id', 'desc')->whereNull('deleted_at')->pluck('enrollment', 'id');
        if (@$enrollments) {
            $enrollments = $enrollments->toArray();
        }
        return $enrollments;
    }


    public function _getEnrollmentListLabeleMappedWithUsername($username = null)
    {

        $finalArr = array();
        $enrollments = Student::with('application')->where("username", $username)
		//->whereNotNull('enrollment')
		->orderBy('id', 'desc')->whereNull('deleted_at')->get();
        if (@$enrollments) {
            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'stream_id';
            $stream_id = $this->master_details($combo_name);
            $combo_name = 'adm_type';
            $adm_types = $this->master_details($combo_name);
            foreach ($enrollments as $k => $value) {
                //$mainEnrollment = $value->enrollment;
				$mainEnrollment = $value->id;
                // $value->enrollment = str_pad(substr($value->enrollment, -6), strlen($value->enrollment), '*', STR_PAD_LEFT);

                if ($value->exam_year <= 123) {
                    if (@$value->application->locksumbitted && $value->application->locksumbitted == 1) {

                    } else {
                        continue;
                    }
                } else {//&& @$value->is_eligible == 1
                    if (@$value->application->locksumbitted && $value->application->locksumbitted == 1 ) {

                    } else {
                        continue;
                    }
                }

                $finalArr[$mainEnrollment] = $value->enrollment . "-" . @$course[@$value->course] . "-" . @$stream_id[@$value->exam_month] . " - " . @$adm_types[@$value['adm_type']];;
            }
        }
        return $finalArr;
    }

    public function _getEnrollmentListLabeleMappedWithSSOId($ssoid = null)
    {

        $finalArr = array();
        $enrollments = Student::with('application')->where("ssoid", $ssoid)
		//->whereNotNull('enrollment')
		->orderBy('id', 'desc')->whereNull('deleted_at')->get();
        if (@$enrollments) {
            $combo_name = 'course';
            $course = $this->master_details($combo_name);
            $combo_name = 'stream_id';
            $stream_id = $this->master_details($combo_name);
            $combo_name = 'adm_type';
            $adm_types = $this->master_details($combo_name);
            foreach ($enrollments as $k => $value) {
                //$mainEnrollment = @$value->enrollment;
				$mainEnrollment = @$value->id;

                // $value->enrollment = str_pad(substr($value->enrollment, -6), strlen($value->enrollment), '*', STR_PAD_LEFT);

                if ($value->exam_year <= 123) {
                    if (@$value->application->locksumbitted && $value->application->locksumbitted == 1) {

                    } else {
                        continue;
                    }
                } else {
					//&& @$value->is_eligible == 1
                    if (@$value->application->locksumbitted && $value->application->locksumbitted == 1 ) {

                    } else {
                        continue;
                    }
                }

                $finalArr[$mainEnrollment] = @$value->enrollment . "-" . @$course[@$value->course] . "-" . @$stream_id[@$value->exam_month] . " - " . @$adm_types[@$value['adm_type']];;
            }
        }
		
        return $finalArr;
    }

    public function _getCurrentStudentLogoutandLogin()
    {
        $selected_student_enrollment_by_student_ajax = @Session::get('selected_student_enrollment_by_student_ajax');
        $ssoid = Auth::guard('student')->user()->ssoid;
        $role_id = Session::get('role_id');
        Session::flush();
        Auth::logout();
        if (@$selected_student_enrollment_by_student_ajax && $selected_student_enrollment_by_student_ajax != null) {
            $password = '123456789';
            $getstudentssoid = Student::where('ssoid', $ssoid)->where('id', $selected_student_enrollment_by_student_ajax)->first('ssoid');
            if (!empty($getstudentssoid)) {
                $studentCredentials = $credentials = (['id' => $selected_student_enrollment_by_student_ajax, 'password' => $password]);
				
                if (Auth::guard('student')->attempt($studentCredentials)) {
                    Session::put('role_id', $role_id);
                    //$enrollmentNumber = Auth::guard('student')->user()->enrollment;
					$enrollmentNumber = Auth::guard('student')->user()->id;
                    Session::put("selected_student_enrollment_by_student", $enrollmentNumber);
                    //Auth::guard('student')->user()->role_id = Session::get('role_id');
                    return 'dashboard';
                } else {
                    return 'landing';
                }
            } else {
                return 'landing';
            }
        }
    }

    public function _movementOfSuppDocuemnts($supp_id = null, $estudent_id = null, $course = null)
    {
        $student_id = Crypt::decrypt($estudent_id);
        $combo_name = 'current_folder_year';
        $current_folder_year = $this->master_details($combo_name);
        $current_folder_year = $this->getCurrentYearFolderName();
        $combo_name = 'student_supplementary_document_path';
        $student_document_path = $this->master_details($combo_name);
        $exam_month = Config::get('global.supp_current_admission_exam_month');

        $suppVerifcationData = DB::table('supplementary_verifications')->where('supplementary_id', @$supp_id)->whereNull('deleted_at')->orderBy('id', 'desc', 'aicenter_rejected_marksheet_document', 'department_rejected_marksheet_document')->first();

        $supplementaryDetails = DB::table('supplementary_verification_documents')->where('supplementary_id', $supp_id)->orderBy('id', 'DESC')->first(['id', 'supp_doc', 'sec_marksheet_doc']);
        $custom_data = null;
        if (@$suppVerifcationData->aicenter_rejected_marksheet_document == 3 || @$suppVerifcationData->department_rejected_marksheet_document == 3) {
            $custom_data = array(
                'marksheet_doc' => $supplementaryDetails->supp_doc,
                'sec_marksheet_doc' => $supplementaryDetails->sec_marksheet_doc,
            );

        } elseif (@$suppVerifcationData->aicenter_rejected_marksheet_document == 2 || @$suppVerifcationData->department_rejected_marksheet_document == 2) {
            $custom_data = array(
                'marksheet_doc' => $supplementaryDetails->supp_doc,
            );

        } elseif (@$suppVerifcationData->aicenter_rejected_marksheet_document == 1 || @$suppVerifcationData->department_rejected_marksheet_document == 1) {
            if ($course == 10) {
                $custom_data = array(
                    'marksheet_doc' => $supplementaryDetails->supp_doc,
                );
            }
            if ($course == 12) {
                $custom_data = array(
                    'sec_marksheet_doc' => $supplementaryDetails->sec_marksheet_doc,
                );
            }
        }


        if (!empty($custom_data)) {

            $isValid = true;

            if (!empty($custom_data['marksheet_doc'])) {
                $basePathStudentDocumentPathOld = $student_document_path[2] . $current_folder_year . '/' . $exam_month . '/' . $student_id . '/';
                $basePathStudentDocumentPathNew = $student_document_path[1] . $current_folder_year . '/' . $exam_month . '/' . $student_id . '/';
                $basePathStudentDocumentPathOld = $basePathStudentDocumentPathOld . $custom_data['marksheet_doc'];
                $folder_path = public_path($basePathStudentDocumentPathNew);
                File::makeDirectory($folder_path, $mode = 0777, true, true);
                $basePathStudentDocumentPathNew = $basePathStudentDocumentPathNew . $custom_data['marksheet_doc'];
                $new_path = public_path($basePathStudentDocumentPathNew);
                $old_path = public_path($basePathStudentDocumentPathOld);

                if (file_exists($old_path)) {
                    $move = File::copy($old_path, $new_path);
                } else {
                    $isValid = false;
                }
            }
            if (!empty($custom_data['sec_marksheet_doc'])) {
                $basePathStudentDocumentPathOld = $student_document_path[2] . $current_folder_year . '/' . $exam_month . '/' . $student_id . '/';
                $basePathStudentDocumentPathNew = $student_document_path[1] . $current_folder_year . '/' . $exam_month . '/' . $student_id . '/';
                $basePathStudentDocumentPathOld = $basePathStudentDocumentPathOld . $custom_data['sec_marksheet_doc'];
                $folder_path = public_path($basePathStudentDocumentPathNew);
                File::makeDirectory($folder_path, $mode = 0777, true, true);
                $basePathStudentDocumentPathNew = $basePathStudentDocumentPathNew . $custom_data['sec_marksheet_doc'];
                $new_path = public_path($basePathStudentDocumentPathNew);
                $old_path = public_path($basePathStudentDocumentPathOld);

                if (file_exists($old_path)) {
                    $move = File::copy($old_path, $new_path);
                } else {
                    $isValid = false;
                }
            }
            if ($isValid) {
                $supplementariesupdatedata = DB::table('supplementaries')->where('id', @$supp_id)->where('student_id', $student_id)->update($custom_data);
            }
        }
        return true;
    }

    public function sendSupplementaryDocuemntVerificationSMS($mobile = null, $level_role_id = null, $type = null)
    {
        $sms = null;
        $templateID = null;

        $combo_name = 'exam_month';
        $exam_months = $this->master_details($combo_name);
        $supp_current_admission_exam_month = Config::get('global.supp_current_admission_exam_month');
        $month_year_label = @$exam_months[@$supp_current_admission_exam_month] . " " . now()->year;
        if ($type == 2) {//approveed
            $templateID = "1107171024459834708";

            if ($level_role_id == Config::get('global.aicenter_id')) {
                $sms = "Dear applicant,We are pleased to inform you that the document/s submitted for your RSOS Supplementary " . @$month_year_label . " Exam application has/have been approved by the Ai Centre.-RSOS,GoR";
            }
            if ($level_role_id == Config::get('global.examination_department')) {
                $sms = "Dear applicant,We are pleased to inform you that the document/s submitted for your RSOS Supplementary " . @$month_year_label . " Exam application has/have been approved by the RSOS Department.-RSOS,GoR";
            }
        } else if ($type == 3) {//rejected
            $templateID = "1107171024461784373";
            if ($level_role_id == Config::get('global.aicenter_id')) {
                $sms = "Dear applicant,We regret to inform you that the document/s submitted for your RSOS Supplementary " . @$month_year_label . " Exam application has/have been marked deficient by the Ai Centre. Please proceed to re-upload the correct document/s by logging into your SSOID to complete your application process.-RSOS,GoR";
            }
            if ($level_role_id == Config::get('global.examination_department')) {
                $sms = "Dear applicant,We regret to inform you that the document/s submitted for your RSOS Supplementary " . @$month_year_label . " Exam application has/have been marked deficient by the RSOS Department. Please proceed to re-upload the correct document/s by logging into your SSOID to complete your application process.-RSOS,GoR";
            }
        }
        // echo $sms;die;
        if (@$templateID && @$mobile) {
            $smsStatus = $this->_sendSMS($mobile, $sms, $templateID);
        }
        return true;
    }

    public function sendSupplementaryDocuemntVerificationHindiSMS($mobile = null, $level_role_id = null, $type = null)
    {
        $sms = null;
        $templateID = null;

        $combo_name = 'exam_month';
        $exam_months = $this->master_details($combo_name);
        $supp_current_admission_exam_month = Config::get('global.supp_current_admission_exam_month');
        $month_year_label = @$exam_months[@$supp_current_admission_exam_month] . " " . now()->year;
        if ($type == 2) {//approveed
            $templateID = "1107171024457443186";
            if ($level_role_id == Config::get('global.aicenter_id')) {
                $sms = "प्रिय आवेदक,
				आपको सूचित किया जाता है कि आपके आरएसओएस पूरक " . @$month_year_label . " परीक्षा आवेदन के लिए प्रस्तुत किए गए दस्तावेज़/दस्तावेजों को एआई केंद्र द्वारा स्वीकृत कर दिया हैं।-आरएसओएस,GoR";
            }
            if ($level_role_id == Config::get('global.examination_department')) {
                $sms = "प्रिय आवेदक,
				आपको सूचित किया जाता है कि आपके आरएसओएस पूरक " . @$month_year_label . " परीक्षा आवेदन के लिए प्रस्तुत किए गए दस्तावेज़/दस्तावेजों को आरएसओएस विभाग द्वारा स्वीकृत कर दिया हैं।-आरएसओएस,GoR";
            }
        } else if ($type == 3) {//rejected
            $templateID = "1107171024461784373";
            if ($level_role_id == Config::get('global.aicenter_id')) {
                $sms = "Dear applicant,We regret to inform you that the document/s submitted for your RSOS Supplementary " . @$month_year_label . " Exam application has/have been marked deficient by the Ai Centre. Please proceed to re-upload the correct document/s by logging into your SSOID to complete your application process.-RSOS,GoR";
            }
            if ($level_role_id == Config::get('global.examination_department')) {
                $sms = "Dear applicant,We regret to inform you that the document/s submitted for your RSOS Supplementary " . @$month_year_label . " Exam application has/have been marked deficient by the RSOS Department. Please proceed to re-upload the correct document/s by logging into your SSOID to complete your application process.-RSOS,GoR";
            }
        }
        // echo $sms;die;
        if (@$templateID && @$mobile) {
            $smsStatus = $this->_sendSMS($mobile, $sms, $templateID);
        }
        return true;
    }

    public function _checksecure_token($secure_token = null, $ssoid = null)
    {
        $ssoid = $ssoid . date("d_m");

        if (@$secure_token && @$ssoid) {
            $orgVal = Crypt::decrypt($secure_token);
            if ($orgVal == $ssoid) {
                return true;
            }
        }
        return false;
    }


    public function getIsAllowToShowAdmitCardDownloadForStudent($student_allotment = null)
    {

        $status = false;
        if (@$student_allotment->id) {
            $objController = new Controller();
            $combo_name = 'student_download_admit_card_start_date';
            $combo_name2 = 'student_download_admit_card_end_date';
            $hall_ticket_start_date_arr = $this->master_details($combo_name);
            $hall_ticket_start_end_arr = $this->master_details($combo_name2);

            if (strtotime(date("Y-m-d H:i:s")) >= strtotime($hall_ticket_start_date_arr[1]) && strtotime(date("Y-m-d H:i:s")) <= strtotime($hall_ticket_start_end_arr[1])) {
                $status = true;
            }
        }
        return $status;
    }


    public function ExamRevalSubjectValidation($request)
    {
        $min = 0;
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';

        if (@$request->reval_type) {
        } else {
            $fld = 'reval_type';
            $errMsg .= 'Please select reval application type.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }
        if (@$request->subject_id) {
            $coutner = count($request->subject_id);
        } else {
            $fld = 'subject_id';
            $errMsg .= 'Please select minimum 1 subject.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        }
        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;

        return $response;
    }

    public function _revalSubjectFeesCalculate($numberofSubject = 0, $reval_type = null)
    {
        $response = array(
            "reval_fees" => 0,
            "numberofSubject" => 0,
            "reval_late_fees" => 0,
            "total_fees" => 0
        );

        if ($reval_type != null) {
            $combo_name = 'reval_per_subject_fee';
            $reval_fees = $this->master_details($combo_name);
            $reval_fees = $reval_fees[$reval_type];

            $combo_name = 'reval_late_fees';
            $reval_late_fees = $this->master_details($combo_name);
            $reval_late_fees = @$reval_late_fees[$reval_type];


            if ($numberofSubject > 0) {
                $total_fees = ($reval_fees * @$numberofSubject) + $reval_late_fees;
                $response = array(
                    "reval_fees" => $reval_fees,
                    "numberofSubject" => $numberofSubject,
                    "reval_late_fees" => $reval_late_fees,
                    "total_fees" => $total_fees,
                );
            }
        }
        return $response;
    }


    public function _checkIsAllowStudentForRevalApplicationForm($student_id = null)
    {

        $custom_component_obj = new CustomComponent;
        $isAllowForRevalApplicaitonForm = false;

        $auth_user_id = null;
        $checkRevalAllow = $custom_component_obj->revalAllowOrNot();
        $isStudent = $custom_component_obj->_getIsStudentLogin();
        $conditions = array();

        if (@$isStudent) {
            $student_id = $auth_user_id = @Auth::guard('student')->user()->id;
            $combo_name = 'reval_exam_year';
            $reval_exam_year = $this->master_details($combo_name);
            $combo_name = 'reval_exam_month';
            $reval_exam_month = $this->master_details($combo_name);
            $reval_exam_year = $reval_exam_year[1];
            $reval_exam_month = $reval_exam_month[1];

            if ($checkRevalAllow == true && @$student_id) {
                $conditions = ['student_id' => $student_id, 'exam_year' => $reval_exam_year,
                    'exam_month' => $reval_exam_month];
                $stu_result = ExamResult::where($conditions)
                    ->where("is_temp_examresult", "!=", "111")
                    ->count();
                if ($stu_result > 0) {
                    $isAllowForRevalApplicaitonForm = true;
                }
            }
        } else {
            $custom_component_obj = new CustomComponent;
            $role_id = @Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            $developer_admin = Config::get("global.developer_admin");
            $current_session = CustomHelper::_get_selected_sessions();
            $combo_name = 'admission_sessions';
            $admission_sessions = $this->master_details($combo_name);
            $combo_name = 'exam_month';
            $exam_month = $this->master_details($combo_name);
            $combo_name = 'application_dashboard';
            $application_dashboard = $this->master_details($combo_name);
            if ($role_id == $super_admin_id || $role_id == $developer_admin) {
                $isAllowForRevalApplicaitonForm = true;
            }
        }
        return $isAllowForRevalApplicaitonForm;
    }


    public function _checkIsAllowStudentForFSDVApplicationForm($student_id = null)
    {

        $custom_component_obj = new CustomComponent;
        $isAllowForFsdvApplicaitonForm = false;

        $auth_user_id = null;
        $checkRevalAllow = $custom_component_obj->revalAllowOrNot();
        $isStudent = $custom_component_obj->_getIsStudentLogin();

        $conditions = array();
        $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
        $current_exam_month_id = Config::get("global.form_current_exam_month_id");

        if (@$isStudent) {
            $student_id = $auth_user_id = @Auth::guard('student')->user()->id;

            if ($checkRevalAllow == true && @$student_id) {
                $conditions = ['id' => $student_id, 'exam_year' => $current_admission_session_id, 'exam_month' => $current_exam_month_id];
                $stu_result = Student::where($conditions)->count();
                if ($stu_result > 0) {
                    $isAllowForFsdvApplicaitonForm = true;
                }
            }
        } else {
            $custom_component_obj = new CustomComponent;
            $role_id = @Session::get('role_id');
            $super_admin_id = Config::get("global.super_admin_id");
            $developer_admin = Config::get("global.developer_admin");
            if ($role_id == $super_admin_id || $role_id == $developer_admin) {
                $isAllowForFsdvApplicaitonForm = true;
            }
        }
        return $isAllowForFsdvApplicaitonForm;
    }

    public function createpracticalexaminerslotValidation1235($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;

        $validators = null;
        $StudentPracticalSlots = new StudentPracticalSlots; /// create model object
        $validators[] = Validator::make($request->all(), $StudentPracticalSlots->rules, $StudentPracticalSlots->uersmakerulesmessage);
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;
    }


    public function createpracticalexaminerslotValidation($request)
    {
        $min = 0;
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $start_date = str_replace("T", " ", @$request->date_time_start) . ":00";
        $end_date = str_replace("T", " ", @$request->date_time_end) . ":00";

        $start = strtotime($request->date_time_start);
        $end = strtotime($request->date_time_end);

        $hours = ($end - $start) / (60 * 60);
        $mins = ($end - $start) / 60;

        if (empty($request->date_time_start)) {
            $fld = 'date_time_start';
            $errMsg .= 'Date Time Start is required';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        } elseif (empty($request->date_time_end)) {
            $fld = 'date_time_end';
            $errMsg .= 'Date Time End is required.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        } elseif (empty($request->batch_student_count)) {
            $fld = 'batch_student_count';
            $errMsg .= 'Batch Student Count is required.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        } elseif (@$request->date_time_start >= $request->date_time_end) {
            $fld = 'batch_student_count';
            $errMsg .= 'Please select slot end time greater than slot start time ';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        } elseif (@$request->date_time_start < carbon::now() || @$request->date_time_end < carbon::now()) {
            $fld = 'batch_student_count';
            $errMsg .= ' Slot can\'t be create in past time. ';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        } elseif (@$hours > 12) {
            $fld = 'batch_student_count';
            $errMsg .= 'Please select slot between 8:00 AM to 8:00 PM today date.';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        } elseif (@$mins < 30) {
            $fld = 'batch_student_count';
            $errMsg .= 'Please select minimum 30 minutes slot duration';
            $errors = $errMsg;
            $validator->getMessageBag()->add($fld, $errMsg);
            $isValid = false;
        } elseif (@$request->date_time_start && $request->date_time_end) {
            $current_exam_year = CustomHelper::_get_selected_sessions();
            $current_exam_month = Config::get("global.current_exam_month_id");
            $user_examiner_map_id = Crypt::decrypt($request->user_examiner_map_id);

            $query = DB::select("select count(*) as cnt from rs_student_practical_slots where (date_time_start >= '$start_date'and date_time_end <= '$end_date' and exam_year = '$current_exam_year' and exam_month = '$current_exam_month' and examiner_mapping_id ='$user_examiner_map_id') and rs_student_practical_slots.deleted_at is null");

            if (@$query['0']->cnt == 0) {
                $query = DB::select("select count(*) as cnt from rs_student_practical_slots where (date_time_start < '$start_date' and date_time_end >'$start_date' or date_time_start < '$end_date' and date_time_end > '$end_date' and exam_year = '$current_exam_year'and exam_month = '$current_exam_month' ) and examiner_mapping_id ='$user_examiner_map_id' and rs_student_practical_slots.deleted_at is null");
            }


            if (@$query['0']->cnt == 1) {

                $fld = 'batch_student_count';
                $errMsg .= 'Selected time slot already created';
                $errors = $errMsg;
                $validator->getMessageBag()->add($fld, $errMsg);
                $isValid = false;
            }
        }

        $response['isValid'] = $isValid;
        $response['errors'] = $errors;
        $response['validator'] = $validator;
        return $response;
    }

    public function start_time()
    {
        /* starting part start */
        return $start_time = microtime(true);
        /* starting part end */
    }

    public function end_time($start_time = null)
    {
        /* ending part start */
        $request_data = @$_SERVER["REQUEST_URI"];
        $end_time = microtime(true);
        $this->save_log_load_time($start_time, $end_time, $request_data);
        return true;
        /* ending part end */
    }

    public function save_log_load_time($start_time = null, $end_time = null, $request_data = null)
    {
        if ($start_time == null) {
            $start_time = microtime(true);
        }

        if ($end_time == null) {
            $end_time = microtime(true);
        }

        $total_time = $end_time - $start_time;

        $milliseconds = intval($total_time * 1000) . " ms";

        $start_time = date("Y-m-d H:i:s", substr($start_time, 0, 10));
        $end_time = date("Y-m-d H:i:s", substr($end_time, 0, 10));

        $log_data = [
            'request_time' => $start_time,
            'response_time' => $end_time,
            'total_time' => $total_time,
            'milliseconds' => $milliseconds,
            'request_data' => $request_data
        ];
        $log_data_string = json_encode($log_data);
        $log_data_string .= "\n";
        file_put_contents(public_path() . '\apitimelogs\api_request_logs_' . date("d_m_Y_H") . '.json', $log_data_string, FILE_APPEND);
        return true;
    }

    public function checkIsStudentVerificationAllowAtAICenter($student_payment_datetime = null)
    {
        $status = true;
        return $status;
        // dd($student_payment_datetime);
        $objController = new Controller();
        $combo_name = 'aicenter_fresh_form_document_verification_start_date';
        $combo_name2 = 'aicenter_fresh_form_document_verification_end_date';
        $aicenter_fresh_form_document_verification_start_date = $this->master_details($combo_name);
        $aicenter_fresh_form_document_verification_end_date = $this->master_details($combo_name2);

        if (strtotime(date("Y-m-d H:i:s")) >= strtotime($aicenter_fresh_form_document_verification_start_date[1]) && strtotime(date("Y-m-d H:i:s")) <= strtotime($aicenter_fresh_form_document_verification_end_date[1])) {
        } else {
            $status = false;
        }
        if (@$status && @$student_payment_datetime) {
            $combo_name = 'student_fresh_form_verification_allowed_number_of_days';
            $student_fresh_form_verification_allowed_number_of_days = $this->master_details($combo_name);
            $student_fresh_form_verification_allowed_number_of_days = @$student_fresh_form_verification_allowed_number_of_days[1];
            $student_payment_datetime = date("y-m-d H:m:s", strtotime("$student_payment_datetime +" . @$student_fresh_form_verification_allowed_number_of_days . " days"));
            $now = date("y-m-d H:m:s");
            // echo $student_payment_datetime . "    " . $now;
            // die;
            $datediff = strtotime($student_payment_datetime) - strtotime($now);
            // dd($datediff);
            if ($datediff < 0) {
                $status = false;
            }
        }
        return $status;
    }

    public function checkIsStudentVerificationAllowAtVerifier($student_payment_datetime = null)
    {
        $status = true;
        return $status;
        // dd($student_payment_datetime);
        $objController = new Controller();
        $combo_name = 'verifier_fresh_form_document_verification_start_date';
        $combo_name2 = 'verifier_fresh_form_document_verification_end_date';
        $verifier_fresh_form_document_verification_start_date = $this->master_details($combo_name);
        $verifier_fresh_form_document_verification_end_date = $this->master_details($combo_name2);

        if (strtotime(date("Y-m-d H:i:s")) >= strtotime($verifier_fresh_form_document_verification_start_date[1]) && strtotime(date("Y-m-d H:i:s")) <= strtotime($verifier_fresh_form_document_verification_end_date[1])) {
        } else {
            $status = false;
        }
        if (@$status && @$student_payment_datetime) {
            $combo_name = 'student_fresh_form_verification_allowed_number_of_days';
            $student_fresh_form_verification_allowed_number_of_days = $this->master_details($combo_name);
            $student_fresh_form_verification_allowed_number_of_days = @$student_fresh_form_verification_allowed_number_of_days[1];
            $student_payment_datetime = date("y-m-d H:m:s", strtotime("$student_payment_datetime +" . @$student_fresh_form_verification_allowed_number_of_days . " days"));
            $now = date("y-m-d H:m:s");
            // echo $student_payment_datetime . "    " . $now;
            // die;
            $datediff = strtotime($student_payment_datetime) - strtotime($now);
            // dd($datediff);
            if ($datediff < 0) {
                $status = false;
            }
        }
        return $status;
    }

    public function checkIsPaymentRecivedOrNot($student_id = null)
    {
        return $status = true;
        $objController = new Controller();
        $combo_name = 'department_fresh_form_document_verification_start_date';
        $combo_name2 = 'department_fresh_form_document_verification_end_date';
        $department_fresh_form_document_verification_start_date = $this->master_details($combo_name);
        $department_fresh_form_document_verification_end_date = $this->master_details($combo_name2);

        if (strtotime(date("Y-m-d H:i:s")) >= strtotime($department_fresh_form_document_verification_start_date[1]) && strtotime(date("Y-m-d H:i:s")) <= strtotime($department_fresh_form_document_verification_end_date[1])) {
        } else {
            $status = false;
        }

        if (@$status && @$student_id) {
            $super_admin_id = Config::get("global.super_admin_id");

            $documentverificationsCount = DocumentVerification::where('student_id', $student_id)->orderby("id", "DESC")->first();
            // echo "ddddddd";dd($documentverificationsCount);
            $combo_name = 'department_fresh_form_rejection_charge_amount';
            $department_fresh_form_rejection_charge_amount = $this->master_details($combo_name);
            $studentamount = $department_fresh_form_rejection_charge_amount[1];
            if ($studentamount <= 0) {

            } else {
                if (@$documentverificationsCount->role_id == $super_admin_id && @$documentverificationsCount->challan_tid == null) {
                    $status = false;
                }
            }
        }
        return $status;
    }

    public function checkIsClarificationMakePaymentAllowOrNot($student_id = null)
    {
        $status = true;
        $objController = new Controller();
        $combo_name = 'dept_rejected_payment_document_verification_start_date';
        $combo_name2 = 'dept_rejected_payment_document_verification_end_date';
        $dept_rejected_payment_document_verification_start_date = $this->master_details($combo_name);
        $dept_rejected_payment_document_verification_end_date = $this->master_details($combo_name2);

        if (strtotime(date("Y-m-d H:i:s")) >= strtotime($dept_rejected_payment_document_verification_start_date[1]) && strtotime(date("Y-m-d H:i:s")) <= strtotime($dept_rejected_payment_document_verification_end_date[1])) {
        } else {
            $status = false;
        }
        return $status;
    }

    public function getDocumentsContent($docIputs = array())
    {
        $result = array();
        foreach (@$docIputs as $k => $v) {
            if ($v != 'label') {
                $combo_name = 'doc_content_' . $v;
                $result[$v] = $this->master_details($combo_name);
                $result[$v] = @$result[$v][1];
            }
        }
        return ($result);
    }

    public function getCurrentYearFolderNamematerialschecklist($exam_year = null)
    {
        if (!empty($exam_year)) {
            $combo_name = 'current_folder_year';
            $current_folder_years = $this->master_details($combo_name);
            return $current_folder_year = @$current_folder_years[@$exam_year];
        }

    }

    public function toNum($alpha)
    {
        $num = 0;
        $len = strlen($alpha);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + ord($alpha[$i]) - 64; // 64 is ASCII for 'A' - 1
        }
        return $num;
    }

    // Function to convert a number to alphanumeric representation (A-Z)

    public function getVerificationDetailedLabels($status = 1)
    {
        $condtions = null;
        $result = array();
        if (!empty($status)) {
            $condtions['status'] = $status;
        }
        $mainTable = "verification_labels";
        $cacheName = $mainTable . "_" . $status;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $mainTable) {
                $verificationLabels = [];
                $result = DB::table($mainTable)->where($condtions)->get(['hindi_name', 'name', 'id']);
                if (@$result) {
                    foreach ($result as $k => $v) {
                        $fld = 'name';
                        $verificationLabels[$v->id][$fld] = $v->$fld;
                        $fld = 'hindi_name';
                        $verificationLabels[$v->id][$fld] = $v->$fld;
                    }
                }
                return $verificationLabels;
            });
        }
        return $result;
    }

    // Function to convert an alphanumeric representation (A-Z) to a number

    public function getVerificationMainDocumentLowerDetailedLabels($verificationLabels = null, $adm_type = null, $course = null)
    {
        $condtions = null;
        $result = array();
        $status = 1;
        if (!empty($status)) {
            $condtions['status'] = $status;
        }
        $mainTable = "verification_masters";
        $cacheName = $mainTable . "_" . $status;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $adm_type, $course, $verificationLabels, $mainTable) {

                $details = [];
                foreach ($verificationLabels as $k => $v) {
                    foreach ($v as $ik => $iv) {
                        if ($iv == 2) {
                            $details[$k][$ik] = $iv;
                        }
                    }
                }
                $keysDetails = array_keys($details);
                $condtions['adm_type'] = $adm_type;
                $condtions['course'] = $course;
                $finalList = [];
                if (@$details) {
                    foreach ($details as $k => $v) {
                        foreach ($v as $ik => $iv) {

                            $tempDetails = DB::table($mainTable)->where($condtions)
                                ->where('main_document_id', $k)
                                ->where('field_id', $ik)
                                ->first(['field_name']);
                            $finalList[$k][$ik] = $tempDetails->field_name;
                        }
                    }
                }
                return $finalList;
            });
        }
        return $result;
    }

    public function getVerificationLabel($status = null, $extra = null)
    {
        $condtions = null;
        $result = array();
        if (!empty($status)) {
            $condtions['status'] = $status;
        }
        $mainTable = "verification_labels";
        $cacheName = $mainTable . "_" . $status;
        Cache::forget($cacheName);
        if (Cache::has($cacheName)) {
            $result = Cache::get($cacheName);
        } else {
            $result = Cache::rememberForever($cacheName, function () use ($condtions, $extra, $mainTable) {
                if ($extra == true) {
                    $result = VerificationLabel::select(
                        DB::raw("CONCAT(real_name,' ',document_field_name) AS name"), 'id')
                        ->whereNotNull('document_field_name')
                        ->where($condtions)
                        ->pluck('name', 'id');

                } else {
                    $result = DB::table($mainTable)->whereNotNull('document_field_name')->where($condtions)->get()->pluck('document_field_name', 'id');
                }
                return $result;
            });
        }
        return $result;
    }

    public function isValidJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function _movementOfFreshDocuemnts($student_id = null, $master_id = null)
    {
        try {
            $isValid = false;
            $custom_data = [];
            $current_admission_session_id = Config::get("global.form_admission_academicyear_id");
            $current_exam_month_id = Config::get("global.form_current_exam_month_id");

            $combo_name = 'marked_rejected_move_documents';
            $marked_rejected_move_documents = $this->master_details($combo_name);
            $markedRejectedMoveDocumentPath = @$marked_rejected_move_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$master_id . DIRECTORY_SEPARATOR;


            // cut from "main document" to paste into "marked_rejected_move_documents".
            // copy from student verification document "student_verification_documents" to paste in main "main document".
            $verifier_id = Config::get('global.verifier_id');
            $getroleid = $master = DocumentVerification::where('student_id', $student_id)
                ->whereNotIn('role_id', [$verifier_id])->orderby("id", "desc")->first();

            $studentDocumentVerificationDetails = StudentDocumentVerification::where('student_id', $student_id)->where('id', $master_id)->where('student_verification_id', $getroleid->id)->orderby("id", "desc")->first();


            $fields = ["photograph", "signature", "category_a", "category_b", "category_c", "category_d", "cast_certificate", "disability", "disadvantage_group", "pre_qualification", "identiy_proof", "minority"];

            if (@$studentDocumentVerificationDetails) {
                $studentDocumentVerificationDetails = $studentDocumentVerificationDetails->toArray();
                if (@$studentDocumentVerificationDetails['toc_subjects']) {
                    unset($studentDocumentVerificationDetails['toc_subjects']);
                }

                foreach ($studentDocumentVerificationDetails as $k => $v) {
                    if (in_array($k, $fields) && $v != null) {
                        $custom_data[$k] = @$v;
                    }
                }
            }
            $folder_path = public_path($markedRejectedMoveDocumentPath);
            File::makeDirectory($folder_path, $mode = 0777, true, true);


            if (!empty($custom_data)) {
                foreach (@$custom_data as $k => $getdata) {

                    if (!empty($getdata)) {
                        /* Start */

                        $combo_name = 'student_document_path';
                        $student_document_path = $this->master_details($combo_name);
                        $studentDocumentPath = $student_document_path[1] . $student_id . '/';

                        $combo_name = 'student_verification_documents';
                        $student_verification_documents = $this->master_details($combo_name);
                        $studentVerificationDocumentPath = @$student_verification_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$master_id . DIRECTORY_SEPARATOR;

                        $combo_name = 'marked_rejected_move_documents';
                        $marked_rejected_move_documents = $this->master_details($combo_name);
                        $markedRejectedMoveDocumentPath = @$marked_rejected_move_documents[1] . $current_admission_session_id . DIRECTORY_SEPARATOR . $current_exam_month_id . DIRECTORY_SEPARATOR . $student_id . DIRECTORY_SEPARATOR . @$master_id . DIRECTORY_SEPARATOR;


                        $mainDocPhysicalpPath = public_path($studentDocumentPath);
                        $markedRejectedMoveDocumentPath = public_path($markedRejectedMoveDocumentPath . $getdata);
                        $studentDocumentPath = public_path($studentDocumentPath . $getdata);

                        $studentVerificationDocumentPath = public_path($studentVerificationDocumentPath . $getdata);
                        $isValid = true;

                        if (file_exists($studentDocumentPath)) {
                            //main to marked_rejected
                            $move = File::move($studentDocumentPath, $markedRejectedMoveDocumentPath);
                        } else {
                            $isValid = false;
                        }
                        if (file_exists($studentVerificationDocumentPath)) {
                            if (file_exists($mainDocPhysicalpPath)) {

                            } else {
                                File::makeDirectory($mainDocPhysicalpPath, $mode = 0777, true, true);
                            }
                            $move = File::copy($studentVerificationDocumentPath, $studentDocumentPath);

                            //here update the name of doc in documents table.
                            $docToBeSave[$k] = $getdata;
                            Document::where('student_id', $student_id)->update($docToBeSave);
                        } else {
                            $isValid = false;
                        }
                        /* End */

                    }
                }
            }
            return $isValid;
        } catch (Exception $e) {
            if (!($e instanceof SQLException)) {
                app()->make(\App\Exceptions\Handler::class)->report($e);
            }
            return redirect()->back()->with('error', 'Failed! Document Can Not Been Uploaded!');
        }
    }

    public function MarksheetValidation($request)
    {
        $isValid = true;
        $errors = null;
        $validator = Validator::make([], []);
        $errMsg = '';
        $response = false;
        $validators = null;
        $alredy_exist_user_data = null;
        $studentdata = new MarksheetMigrationRequest;
        if (@$request->marksheet_type == 2) {
            $validators[] = Validator::make($request->all(), $studentdata->DuplicateDocumentRule);
        } else {
            $validators[] = Validator::make($request->all(), $studentdata->revisedDocumentRule);
        }
        foreach (@$validators as $key => $validator) {
            if ($validator->fails()) {
                $response[$key]['isValid'] = false;
                $response[$key]['errors'] = $validator->messages();
                $response[$key]['validator'] = $validator;
            }
        }
        return $response;

    }

    public function getMarksheetRequestStudentDetails($student_id = null, $mmr_id = null)
    {

        $master = Student::where('students.id', $student_id)
            ->with('marksheet_migration_requests', function ($query) use ($mmr_id) {
                $query->where('id', '=', $mmr_id);
            })->with('revised_correction', function ($query) use ($mmr_id) {
                $query->where('marksheet_migration_request_id', '=', $mmr_id);
            })
            ->with('Address')
            ->first();

        $dobs = null;


        if (@$master->dob) {
            $dobs = date('d-m-Y', strtotime($master->dob));
        }
        $combo_name = 'adm_type';
        $adm_types = $this->master_details($combo_name);
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_id = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_id = $this->master_details($combo_name);
        $combo_name = 'disability';
        $disability = $this->master_details($combo_name);
        $combo_name = 'categorya';
        $categorya = $this->master_details($combo_name);
        $combo_name = 'category_b';
        $category_b = $this->master_details($combo_name);
        $combo_name = 'pre-qualifi';
        $pre_qualifi = $this->master_details($combo_name);
        $combo_name = 'midium';
        $midium = $this->master_details($combo_name);
        $combo_name = 'rural_urban';
        $rural_urban = $this->master_details($combo_name);
        $combo_name = 'year';
        $rural_ustudentSubjectDropdownban = $this->master_details($combo_name);
        $combo_name = 'nationality';
        $nationality = $this->master_details($combo_name);
        $combo_name = 'religion';
        $religion = $this->master_details($combo_name);
        $combo_name = 'dis_adv_group';
        $dis_adv_group = $this->master_details($combo_name);
        $combo_name = 'exam_session';
        $exam_session = $this->master_details($combo_name);
        $combo_name = 'employment';
        $employment = $this->master_details($combo_name);
        $combo_name = 'are_you_from_rajasthan';
        $are_you_from_rajasthan = $this->master_details($combo_name);
        $combo_name = 'reval_types';
        $reval_types = $this->master_details($combo_name);
        $combo_name = 'marsheet_type';
        $marsheet_type = $this->master_details($combo_name);
        $combo_name = 'marksheet_print_option';
        $document_type = $this->master_details($combo_name);
        $combo_name = 'reval_per_subject_fee';
        $reval_per_subject_fee = $this->master_details($combo_name);

        $subject_list = $this->subjectList();
        $combo_name = 'student_document_path';
        $student_document_path = $this->master_details($combo_name);
        $studentDocumentPath = $student_document_path[1] . $student_id;
        $photograph = @$master->document->photograph;
        $signature = @$master->document->signature;
        $combo_name = 'yesno';
        $yesno = $this->master_details($combo_name);

        /* Replace string with X start */
        $custom_component_obj = new CustomComponent;
        $master->mobile = $custom_component_obj->_replaceTheStringWithX(@$master->mobile);
        if (@$master->application->jan_aadhar_number) {
            $master->application->jan_aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->jan_aadhar_number);
        }
        if (@$master->application->aadhar_number) {
            $master->application->aadhar_number = $custom_component_obj->_replaceTheStringWithX(@$master->application->aadhar_number);
        }
        if (@$master->bankdetils->account_number) {
            $master->bankdetils->account_number = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->account_number);
        }
        if (@$master->bankdetils->linked_mobile) {
            $master->bankdetils->linked_mobile = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->linked_mobile);
        }
        if (@$master->bankdetils->ifsc_code) {
            $master->bankdetils->ifsc_code = $custom_component_obj->_replaceTheStringWithX(@$master->bankdetils->ifsc_code);
        }
        if (@$master->challan_tid) {
            $master->challan_tid = $custom_component_obj->_replaceTheStringWithX(@$master->challan_tid);
        }
        /* Replace string with X end */


        $details = $this->getDefaultImgAndSign($photograph, $signature, $studentDocumentPath);
        $output['personalDetails'] = array(
            "photograph" => array(
                "fld" => "photograph",
                "label" => "फोटोग्राफ (photograph)",
                "value" => "<img height='100px' width='100px' target='_blank' src='" . $details['photographimageurl'] . "'/>"
            ),
            "signature" => array(
                "fld" => "signature",
                "label" => "हस्ताक्षर (Signature)",
                "value" => "<img height='100px' width='100px' target='_blank' src='" . $details['signatureimageurl'] . "'/>"
            ),
            // "ai_code" => array(
            // 	"fld" => "ai_code",
            // 	"label" => "एआई कोड(AI Code)",
            // 	"value" => @$master->ai_code
            // ),
            "enrollment" => array(
                "fld" => "enrollment",
                "label" => "नामांकन (Enrollment)",
                "value" => @$master->enrollment
            ),
            // "is_self_filled" => array(
            // 	"fld" => "is_self_filled",
            // 	"label" => "छात्र द्वारा स्वयं भरा गया है? (Is it filled by the student themselves?)",
            // 	"value" => @$yesno[@$master->is_self_filled]
            // ),
            // "is_otp_verified" => array(
            // 	"fld" => "is_otp_verified",
            // 	"label" => "क्या ओटीपी सत्यापित है?(Is OTP verified)",
            // 	"value" => @$yesno[@$master->is_otp_verified]
            // ),
            //"ssoid" => array(
            //	"fld" => "ssoid",
            //	"label" => "एसएसओ (SSO)",
            //	"value" => @$master->ssoid
            //),
            "name" => array(
                "fld" => "name",
                "label" => "आवेदक का नाम (Applicant's Name)",
                "value" => @$master->name
            ),
            "father_name" => array(
                "fld" => "father_name",
                "label" => "पिता का नाम (Father's Name)",
                "value" => @$master->father_name
            ),
            "mother_name" => array(
                "fld" => "	mother_name",
                "label" => " माँ का नाम (Mother's Name)",
                "value" => @$master->mother_name
            ),
            // "Exam" => array(
            // 	"fld" => "Exam",
            // 	"label" => " परीक्षा (Exam)",
            // 	"value" => @$exam_session[@$master->exam_month]
            // ),
            //"gender_id" => array(
            ///	"fld" => "gender_id",
            ///	"label" => " लिंग (Gender)",
            ///	"value" => @$gender_id[@$master->gender_id]
            //),
            "dob" => array(
                "fld" => "dob",
                "label" => "जन्म की तारीख (Date of Birth) (DD-MM-YYYY):",
                "value" => @$dobs
            ),
            // "religion_id" => array(
            // 	"fld" => "religion_id",
            // 	"label" => "धर्म (Religion)",
            // 	"value" => @$religion[@$master->application->religion_id]
            // ),"is_multiple_faculty" => array(
            // 	"fld" => "is_multiple_faculty",
            // 	"label" => " विभिन्न संकाय विषय(Multiple Faculty Subjects)",
            // 	"value" => @$yesno[@$master->application->is_multiple_faculty]
            // ), "selected_faculty" => array(
            // 	"fld" => "selected_faculty",
            // 	"label" => "मुख्य संकाय(Preferred Faculty)",
            // 	"value" => @$master->application->selected_faculty
            // ),"nationality" => array(
            // 	"fld" => "nationality",
            // 	"label" => "राष्ट्रीयता (Nationality)",
            // 	"value" => @$nationality[@$master->application->nationality]
            // ),"category_a" => array(
            // 	"fld" => "category_a",
            // 	"label" => "श्रेणी ए (Category A)",
            // 	"value" => @$categorya[@$master->application->category_a]
            // ),"disability" => array(
            // 	"fld" => "disability",
            // 	"label" => "विकलांगता (Disability)",
            // 	"value" => @$disability[@$master->application->disability]
            // ),"disadvantage_group" => array(
            // 	"fld" => "disadvantage_group",
            // 	"label" => "वंचित वर्ग (Disadvantage Group)",
            // 	"value" => @$dis_adv_group[@$master->application->disadvantage_group]
            // ),
            "course" => array(
                "fld" => "course",
                "label" => "पाठ्यक्रम (Course)",
                "value" => @$course[@$master->course]
            ), "mobile" => array(
                "fld" => "mobile",
                "label" => "मोबाइल (Mobile)",
                "value" => @$master->mobile
            ),
            // "midium" => array(
            // 	"fld" => "midium",
            // 	"label" => "अध्ययन का माध्यम (Medium of Study)",
            // 	"value" => @$midium[@$master->application->medium]
            // ),
            //"stream" => array(
            //	"fld" => "stream",
            //"label" => "स्ट्रीम (Stream)",
            //"value" => @$stream_id[@$master->stream]
            //),
            /* "jan_aadhar_number" => array(
				"fld" => "jan_aadhar_number",
				"label" => "जन आधार संख्या (Jan Aadhar Number)",
				"value" => @$master->application->jan_aadhar_number
			),
			  "aadhar_number" => array(
				"fld" => "aadhar_number",
				"label" => "आधार नंबर (Aadhar Number)",
				"value" => @$master->application->aadhar_number
			),"are_you_from_rajasthan	" => array(
				"fld" => "are_you_from_rajasthan	",
				"label" => "क्या आप राजस्थान के मूल निवासी हैं? (Are You domicile of Rajasthan)",
				"value" => @$are_you_from_rajasthan[@$master->are_you_from_rajasthan]
			),*/
            //"email" => array(
            //	"fld" => "email",
            //"label" => "ईमेल (Email)",
            //"value" => @$master->email
            //),
            "Marksheet Type" => array(
                "fld" => "marksheet_type",
                "label" => "मार्कशीट प्रकार (Marksheet Type)",
                "value" => @$marsheet_type[@$master->marksheet_migration_requests->marksheet_type]
            ),

            "Document Type" => array(
                "fld" => "document_type",
                "label" => "मार्कशीट दस्तावेज़ प्रकार ( Marksheet Document Type)",
                "value" => @$document_type[@$master->marksheet_migration_requests->document_type]
            ),
            /*"rural_urban" => array(
				"fld" => "rural_urban",
				"label" => "शहरी /ग्रामीण (Rural/Urban)",
				"value" => @$rural_urban[@$master->application->rural_urban]
			),
			"employment" => array(
				"fld" => "employment",
				"label" => "रोज़गार (Employment)",
				"value" => @$employment[@$master->application->employment]
			),"pre_qualification" => array(
				"fld" => "pre_qualification",
				"label" => "पूर्व योग्यता (Pre Qualification)",
				"value" => @$pre_qualifi[@$master->application->pre_qualification]
			),*/
        );

        $output['addressDetails'] = array(
            "address1" => array(
                "fld" => "address1",
                "label" => " पता पंक्ति 1 (Address Line 1)",
                "value" => @$master->address->address1
            ),
            "address2" => array(
                "fld" => "address2",
                "label" => "पता पंक्ति 2 (Address Line 2)",
                "value" => @$master->address->address2
            ),
            "address3" => array(
                "fld" => "address3",
                "label" => "पता पंक्ति 3 (Address Line 3)",
                "value" => @$master->address->address3
            ),
            "state_name" => array(
                "fld" => "state_name",
                "label" => " राज्य (State)",
                "value" => @$master->address->state_name
            ),
            "district_name" => array(
                "fld" => "district_name",
                "label" => " जिला (District)",
                "value" => @$master->address->district_name
            ),
            "tehsil_name" => array(
                "fld" => "tehsil_name",
                "label" => "तहसील (Tehsil)",
                "value" => @$master->address->tehsil_name
            ),
            "block_name" => array(
                "fld" => "block_name",
                "label" => "ब्लॉक/(Block)",
                "value" => @$master->address->block_name
            ),
            "city_name" => array(
                "fld" => "city_name",
                "label" => " शहर / गाँव (City/Village)",
                "value" => @$master->address->city_name
            ),
            "pincode" => array(
                "fld" => "pincode",
                "label" => " पिन कोड (Pincode)",
                "value" => @$master->address->pincode
            )
        );

        if (@$master->address->is_both_same) {

            $output['currentAddressDetails'] = array(
                "is_both_same" => array(
                    "fld" => "is_both_same",
                    "label" => "यदि स्थायी और पत्राचार पता समान है तो (if the permanent and correspondence address are same)",
                    "value" => @$yesno[@$master->address->is_both_same]
                )
            );
        } else {
            $output['currentAddressDetails'] = array(

                "current_address1" => array(
                    "fld" => "address1",
                    "label" => " पता पंक्ति 1 (Address Line 1)",
                    "value" => @$master->address->current_address1
                ),
                "current_address2" => array(
                    "fld" => "address2",
                    "label" => "पता पंक्ति 2 (Address Line 2)",
                    "value" => @$master->address->current_address2
                ),
                "current_address3" => array(
                    "fld" => "address3",
                    "label" => "पता पंक्ति 3 (Address Line 3)",
                    "value" => @$master->address->current_address3
                ),
                "current_state_name" => array(
                    "fld" => "state_name",
                    "label" => " राज्य (State)",
                    "value" => @$master->address->current_state_name
                ),
                "current_district_name" => array(
                    "fld" => "district_name",
                    "label" => " जिला (District)",
                    "value" => @$master->address->current_district_name
                ),
                "current_tehsil_name" => array(
                    "fld" => "tehsil_name",
                    "label" => "तहसील (Tehsil)",
                    "value" => @$master->address->current_tehsil_name
                ),
                "current_block_name" => array(
                    "fld" => "block_name",
                    "label" => "ब्लॉक/(Block)",
                    "value" => @$master->address->current_block_name
                ),
                "current_city_name" => array(
                    "fld" => "city_name",
                    "label" => " शहर / गाँव (City/Village)",
                    "value" => @$master->address->current_city_name
                ),
                "current_pincode" => array(
                    "fld" => "pincode",
                    "label" => " पिन कोड (Pincode)",
                    "value" => @$master->address->current_pincode
                )
            );
        }


        if ($master->marksheet_migration_requests->total_fees > 0) {
            $output['studentfeesDetails'] = array(
                "total" => array(
                    "fld" => "total_fees",
                    "label" => "कुल  शुल्क(Total Fees)",
                    "value" => @$master->marksheet_migration_requests->total_fees
                ),
            );
        } else {
            $output['studentfeesDetails'] = array(
                "total" => array(
                    "fld" => "total_fees",
                    "label" => "कुल  शुल्क(Total Fees)",
                    "value" => @$master->marksheet_migration_requests->total_fees
                ),
            );
        }
        $output['TransactionDetails'] = array(
            "challan_tid " => array(
                "fld" => "challan_tid ",
                "label" => " चालान संख्या (Challan Number)",
                "value" => @$master->marksheet_migration_requests->challan_tid
            ),
            "submitted" => array(
                "fld" => "submitted",
                "label" => "शुल्क जमा तिथि( Fees Submitted Date)",
                "value" => @$master->marksheet_migration_requests->submitted
            ),
        );

        $super_admin_id = Config::get("global.super_admin_id");
        foreach (@$master->revised_correction as $k => $v) {
            $extraFlag = null;
            $label = ucwords(str_replace(array('\'', '"', ',', ';', '<', '_', '>'), ' ', @$v->correction_field));
            if (@$v->correction_field == 'dob') {
                $value = date("d-m-Y", strtotime($v->correct_value));
            } else {
                $value = $v->correct_value;
            }
            $output['correction_details'][] = array(
                "fld" => "",
                "label" => "$label",
                "value" => "$value",
            );
        }

        $studentDocumentPath = "marksheets/$master->id";
        $output['documentDetails']['support_document'] = array(
            "fld" => 'support_document',
            "label" => "Support Document",
            // "value" => "<a title= 'Click here to download " . @$v ."' href=".route('download',Crypt::encrypt('/'.$studentDocumentPath . "/" . @$master->document->$k)) . "><i class='material-icons'>file_download</i></a>"
            "value" => "<a target='_blank' title='Click here to verify " . @$v . "' href=" . url('public/' . $studentDocumentPath . '/' . @$master->marksheet_migration_requests->support_document) . "><span class='material-icons'>link</span></a>"
        );

        $output = array(
            "personalDetails" => array(
                "seciontLabel" => "Personal Details",
                "data" => @$output['personalDetails']
            ),
            "CorrectionvalueDetails" => array(
                "seciontLabel" => "Correction value Details",
                "data" => @$output['correction_details']
            ),

            // "currentAddressDetails" => array(
            // 	"seciontLabel" => "Correspondence Address Details",
            // 	"data" => @$output['currentAddressDetails']
            // ),
            "documentDetails" => array(
                "seciontLabel" => "Document Details",
                "data" => @$output['documentDetails']
            ),
            //"studentfeesDetails" => array(
            //"seciontLabel" => "Student Fees Details",
            //"data" => @$output['studentfeesDetails']
            //),
            "TransactionDetails" => array(
                "seciontLabel" => "Transaction Details",
                "data" => @$output['TransactionDetails']
            ),
            // "addressDetails" => array(
            //	"seciontLabel" => "Address Details",
            //"data" => @$output['addressDetails']
            //),
        );
        return $output;

    }

    public function _checkchangerequestsAllowOrNotAllow()
    {
        $objController = new Controller();
        $combo_name = 'change_request_start_date';
        $combo_name2 = 'change_request_end_date';
        $change_request_start_date_arr = $objController->master_details($combo_name);
        $change_request_end_date = $objController->master_details($combo_name2);
        if (strtotime(date("Y-m-d H:i:s")) >= strtotime($change_request_start_date_arr[1]) && strtotime(date("Y-m-d H:i:s")) <= strtotime($change_request_end_date[1])) {
            $isValid = true;
        } else {
            $isValid = false;
        }
        return $isValid;
    }

    public function updateFlagInMarkMigrationRequest($update_field = null, $student_id = null, $status = null)
    {
        $respones = false;
        $data = MarksheetMigrationRequest::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        if (!empty($data)) {
            if ($data->$update_field != 1) {
                $svData = [$update_field => $status];
                MarksheetMigrationRequest::where('id', $data->id)->update($svData);
                $respones = true;
            }
        }
        return $respones;
    }

    public function changerequestchecklatefeedupdate($student_id)
    {
        @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        @$getstudenttotalfeesold = ChangeRequertOldStudentFees::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)->orderBy('id', 'desc')->first('late_fee');
        if (@$getstudenttotalfeesold->late_fee != 0 || @$getstudenttotalfeesold->late_fee != NULL) {
            @$master = StudentFee::where('student_id', $student_id)->first('total');
            @$getstudenttotalfeestotal = @$master->total;
            @$changetotal = @$getstudenttotalfeestotal + @$getstudenttotalfeesold->late_fee;
            @$studentfeeschangerequerts = StudentFee::where('student_id', $student_id)->update(['late_fee' => $getstudenttotalfeesold->late_fee, 'total' => $changetotal]);
        }

    }

    public function suppchangerequestchecklatefeedupdate($student_id)
    {
        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        @$master = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->first('id');
        @$changerequeststudent = SuppChangeRequestStudents::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('supp_id', @$master->id)->orderBy('id', 'desc')->first('id');
        @$getstudenttotalfeesold = SuppChangeRequertOldStudentFees::where('student_id', $student_id)->where('supp_student_change_request_id', $changerequeststudent->id)->where('supp_id', @$master->id)->orderBy('id', 'desc')->first('late_fees');

        if (@$getstudenttotalfeesold->late_fees != 0 || @$getstudenttotalfeesold->late_fees != NULL) {
            @$masters = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->first(['total_fees']);
            @$getstudenttotalfeestotal = @$masters->total_fees;
            @$changetotal = @$getstudenttotalfeestotal + @$getstudenttotalfeesold->late_fees;
            @$studentfeeschangerequerts = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->update(['late_fees' => $getstudenttotalfeesold->late_fees, 'total_fees' => $changetotal]);
        }

    }

    public function _changerequestsendLockSubmittedMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        @$checkfeesdata = $this->changerequestcheckfees($student_id);
        @$changemakepayment = $this->changerequestcheckfeesdifference($student_id);
        @$student = Student::where('id', $student_id)->where('student_change_requests', 2)->first();
        @$mobile = @$student->mobile;
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $sms = null;
        $templateID = '1107172561504779036';
        if (!empty(@$student)) {
            $mobile = @$student->mobile;
            $sms = 'Dear Applicant, Your application has been registered successfully. Please pay difference fees Rs.' . $changemakepayment . 'by logging into RSOS Application using SSOID to complete your application.-RSOS,GoR';

        }
        return $this->_sendSMS($mobile, $sms, $templateID);
    }

    public function changerequestcheckfeesdifference($student_id)
    {
        $checkfeesdata = $this->changerequestcheckfees($student_id);
        if (@$checkfeesdata == true) {
            @$master = StudentFee::where('student_id', $student_id)->first('total');
            @$changerequeststudent = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
            @$getstudenttotalfeesold = ChangeRequertOldStudentFees::where('student_id', $student_id)->where('student_change_request_id', $changerequeststudent->id)->orderBy('id', 'desc')->first('total');
            @$getstudenttotalfeesnew = @$master->total;
            @$changemakepayment = $getstudenttotalfeesnew - $getstudenttotalfeesold->total;
        }
        return @$changemakepayment;
    }

    public function getVerifcaionSMSSend($type, $lang)
    {
        $details = array();
        $templateID = null;
        $sms = null;

        if ($lang == 'eng') {
            if ($type == 'deficient') {
                $sms = "Dear applicant,
				We regret to inform you that the document/s submitted for your RSOS Admission application has/have been marked deficient by the RSOS Officials. Please proceed to re-upload the correct document/s by logging into your SSOID to complete your application process.-RSOS,GoR";
                $templateID = "1107171024461784373";
            } else if ($type == 'approved') {
                $sms = "Dear applicant,
				We are pleased to inform you that the document/s submitted for your RSOS Admission application has/have been approved by the RSOS Officials.-RSOS,GoR";
                $templateID = "1107171024459834708";
            }
        } else if ($lang == 'hindi') {
            if ($type == 'deficient') {
                $sms = "प्रिय आवेदक,
				आपको सूचित किया जाता है कि आपके राजस्थान स्टेट ओपन स्कूल आवेदन के लिए प्रस्तुत किए गए दस्तावेज़/दस्तावेजों को अधिकारी द्वारा ने अयोग्य चिह्नित कर दिया है। कृपया अपने एसएसओआईडी में लॉग इन करके सही दस्तावेज़/दस्तावेजों को पुनः अपलोड करें ताकि आपके आवेदन प्रक्रिया को पूरा किया जा सके।-RSOS,GoR";
                $templateID = "1107171110039936356";
            } else if ($type == 'approved') {
                $sms = "प्रिय आवेदक,
				आपको सूचित किया जाता है कि आपके राजस्थान स्टेट ओपन स्कूल आवेदन के लिए प्रस्तुत किए गए दस्तावेज़/दस्तावेजों को राजस्थान स्टेट ओपन स्कूल अधिकारी द्वारा स्वीकृत कर दिया हैं।-RSOS,GoR";
                $templateID = "1107171024457443186";
            }
        }
        $details = ['sms' => $sms, 'templateID' => $templateID];
        return $details;

    }

    public function _changerequestsendapprovedMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        @$student = Student::where('id', $student_id)->where('student_change_requests', 2)->first();
        @$ChangeRequerenddate = DB::table('masters')->where('combo_name', 'change_request_end_date')->first();
        @$newDate = date("d-m-Y", strtotime($ChangeRequerenddate->option_val));
        @$mobile = @$student->mobile;
        $sms = null;
        $templateID = '1107172561521626638';
        if (!empty(@$student)) {
            $mobile = @$student->mobile;
            $sms = 'Dear Applicant, your request for update has been approved by RSOS. Please update your application before date' . $newDate . '-RSOS,GoR';
        }
        return $this->_sendSMS($mobile, $sms, $templateID);
    }

    public function _marksheetCorrectionLockAndSubmittedMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        $student = Student::where('id', $student_id)->orderBy('id', 'desc')->first();
        $marksheetData = MarksheetMigrationRequest::where('student_id', $student_id)->where('marksheet_migration_status', '0')->orderBy('id', 'desc')->first();
        $fld = "enrollment";
        $$fld = $student->$fld;
        $fld = "mobile";
        $$fld = $student->$fld;
        // $paymentUrl = route('admission_fee_payment');
        //$paymentUrl = $_SERVER['HTTP_HOST'];
        //$application_fee = 0;
        $templateID = "1107172561504779036";
        if (isset($marksheetData->total_fees) && $marksheetData->total_fees > 0) {
            $application_fee = $marksheetData->total_fees;
        }
        $sms = null;
        if ($application_fee > 0) {

            //$sms = 'Dear Applicant, Your application has been submitted successfully. Please pay  fees Rs.' . $application_fee . ' to complete your request.-RSOS,GoR';
            $sms = "Dear Applicant, Your  Application has been registered successfully. Please pay Marksheet/Correction fees Rs." . $application_fee . " by logging into RSOS Application using SSOID to complete your Application.-RSOS,GoR";
        }
        return $this->_sendSMS($mobile, $sms, $templateID);
    }

    public function _sendLockSubmittedMessageReval($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        $combo_name = 'reval_exam_year';
        $reval_exam_year = $this->master_details($combo_name);
        $combo_name = 'reval_exam_month';
        $reval_exam_month = $this->master_details($combo_name);
        $student = Student::with('application', 'studentfee')
            ->where('students.id', $student_id)
            ->first();
        $revalstudentdata = RevalStudent::where('student_id', $student_id)->where('exam_year', $reval_exam_year)->where('exam_month', $reval_exam_month)->orderBy('id', 'desc')->first();
        $fld = "enrollment";
        $$fld = $student->$fld;
        $fld = "mobile";
        $$fld = $student->$fld;
        $templateID = "1107172561504779036";
        // $paymentUrl = route('admission_fee_payment');
        $paymentUrl = $_SERVER['HTTP_HOST'];
        if (isset($revalstudentdata->total_fees) && $revalstudentdata->total_fees > 0) {
            $revalstudentdata->total_fees = $revalstudentdata->total_fees;
        }
        $sms = null;

        if ($revalstudentdata->total_fees > 0) {
            $application_fee = $revalstudentdata->total_fees;
            //$sms = 'Dear Applicant, Your application has been registered successfully. Please pay admission fees Rs.' . $application_fee . ' by clicking on URL  ' . $paymentUrl . ' to complete your admission application.-RSOS,GoR';
            $sms = "Dear Applicant, Your  Application has been registered successfully. Please pay Revaluation fees Rs." . $application_fee . " by logging into RSOS Application using SSOID to complete your Application.-RSOS,GoR";
        }
        return $this->_sendSMS($mobile, $sms, $templateID);
    }

    public function studentalltablebackup($student_id)
    {
        @$changerequeststudentdata = ChangeRequeStstudent::where('student_id', $student_id)->orderBy('id', 'desc')->first();
        @$changerequeststudents = Student::where('id', $student_id)->first()->toArray();
        @$changerequestapplications = Application::where('student_id', $student_id)->first()->toArray();
        @$changerequestaddress = Address::where('student_id', $student_id)->first()->toArray();
        @$changerequestbankdetail = BankDetail::where('student_id', $student_id)->first()->toArray();
        @$changerequestdocument = Document::where('student_id', $student_id)->first()->toArray();
        @$changerequestadmissionSubject = AdmissionSubject::where('student_id', $student_id)->get();
        @$changerequesttoc = Toc::where('student_id', $student_id)->first();
        @$changerequesttocmark = TocMark::where('student_id', $student_id)->get();
        @$changerequestexamsubjects = ExamSubject::where('student_id', $student_id)->get();

        if (!empty(@$changerequeststudents)) {
            @$changerequeststudents['student_change_request_id'] = $changerequeststudentdata->id;
            @$changerequeststudents['student_id'] = @$student_id;
            unset($changerequeststudents["id"]);
            @$changerequeststudents = DB::table('student_change_request_backups')->insert($changerequeststudents);
        }
        if (!empty(@$changerequestapplications)) {
            @$changerequestapplications['student_change_request_id'] = $changerequeststudentdata->id;
            unset($changerequestapplications["id"]);
            $changerequestapplications = DB::table('application_change_request_backups')->insert($changerequestapplications);
        }
        if (!empty(@$changerequestaddress)) {
            @$changerequestaddress['student_change_request_id'] = $changerequeststudentdata->id;
            unset($changerequestaddress["id"]);
            $changerequestaddress = DB::table('addresses_change_request_backups')->insert($changerequestaddress);
        }
        if (!empty(@$changerequestbankdetail)) {
            @$changerequestbankdetail['student_change_request_id'] = $changerequeststudentdata->id;
            unset($changerequestbankdetail["id"]);
            $changerequestbankdetail = DB::table('bank_detail_change_request_backups')->insert($changerequestbankdetail);
        }
        if (!empty(@$changerequestdocument)) {
            @$changerequestdocument['student_change_request_id'] = $changerequeststudentdata->id;
            unset($changerequestdocument["id"]);
            $changerequestdocument = DB::table('document_change_request_backups')->insert($changerequestdocument);
        }
        if (!empty(@$changerequestadmissionSubject)) {
            foreach ($changerequestadmissionSubject as $field) {
                DB::table('admission_subject_change_request_backups')->insert(
                    ['student_id' => $field->student_id, 'subject_id' => $field->subject_id, 'is_additional' => $field->is_additional, 'student_change_request_id' => $changerequeststudentdata->id, 'exam_year' => $field->exam_year, 'stream' => $field->stream, 'course' => $field->course, 'exam_month' => $field->exam_month, 'adm_type' => $field->adm_type, 'deleted_at' => $field->deleted_at, 'created_at' => $field->created_at, 'updated_at' => $field->updated_at, 'passing_year' => $field->passing_year,]
                );
            }
        }
        if (!empty(@$changerequesttoc)) {
            unset($changerequesttoc["id"]);
            $changerequesttoc = DB::table('toc_change_request_backups')->insert(
                ['student_id' => $changerequesttoc->student_id, 'year_fail' => $changerequesttoc->year_fail, 'year_pass' => $changerequesttoc->year_pass, 'student_change_request_id' => $changerequeststudentdata->id, 'board' => $changerequesttoc->board, 'roll_no' => $changerequesttoc->roll_no, 'course' => $changerequesttoc->course, 'exam_year' => $changerequesttoc->exam_year, 'exam_month' => $changerequesttoc->exam_month, 'stream' => $changerequesttoc->stream, 'deleted_at' => $changerequesttoc->deleted_at, 'created_at' => $changerequesttoc->created_at, 'updated_at' => $changerequesttoc->updated_at,]
            );
        }
        if (!empty(@$changerequesttocmark)) {
            foreach ($changerequesttocmark as $field) {
                DB::table('toc_mark_change_request_backups')->insert(
                    ['student_id' => $field->student_id, 'toc_id' => $field->toc_id, 'subject_id' => $field->subject_id, 'student_change_request_id' => $changerequeststudentdata->id, 'theory' => $field->theory, 'practical' => $field->practical, 'total_marks' => $field->total_marks, 'conv_theory' => $field->conv_theory, 'conv_practical' => $field->conv_practical, 'conv_total_marks' => $field->conv_total_marks, 'deleted' => $field->deleted, 'deleted_at' => $field->deleted_at, 'created_at' => $field->created_at, 'updated_at' => $field->updated_at]
                );
            }
        }
        if (!empty(@$changerequestexamsubjects)) {
            foreach ($changerequestexamsubjects as $field) {
                DB::table('exam_subject_change_request_backups')->insert(
                    ['remarks' => $field->remarks, 'student_id' => $field->student_id, 'enrollment' => $field->enrollment, 'student_change_request_id' => $changerequeststudentdata->id, 'pastdata_id' => $field->pastdata_id, 'subject_id' => $field->subject_id, 'is_additional' => $field->is_additional, 'final_theory_marks' => $field->final_theory_marks, 'final_practical_marks' => $field->final_practical_marks, 'sessional_marks' => $field->sessional_marks, 'sessional_marks_reil_result_20' => $field->sessional_marks_reil_result_20, 'sessional_marks_reil_result' => $field->sessional_marks_reil_result, 'total_marks' => $field->total_marks,
                        'final_result' => $field->final_result, 'exam_year' => $field->exam_year, 'stream' => $field->stream, 'exam_month' => $field->exam_month, 'course' => $field->course, 'subject_type' => $field->subject_type, 'adm_type' => $field->adm_type, 'is_sessional_mark_entered' => $field->is_sessional_mark_entered,
                        'is_temp_exam_subject' => $field->is_temp_exam_subject, 'created_at' => $field->created_at, 'updated_at' => $field->updated_at, 'modified_at' => $field->modified_at, 'is_supplementary_subject' => $field->is_supplementary_subject]
                );
            }
        }

    }

    public function _suppchangerequestsendapprovedMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        $exam_yearsupp = Config::get("global.form_supp_current_admission_session_id");
        $exam_monthsupp = Config::get("global.supp_current_admission_exam_month");
        $conditions['supplementaries.exam_year'] = $exam_yearsupp;
        $conditions['supplementaries.exam_month'] = $exam_monthsupp;
        $conditions['supplementaries.supp_student_change_requests'] = 2;
        @$student = Supplementary::join('students', 'students.id', '=', 'supplementaries.student_id')->
        where('supplementaries.student_id', $student_id)->where($conditions)->first('mobile');
        @$ChangeRequerenddate = DB::table('masters')->where('combo_name', 'change_request_supplementaries_end_date')->first();
        @$newDate = date("d-m-Y", strtotime($ChangeRequerenddate->option_val));
        @$mobile = @$student->mobile;
        $sms = null;
        $templateID = '1107172561521626638';
        if (!empty(@$student)) {
            $mobile = @$student->mobile;
            $sms = 'Dear Applicant, your request for update has been approved by RSOS. Please update your application before date' . $newDate . '-RSOS,GoR';
        }
        return $this->_sendSMS($mobile, $sms, $templateID);
    }

    public function _suppchangerequestsendLockSubmittedMessage($student_id)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", -1);
        @$checkfeesdata = $this->suppchangerequestcheckfees($student_id);
        @$changemakepayment = $this->suppchangerequestcheckfeesdifference($student_id);
        @$student = Student::where('id', $student_id)->first();
        @$mobile = @$student->mobile;
        $paymentUrl = $_SERVER['HTTP_HOST'];
        $sms = null;
        $templateID = '1107172561504779036';
        if (!empty(@$student)) {
            $mobile = @$student->mobile;
            $sms = 'Dear Applicant, Your application has been registered successfully. Please pay difference fees Rs.' . $changemakepayment . 'by logging into RSOS Application using SSOID to complete your application.-RSOS,GoR';

        }
        return $this->_sendSMS($mobile, $sms, $templateID);
    }

    public function suppchangerequestcheckfees($student_id)
    {
        $exam_year = Config::get('global.form_supp_current_admission_session_id');
        $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
        @$master = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->first(['total_fees', 'id']);
        @$changerequeststudent = SuppChangeRequestStudents::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('supp_id', @$master->id)->orderBy('id', 'desc')->first('id');
        @$getstudenttotalfeesold = SuppChangeRequertOldStudentFees::where('student_id', $student_id)->where('supp_student_change_request_id', $changerequeststudent->id)->where('supp_id', @$master->id)->first('total_fees');
        @$getstudenttotalfeesnew = @$master->total_fees;
        if (@$getstudenttotalfeesold->total == @$getstudenttotalfeesnew) {
            $suppmakepaymentchangerequerts = 'false';
        } elseif (@$getstudenttotalfeesold->total_fees < @$getstudenttotalfeesnew) {
            $suppmakepaymentchangerequerts = 'true';
        } elseif (@$getstudenttotalfeesold->total_fees > @$getstudenttotalfeesnew) {
            @$suppmakepaymentchangerequerts = 'false';
        }
        return @$suppmakepaymentchangerequerts;
    }

    public function suppchangerequestcheckfeesdifference($student_id)
    {
        $checkfeesdata = $this->suppchangerequestcheckfees($student_id);
        if (@$checkfeesdata == true) {
            $exam_year = Config::get('global.form_supp_current_admission_session_id');
            $exam_month = $current_exam_month_id = Config::get('global.supp_current_admission_exam_month');
            @$master = Supplementary::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->first(['total_fees', 'id']);
            @$changerequeststudent = SuppChangeRequestStudents::where('student_id', $student_id)->where('exam_year', $exam_year)->where('exam_month', $exam_month)->where('supp_id', @$master->id)->orderBy('id', 'desc')->first('id');
            @$getstudenttotalfeesold = SuppChangeRequertOldStudentFees::where('student_id', $student_id)->where('supp_student_change_request_id', $changerequeststudent->id)->where('supp_id', @$master->id)->orderBy('id', 'desc')->first('total_fees');
            @$getstudenttotalfeesnew = @$master->total_fees;
            @$changemakepayment = $getstudenttotalfeesnew - $getstudenttotalfeesold->total_fees;
        }
        return @$changemakepayment;
    }

    public function suppstudentalltablebackup($student_id)
    {
        $current_supp_exam_month_id = Config::get("global.supp_current_admission_exam_month");
        $changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
        $conditions['exam_year'] = $changerequertcurrentsuppexamyear;
        $conditions['exam_month'] = $current_supp_exam_month_id;
        @$changerequeSupps = Supplementary::where('student_id', $student_id)->where($conditions)->first()->toArray();
        @$changerequeststudentdata = SuppChangeRequestStudents::where('student_id', $student_id)->where($conditions)->where('supp_id', @$changerequeSupps['id'])->orderBy('id', 'desc')->first();
        @$changerequestsupplementarysubject = SupplementarySubject::where('student_id', $student_id)->where($conditions)->where('supplementary_id', @$changerequeSupps['id'])->get();

        if (!empty(@$changerequeSupps)) {
            @$changerequeSupps['supp_student_change_request_id'] = $changerequeststudentdata->id;
            @$changerequeSupps['student_id'] = @$student_id;
            unset($changerequeSupps["id"]);
            @$changerequeSupps = DB::table('change_request_supplementarie_backups')->insert($changerequeSupps);
        }
        if (!empty(@$changerequestsupplementarysubject)) {
            foreach ($changerequestsupplementarysubject as $field) {
                DB::table('change_request_supplementary_subject_backups')->insert(
                    ['student_id' => $field->student_id, 'supplementary_id' => $field->supplementary_id, 'subject_id' => $field->subject_id, 'supp_student_change_request_id' => $changerequeststudentdata->id, 'is_additional_subject' => $field->is_additional_subject, 'previous_subject_id' => $field->previous_subject_id, 'status' => $field->status, 'deleted_at' => $field->deleted_at, 'created_at' => $field->created_at, 'updated_at' => $field->updated_at, 'origional_subject_id' => $field->origional_subject_id, 'exam_year' => $field->exam_year, 'exam_month' => $field->exam_month, 'remarks' => $field->remarks, 'deleted_by_user_id' => $field->deleted_by_user_id]
                );
            }
        }
    }

    public function suppstudentalltablebackuprewarddata($student_id)
    {
        $current_supp_exam_month_id = Config::get("global.supp_current_admission_exam_month");
        $changerequertcurrentsuppexamyear = Config::get("global.form_supp_current_admission_session_id");
        $conditions['exam_year'] = $changerequertcurrentsuppexamyear;
        $conditions['exam_month'] = $current_supp_exam_month_id;
        @$changerequeSupps = Supplementary::where('student_id', $student_id)->where($conditions)->first()->toArray();
        @$changerequeststudentdata = SuppChangeRequestStudents::where('student_id', $student_id)->where($conditions)->where('supp_id', @$changerequeSupps['id'])->orderBy('id', 'desc')->first();
        @$changerequestsupplementarysubject = SupplementarySubject::where('student_id', $student_id)->where($conditions)->where('supplementary_id', @$changerequeSupps['id'])->get();

        if (!empty(@$changerequeSupps)) {
            @$changerequeSupps['supp_student_change_request_id'] = $changerequeststudentdata->id;
            @$changerequeSupps['student_id'] = @$student_id;
            unset($changerequeSupps["id"]);
            @$changerequeSupps = DB::table('change_request_supplementarie_backups')->insert($changerequeSupps);
        }
        if (!empty(@$changerequestsupplementarysubject)) {
            foreach ($changerequestsupplementarysubject as $field) {
                DB::table('change_request_supplementary_subject_backups')->insert(
                    ['student_id' => $field->student_id, 'supplementary_id' => $field->supplementary_id, 'subject_id' => $field->subject_id, 'supp_student_change_request_id' => $changerequeststudentdata->id, 'is_additional_subject' => $field->is_additional_subject, 'previous_subject_id' => $field->previous_subject_id, 'status' => $field->status, 'deleted_at' => $field->deleted_at, 'created_at' => $field->created_at, 'updated_at' => $field->updated_at, 'origional_subject_id' => $field->origional_subject_id, 'exam_year' => $field->exam_year, 'exam_month' => $field->exam_month, 'remarks' => $field->remarks, 'deleted_by_user_id' => $field->deleted_by_user_id]
                );
            }
        }
    }

    public function _getfreshVerNotRequriedDocInput($documents_verification_arr = null)
    {
        $fresh_ver_not_requried_doc_input = Config::get("global.fresh_ver_not_requried_doc_input");
        foreach ($fresh_ver_not_requried_doc_input as $key => $v) {
            if (@$documents_verification_arr[$v]) {
                unset($documents_verification_arr[$v]);
            }
        }
        return $documents_verification_arr;
    }

    public function setUpdatedRoleBasedSessionYear($role_id = null)
    {
        $form_session_changed = Config::get('global.form_session_changed');
        $practical_role = Config::get('global.practicalexaminer');
        $allowpeviousyear = Config::get('global.allowOnlyPreviousYears');
        if (in_array($role_id, $form_session_changed)) {
            $current_admission_session_id_for_year_selection = Config::get('global.form_current_admission_session_id');
        } else if (in_array($role_id, $allowpeviousyear)) {
            $current_admission_session_id_for_year_selection = config::get('global.previous_year');
        } else {
            $current_admission_session_id_for_year_selection = Config::get('global.form_current_admission_session_id');
        }

        Session::put("current_admission_sessions", $current_admission_session_id_for_year_selection);
        return true;
    }

    public function _getUsernameWithPassword($student_id = null)
    {
        $username = null;
        $password = null;
        $student = Student::where('id', @$student_id)->first();
        if (@$student_id) {
            $length = 3;
            $student->name = substr(str_shuffle(@$student->name), 0, $length);
            $username = "RJ" . (@$student->id + 600) . str_replace(' ', '', @$student->name);
            $firstTwo = substr($username, 0, 2);
            $lastThree = substr($username, -3);
            $password = @$firstTwo . @$lastThree . "@" . rand(100, 500) . Date("Y");
        }
        $response['username'] = $username;
        $response['password'] = $password;
        return $response;
    }

    public function hallticketqrcode($enrollment = null)
    {
        $imagepath = asset('public/hallticket/qrcode/enrollment/' . $enrollment . '.png');
        File::makeDirectory($imagepath, $mode = 0777, true, true);
        $custom_component_obj = new CustomComponent;
        $alpha = $this->toAlpha($enrollment);
        $url = URL::to("/hallticketqr?$alpha");
        //$url = URL::to($path);
        $qrcode = $this->qrcode($url, $enrollment);
        $imagepath = asset('public/hallticket/qrcode/enrollment/' . $enrollment . '.png');
        $barcode_img = '<img src="' . $imagepath . '" alt=barcode-' . $enrollment . ' style="font-size:0;position:relative;width:65px;height:65px;" >';
        return $barcode_img;
    }

    public function toAlpha($num)
    {
        $alpha = "";
        while ($num > 0) {
            $remainder = ($num - 1) % 26;
            $alpha = chr(65 + $remainder) . $alpha; // 65 is ASCII for 'A'
            $num = ($num - $remainder - 1) / 26;
        }
        return $alpha;
    }

    public function qrcode($value, $enrollment)
    {
        $qrcdeo = new QRcode();
        $directory_path = public_path("hallticket/qrcode/enrollment");
        $directory = File::makeDirectory($directory_path, $mode = 0777, true, true);
        $value = $value;
        $ecc = 'L';
        $file = public_path("hallticket/qrcode/enrollment/$enrollment.png");
        $pixel_size = 4;
        $frame_size = 2;
        $qrcdeo->png($value, $file, $ecc, $pixel_size, $frame_size);
    }

    public function updateInGlobalVariable($indexItem = null, $indexValue = null)
    {
        // $indexItem = 'revalMarksDefaultPageLimit';$indexValue = '999999';
        $response['status'] = false;
        $response['error'] = null;
        // Path to the global.php file
        $filePath = config_path('backend_changeable_global.php');
        $indexItem = Crypt::decrypt($indexItem);
        if (@$indexItem && file_exists($filePath)) {
            $config = include $filePath;
            $oldValue = $config[@$indexItem];
            if (isset($config[$indexItem])) {
                $config[$indexItem] = $indexValue;

            } else if (isset($config[0][$indexItem])) {
                $config[0][$indexItem] = $indexValue;
            }
            @$table_name = "global_variable";
            $newContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
            if ($newContent !== null) {

                $this->updateStudentLog(@$table_name, $indexItem, 'global_variable', '1', @$oldValue);
                file_put_contents($filePath, $newContent);
                $response['status'] = true;
            } else {
                $response['error'] = $indexItem . " does not write in the file.";
            }
        } else {
            $response['error'] = 'File not not found.';
        }
        return $response;
    }

    public function updateStudentLog($table_name = null, $table_primary_id = null, $form_type = 'Admission', $is_unlock_allow = false, $global_value = null)
    {
        $exam_year = Config::get('global.current_admission_session_id');
        $exam_month = Config::get('global.current_exam_month_id');
        $macAddress = $this->_my_mac_address();
        $status = false;
        if (!empty($table_name) && !empty($table_primary_id)) {
            if (!empty($table_name) && $table_name == "global_variable") {
                $table_data = [$table_primary_id => $global_value];
            } else {
                $table_data = DB::table($table_name)->where('id', $table_primary_id)->first();
            }
            if (!empty($table_name) && $table_name == "masters") {
                $table_data = DB::table($table_name)->where('combo_name', $table_primary_id)->first();
                $table_primary_id = @$table_data->id;
            }


            if (!empty($table_data)) {
                $student_id_fld = 'student_id';
                if ($table_name == 'students') {
                    $student_id_fld = 'id';
                }
                $student_id = 0;
                if (@$table_data->$student_id_fld) {
                    $student_id = @$table_data->$student_id_fld;
                }
                if (!empty($student_id) || $student_id == 0) {
                    $is_lock_submit = $this->_isStudentLockAndSubmit($student_id);
                    if ($is_unlock_allow) {
                        $is_lock_submit = 1;
                    }
                    $user_id = 0;
                    if (Session::get('role_id')) {
                        if (Session::get('role_id') == Config::get('global.student')) {
                            $user_id = Auth::guard('student')->user()->id;
                        } else {
                            $user_id = Auth::user()->id;
                        }
                    }
                    $role_id = Session::get('role_id');
                    if (@$role_id) {

                    } else {
                        $role_id = 0;
                    }
                    if ($is_lock_submit) {
                        $custom_data = array(
                            'student_id' => $student_id,
                            'table_primary_id' => $table_primary_id,
                            'table_name' => $table_name,
                            'table_data' => json_encode($table_data),
                            'user_id' => $user_id,
                            'user_role' => $role_id,
                            'user_ip_address' => Config::get('global.request_client_ip'),
                            'user_mac_address' => $macAddress,
                            'form_type' => $form_type,
                            'exam_year' => $exam_year,
                            // 'exam_month'=>$exam_month,
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s")
                        );
                        if (StudentLog::insert($custom_data)) {
                            $status = true;
                        }
                    }
                }
            }
        }
        return $status;

    }

    public function _my_mac_address()
    {
        /* $ip = Config::get('global.CURRENT_IP');
		$currentMacAddress = shell_exec("arp -a " . $ip);
		$currentMacAddress = substr($currentMacAddress, 100,26);
		$myMacAddr = trim($currentMacAddress); */
        $currentMacAddress = shell_exec("getmac");
        $currentMacAddress = substr($currentMacAddress, 159, 20);
        $myMacAddr = trim($currentMacAddress);

        return @$myMacAddr;
    }

    public function _isStudentLockAndSubmit($student_id = null)
    {
        $masterrecord = Application::where('student_id', $student_id)->first();
        $fld = "locksumbitted";
        $isLockAndSubmit = false;
        if (isset($masterrecord[$fld]) && $masterrecord[$fld] == 1) {
            $isLockAndSubmit = true;
        }
        return $isLockAndSubmit;
    }

    public function getFromGlobalVariable($indexItem = null)
    {
        // $indexItem = 'revalMarksDefaultPageLimit';
        $response['status'] = false;
        $response['error'] = null;
        $response['data'] = null;
        // Path to the global.php file
        $filePath = config_path('backend_changeable_global.php');
        $indexItem = Crypt::decrypt($indexItem);
        if (@$indexItem && file_exists($filePath)) {
            $config = include $filePath;
            if (isset($config[$indexItem])) {
                $response['status'] = true;
                $response['data'] = $config[$indexItem];
            } else if (isset($config[0][$indexItem])) {
                $response['status'] = true;
                $response['data'] = $config[0][$indexItem];
            } else {
                $response['error'] = $indexItem . " does not read in the file.";
            }
        } else {
            $response['error'] = 'File not not found.';
        }
        return $response;
    }

    public function updateSingleScreenDetails($table_name = null, $comobo_name = null, $input_value = null, $field_name = null)
    {
        //table_name Crypt,comobo_name Crypt,input_value Normal, field_name Crypt
        $response['status'] = false;
        $response['error'] = null;
        $response['data'] = null;

        $table_name = Crypt::decrypt($table_name);
        if (@$table_name && $table_name == 'masters') {
            if (@$comobo_name) {
                $comobo_name = Crypt::decrypt($comobo_name);
                $this->updateStudentLog(@$table_name, $comobo_name, 'masters', '1');
                $createtesttable = DB::table($table_name)->where('combo_name', $comobo_name)->update(['option_val' => @$input_value]);
                $response['status'] = true;
            }
        } else if (@$table_name && $table_name == 'exam_late_fee_dates') {
            $record_id = $comobo_name;
            if (@$record_id) {
                $field_name = Crypt::decrypt($field_name);
                $record_id = Crypt::decrypt($record_id);
                $this->updateStudentLog(@$table_name, $record_id, 'exam_late_fee_dates', '1');
                $createtesttable = DB::table($table_name)->where('id', $record_id)->update([$field_name => @$input_value]);
                $response['status'] = true;
            }
        }
        return $response;
    }

    public function getFromMasterDetils($comobo_name = null, $table_name = 'masters')
    {
        $response['status'] = false;
        $response['error'] = null;
        $response['data'] = null;

        if (@$table_name && $table_name == 'masters') {
            if (@$comobo_name) {
                $comobo_name = Crypt::decrypt($comobo_name);
                $details = DB::table($table_name)->where('combo_name', $comobo_name)->first();
                if (@$details) {
                    $response['data'] = $details->option_val;
                }
                $response['status'] = true;
            }
        }
        return $response;
    }

    public function getExamLateFeeDetails($type = 0)
    {
        $masters = false;
        $is_supplementary = 0;
        if ($type == 'supplementary') {
            $is_supplementary = 1;
        }
        $sql = "select * from rs_exam_late_fee_dates  where deleted_at is null and is_supplementary = " . $is_supplementary . " order by is_supplementary,stream,gender_id,late_fee ;";
        $dates = DB::select($sql);
        if (!empty($dates)) {
            $masters = $dates;
        }

        return $masters;
    }

    public function getTheoryExaminersList()
    {
        $theoryexaminer_role_id = Config('global.theoryexaminer');
        $conditions = array();

        if (@$theoryexaminer_role_id) {
            $conditions = array(
                'users.active' => 1,
                'model_has_roles.role_id' => $theoryexaminer_role_id
            );
        }
        $queryOrder = "CAST(id AS DECIMAL(10,0)) ASC";
        $master = ModelHasRole::join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->where($conditions)
            ->whereNull('users.deleted_at')
            ->orderByRaw($queryOrder)
            ->get(['id', 'ssoid', 'name']);
        return $master;
    }


    public function getStudentsTheorySubjectsList($limit = 50)
    {
        $conditions = array();

        $current_admission_session_id = Config::get("global.theory_copy_checking_exam_year");
        $current_exam_month_id = Config::get("global.theory_copy_checking_exam_month");

        $conditions ["student_allotments.exam_year"] = $current_admission_session_id;
        $conditions ["student_allotments.exam_month"] = $current_exam_month_id;

        if (@$is_supplementary && isset($is_supplementary) && !empty($is_supplementary)) {
            $conditions['student_allotments.supplementary'] = $is_supplementary;
        }
        if (@$course && isset($course) && !empty($course)) {
            $conditions['student_allotments.course'] = $course;
        }
        $studentLists = StudentAllotmentMark::
        join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')
            ->whereNull('student_allotments.deleted_at')
            ->whereNull('student_allotment_marks.deleted_at')
            ->where('student_allotment_marks.is_exclude_for_theory', "!=", 1)
            ->where($conditions)
            ->orderby("student_allotment_marks.student_id")
            ->orderby("student_allotment_marks.subject_id")
            ->limit($limit)
            ->get(["student_allotment_marks.subject_id", "student_allotment_marks.student_id"]);
        $fianlArr = [];
        if (@$studentLists) {
            foreach ($studentLists as $k => $student) {
                $fianlArr[$student->student_id][$student->subject_id] = $student->subject_id;
            }
        }
        return $fianlArr;
    }

    public function getSubmitStudentsTheoryMarks($student_id = null, $subject_id = null, $final_theory_marks = null, $theory_absent = null, $theory_examiner_id = null)
    {
        // echo "TTTTTTT";die;
        $status = false;
        $error = null;
        $data = null;
        $theoryexaminer = Config('global.theoryexaminer');
        $isValidExaminer = false;
        $custom_component_obj = new CustomComponent;
        $isDateAllow = $custom_component_obj->_checkCopyCheckingTheoryMarksEntryAllowOrNotAllow();
        if ($isDateAllow) {
            $isValidExaminerCounter = ModelHasRole::where('model_id', $theory_examiner_id)
                ->where('role_id', $theoryexaminer)->count();
            if ($isValidExaminerCounter > 0) {
                $isValidExaminer = true;
            } else {
                $error = "Theory Examiner not found!";
            }
        } else {
            $error = "Date is closed!";
        }
        // dd($isValidExaminerCounter);

        $current_admission_session_id = Config::get("global.theory_copy_checking_exam_year");
        $current_exam_month_id = Config::get("global.theory_copy_checking_exam_month");

        if ($isValidExaminer) {
            // $isValidStudentSubject = false;
            $conditions = array();

            $conditions["student_allotment_marks.exam_year"] = $current_admission_session_id;
            $conditions["student_allotment_marks.exam_month"] = $current_exam_month_id;

            $studentSubjectDetails = StudentAllotmentMark::where('student_id', $student_id)
                ->where($conditions)->where('is_exclude_for_theory', '!=', 1)->where('subject_id', $subject_id)->first();
            if (@$studentSubjectDetails->id) {
                //check validaiton
                $tempResponae = array();
                $theorycustomcomponent = new ThoeryCustomComponent();
                $svdata['data'][0]['final_theory_marks'] = $final_theory_marks;
                $svdata['data'][0]['theory_absent'] = $theory_absent;
                $svdata['data'][0]['theory_absent_nr'] = 'no';
                $isValid = true;
                $theory_custom_component_obj = new ThoeryCustomComponent;
                $getMaxMarks = $theory_custom_component_obj->getTheoryMaxMarks(@$subject_id);
                $max_marks = @$getMaxMarks->theory_max_marks;

                $getMinMarks = $theory_custom_component_obj->getTheoryMinMarks(@$subject_id);
                $min_marks = @$getMinMarks->theory_min_marks;

                $subjectMaxMarks = @$max_marks;
                $subjectMinMarks = @$min_marks;

                $tempResponse = $theorycustomcomponent->isvalidtheorymarks($svdata, $subjectMaxMarks, $subjectMinMarks);
                $isAnyValidationError = true;

                if ($tempResponse['isValid'] == true) {
                    $isAnyValidationError = false;
                } else {
                    $error = $tempResponse['errors'];
                }
                if ($isAnyValidationError) {

                } else {
                    $studentTheoryCopyCheckingMarkDetails = StudentTheoryCopyCheckingMark::where('student_allotment_marks_id', $studentSubjectDetails->id)->first();
                    $saveDetails = array();
                    $fld = 'student_id';
                    $saveDetails[$fld] = $$fld;
                    $fld = 'subject_id';
                    $saveDetails[$fld] = $$fld;
                    $fld = 'final_theory_marks';
                    $saveDetails[$fld] = $$fld;
                    $fld = 'theory_examiner_id';
                    $saveDetails[$fld] = $$fld;
                    $fld = 'theory_max_marks';
                    $saveDetails[$fld] = $max_marks;
                    $fld = 'student_allotment_marks_id';
                    $saveDetails[$fld] = @$studentSubjectDetails->id;
                    $fld = 'student_allotment_id';
                    $saveDetails[$fld] = @$studentSubjectDetails->student_allotment_id;
                    $fld = 'exam_year';
                    $saveDetails[$fld] = @$studentSubjectDetails->exam_year;
                    $fld = 'exam_month';
                    $saveDetails[$fld] = @$studentSubjectDetails->exam_month;
                    $fld = 'is_theory_marks_entered_by_api';
                    $saveDetails[$fld] = 5;

                    $fld = 'theory_absent';
					if($theory_absent == "'on'"){
						$$fld = '1'; 
					}
                    $saveDetails[$fld] = $$fld;
                    // dd($saveDetails);
                    $type = null;

                    if (@$studentTheoryCopyCheckingMarkDetails->id) {
                        $fld = 'theory_marks_update_count';
                        $saveDetails[$fld] = ($studentTheoryCopyCheckingMarkDetails->$fld + 1);

                        //Data Updation Log Enteries
                        $table_primary_id = $studentTheoryCopyCheckingMarkDetails->id;
                        $table_name = 'student_theory_copy_checking_marks';
                        $form_type = 'StudentTheoryCopyCheckingMark';
                        $controller_obj = new Controller;
                        $log_status = $controller_obj->updateStudentLog($table_name, $table_primary_id, $form_type);
                        //Data Updation Log Enteries
                        // dd($log_status);
                        StudentTheoryCopyCheckingMark::where('id', $studentTheoryCopyCheckingMarkDetails->id)->update($saveDetails);
                        $type = 'updated';
                    } else {
                        $fld = 'theory_marks_update_count';
                        $saveDetails[$fld] = 0;
                        StudentTheoryCopyCheckingMark::create($saveDetails);
                        $type = 'submitted';
                    }
                    $status = true;
                    $data = " Student subject marks has been " . $type . " successfully.";
                }
            } else {
                $error = "Student subject combination not found !";
            }
        }
        $response = array('status' => $status, "data" => $data, "error" => $error);
        // dd($response);
        return $response;
    }


    public function getStudentEnrollmentList($limit = 500000)
    {
        $conditions = array();

        $current_admission_session_id = Config::get("global.theory_copy_checking_exam_year");
        $current_exam_month_id = Config::get("global.theory_copy_checking_exam_month");

        $conditions ["student_allotments.exam_year"] = $current_admission_session_id;
        $conditions ["student_allotments.exam_month"] = $current_exam_month_id;

        if (@$is_supplementary && isset($is_supplementary) && !empty($is_supplementary)) {
            $conditions['student_allotments.supplementary'] = $is_supplementary;
        }
        if (@$course && isset($course) && !empty($course)) {
            $conditions['student_allotments.course'] = $course;
        }
        $studentLists = StudentAllotmentMark::
        join('student_allotments', 'student_allotments.student_id', '=', 'student_allotment_marks.student_id')
            ->whereNull('student_allotments.deleted_at')
            ->whereNull('student_allotment_marks.deleted_at')
            ->where('student_allotment_marks.is_exclude_for_theory', "!=", 1)
            ->where($conditions)
            ->orderby("student_allotment_marks.student_id")
            ->orderby("student_allotment_marks.subject_id")
            ->limit($limit)
            ->get(["student_allotments.enrollment", "student_allotments.student_id"]);
        $fianlArr = [];
        if (@$studentLists) {
            foreach ($studentLists as $k => $student) {
                $fianlArr[$student->student_id]['enrollment'] = $student->enrollment;
            }
        }
        return $fianlArr;
    }
}

