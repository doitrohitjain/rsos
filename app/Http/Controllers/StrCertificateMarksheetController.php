<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use App\Helper\CustomHelper;
use Auth;
use Config;
use DB;
use File;
use Illuminate\Http\Request;
use PDF;
use Response;
use Session;

class StrCertificateMarksheetController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:bulk_download', ['only' => ['downloadBulkDocument']]);
    }

    // sample enrollment :
    // pass enrollment : 08008224007,20021224017,30024224004,09042224032,16027224063
    // subject count wise pass enrollment (7,5,5,2,5) : 01018212029,01006203108,01018203075,08008224007,01020213023

    // fail enrollment : 12281224065,12285224010,12283224001,12276224001,33005224001
    // subject count wise fail enrollment (7,5,7,5,5) : 01001182076,01001183188,01001193030,01001192041,01001192046

    // pass & FAIL ENROLLEMNT : 08008224007,20021224017,30024224004,09042224032,16027224063,12281224065,12285224010,12283224001,12276224001,33005224001
    // pass enrollment (ai_code : 1020) : 01020213023,01020213038,01020213044,01020213081,01020213047,01020213085,01020213087,01020213095,01020213097,01020213104

    //(ai_code : 20021,6002,9042) : // 20021224017,09042224032,06002225008,06002225011,20021225005
    // aicode : 23002,12281

    // ->withInput($request->all())

    public function downloadBulkDocument(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $current_admission_year_string = CustomHelper::_get_selected_sessions();
        //$current_admission_year_string = Config::get('global.current_admission_year_string');
        $title = "Generate and Download Marksheet/Certificate/STR";
        $formId = ucfirst(str_replace(" ", "-", $title));
        $current_admission_session_id = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');
        $customComponentObj = new CustomComponent;

        $aicenter_dropdown_download_arr = $customComponentObj->getAiCenters(null, null, 1);
        $aicenter_dropdown_arr = $customComponentObj->getAiCenters(null, null, 1);
        $combo_name = 'course';
        $course_dropdown_arr = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_dropdown_arr = $this->master_details($combo_name);
        $document_type_dropdown_arr = array('1' => 'Marksheet-Pass', '2' => 'Marksheet-Fail', '3' => 'Certificate', '4' => 'STR');
        // $document_type_dropdown_arr = array('1'=>'Marksheet-Pass','2'=>'Marksheet-Fail','3'=>'Certificate');

        if (count(@$request->all()) > 0) {
            $enrollment_arr = array();
            $ai_code_arr = array();


            if (empty(@$request->document_type)) {
                return redirect()->back()->with('error', 'Please select correct "Document Type" field');

                //-- 1 condition
            } else if (!empty(@$request->enrollment) && @$request->formType == 1) {   // form type 1 = enrollment wise  generate & download both
                $enrollment_arr = explode(',', @$request->enrollment);
                if (!empty(@$enrollment_arr)) {
                    $pdfPath = $this->documentDownloadByEnrollmentArr($request, $enrollment_arr);

                    if ($pdfPath != false) {
                        return (Response::download($pdfPath));
                    } else {
                        return redirect()->back()->with('error', 'Data does not found accroding to selected fields or Exam Cancelled.');
                    }
                }

                //-- 2 condition condtion
            } else if (!empty(@$request->ai_code) && @$request->formType == 2) {  // form type 2 = Aicenter wise download only
                $documentTypePath = '';
                if (!empty($request->document_type) && $request->document_type == 1) {
                    $documentTypePath = 'pass-marksheets';
                } else if (!empty($request->document_type) && $request->document_type == 2) {
                    $documentTypePath = 'fail-marksheets';
                } else if (!empty($request->document_type) && $request->document_type == 3) {
                    $documentTypePath = 'certificate';
                } else if (!empty($request->document_type) && $request->document_type == 4) {
                    $documentTypePath = 'str';
                }

                $ai_code_arr = @$request->ai_code;
                if (!empty(@$ai_code_arr)) {
                    foreach (@$ai_code_arr as $ai_code) {
                        $folderPath = public_path('bulk_documents' . DIRECTORY_SEPARATOR . $documentTypePath . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream . DIRECTORY_SEPARATOR . $ai_code);
                        $zip_file_name = $ai_code . '.zip';
                        if (is_dir($folderPath)) {
                            $destdirTemp = 'allzipsave/bulk/' . $current_admission_year_string . '/' . @$request->stream . '/' . @$request->course . '/' . $documentTypePath;
                            $folderPathsTemp = public_path($destdirTemp);
                            File::makeDirectory($folderPathsTemp, $mode = 0777, true, true);
                            $zip_file_name = $zip_file_name;
                            $zip_file_name = 'allzipsave/bulk/' . $current_admission_year_string . '/' . @$request->stream . '/' . @$request->course . '/' . $documentTypePath . "/" . $zip_file_name;
                            $zip_file_name = public_path($zip_file_name);
                            $zip_file = $this->_zipAndDownload($folderPath, $zip_file_name);
                            return (Response::download($zip_file));
                        } else {
                            return redirect()->back()->with('error', 'Please generate file first, then download it.');
                        }
                    }
                }
            } else if (!empty(@$request->ai_code) && @$request->formType == 3) {      // form type 3 = Aicenter wise Generate only
                $ai_code_arr = @$request->ai_code;

                if (!empty(@$ai_code_arr)) {
                    $pdfPath = $this->documentDownloadByAiCodeArr($request, $ai_code_arr);
                    if ($pdfPath != null) {
                        return redirect()->back()->with('message', 'File has been generateed successfully.');
                    } else {
                        return redirect()->back()->with('error', 'Data Not found.');
                    }
                }

                //-- 4 condition condtion
            } else if (@$request->formType == 4) {  // form type 4 = bulk download only
                $documentTypePath = '';
                if (!empty($request->document_type) && $request->document_type == 1) {
                    $documentTypePath = 'pass-marksheets';
                } else if (!empty($request->document_type) && $request->document_type == 2) {
                    $documentTypePath = 'fail-marksheets';
                } else if (!empty($request->document_type) && $request->document_type == 3) {
                    $documentTypePath = 'certificate';
                } else if (!empty($request->document_type) && $request->document_type == 4) {
                    $documentTypePath = 'str';
                }

                $ai_code_arr = $customComponentObj->getAiCenters(null, null, 1); // $formId=null,$isPaginate=true

                if (!empty(@$ai_code_arr)) { // bulk download zip create type of document
                    foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                        if (!empty($ai_code_id)) {
                            $src = public_path('bulk_documents' . DIRECTORY_SEPARATOR . $documentTypePath . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream . DIRECTORY_SEPARATOR . $ai_code_id);
                            if (is_dir($src)) {
                                if (!empty($request->document_type) && $request->document_type == 1) {
                                    $zip_file_name = 'bulk-pass-marksheets' . date('dmY-H-i-s') . '.zip';
                                } else if (!empty($request->document_type) && $request->document_type == 2) {
                                    $zip_file_name = 'bulk-fail-marksheets' . date('dmY-H-i-s') . '.zip';
                                } else if (!empty($request->document_type) && $request->document_type == 3) {
                                    $zip_file_name = 'bulk-certificate' . date('dmY-H-i-s') . '.zip';
                                } else if (!empty($request->document_type) && $request->document_type == 4) {
                                    $zip_file_name = 'bulk-str' . date('dmY-H-i-s') . '.zip';
                                }

                                $dst = public_path('bulk_documents' . DIRECTORY_SEPARATOR . $documentTypePath . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream . DIRECTORY_SEPARATOR . 'bulk');
                                $srcFile = opendir($src);
                                @mkdir($dst);
                                while ($file = readdir($srcFile)) {
                                    if (($file != '.') && ($file != '..')) {
                                        copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                                    }
                                }
                                closedir($srcFile);
                            }
                        }
                    }
                }
                if (!empty($dst)) {
                    $destdirTemp = 'allzipsave/bulk/' . $current_admission_year_string . '/' . @$request->stream . '/' . @$request->course . '/' . $documentTypePath;
                    $folderPathsTemp = public_path($destdirTemp);
                    File::makeDirectory($folderPathsTemp, $mode = 0777, true, true);

                    $zip_file_name = 'allzipsave/bulk/' . $current_admission_year_string . '/' . @$request->stream . '/' . @$request->course . '/' . $documentTypePath . "/" . $zip_file_name;

                    $zip_file_name = public_path($zip_file_name);
                    $zip_file = $this->_zipAndDownload($dst, $zip_file_name);
                    //$zip_file = $this->_zipAndDownload($dst,$zip_file_name);
                    return (Response::download($zip_file));
                } else {
                    return redirect()->back()->with('error', 'Please generate file first, then download it.');
                }

                //-- 5 condition condtion
            } else if (@$request->formType == 5) {  // form type 5 = bulk Generate only
                $fileData = $this->documentDownloadByAllAiCenter($request);
                return redirect()->back()->with('message', 'Files has been generateed successfully.');

            } else if (@$request->formType == 6) {  // form type 6 = temp custom bulk Generate only
                $fileData = $this->tempdocumentDownloadByAllAiCenter($request);
                return redirect()->back()->with('message', 'Temp Files has been generateed successfully.');

            } else {
                return redirect()->back()->with('error', 'Request is not valid');
            }
        }
        return view('str_certificate_marksheet.download_bulk_document', compact('title', 'formId', 'aicenter_dropdown_arr', 'aicenter_dropdown_download_arr', 'course_dropdown_arr', 'stream_dropdown_arr', 'document_type_dropdown_arr'));
    }

    public function documentDownloadByEnrollmentArr($request, $enrollment_arr)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $current_admission_year_string = CustomHelper::_get_selected_sessions();
        //$current_admission_session_id = Config::get('global.current_admission_session_id');
        $exam_month = Config::get('global.current_exam_month_id');
        $customComponentObj = new CustomComponent;
        $ControllerObj = new Controller;

        if (!empty(@$request->document_type) && @$request->document_type == '1') {  // 1 = marsheet Fail and Pass Both;
            // dd($request->all());
            $finalPdfHtml = '';
            $enrollment_count = count($enrollment_arr);
            $i = 1;
            foreach (@$enrollment_arr as $enrollment) {
                if (!empty($enrollment) && $enrollment != 'NULL') {
                    $marksheetPdfHtml = $customComponentObj->downloadCustomSingleMarksheetPdf(@$enrollment, @$request->type);
                    if (!empty($marksheetPdfHtml->html)) {
                        if ($enrollment_count == $i) {
                            $finalPdfHtml .= @$marksheetPdfHtml->html;
                        } else {
                            $finalPdfHtml .= @$marksheetPdfHtml->html . "<div class='page-break'></div>";
                        }
                    }
                    $i++;
                }

            }
            // echo $finalPdfHtml; die;

            if (!empty($finalPdfHtml)) {
                $path = public_path('bulk_documents\custom' . DIRECTORY_SEPARATOR . 'marksheet-' . date("dmY-H-i-s") . '.pdf');
                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                $pdf->setTimeout(60000);
                $pdf->save($path, $pdf, true);
                return $path;
            } else {
                return false;
            }

        } else if (!empty(@$request->document_type) && @$request->document_type == '3') {  // 3 = certificate only Pass
            $finalPdfHtml = '';
            $i = 1;
            $passEnrollmentCount = $customComponentObj->getCountPassEnrollmentByEnrollmentArr($enrollment_arr);
            foreach (@$enrollment_arr as $enrollment) {
                $checkResultAcoordingStatus = $customComponentObj->getResultByEnrollment($enrollment, 'PASS');
                if (@$checkResultAcoordingStatus == 'PASS') {
                    if (!empty($enrollment)) {
                        $certificatePdfHtml = $customComponentObj->downloadCustomSingleCertificatePdf(@$enrollment, 'PASS');
                        if ($passEnrollmentCount == $i) {
                            $finalPdfHtml .= @$certificatePdfHtml->html;
                        } else {
                            $finalPdfHtml .= @$certificatePdfHtml->html . "<div class='page-break'></div>";
                        }
                    }
                    $i++;
                }
            }

            if (!empty($finalPdfHtml)) {
                $path = public_path('bulk_documents\custom' . DIRECTORY_SEPARATOR . 'certificate-' . date("dmY-H-i-s") . '.pdf');
                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                $pdf->setTimeout(60000);
                $pdf->save($path, $pdf, true);
                return $path;
            } else {
                return false;
            }
        }
    }

    public function documentDownloadByAiCodeArr($request, $ai_code_arr)
    {
        ini_set('memory_limit', '100000M');
        ini_set('max_execution_time', '-1');

        $current_admission_session_id = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');
        $customComponentObj = new CustomComponent;
        $ControllerObj = new Controller;
        $limit = 100;

        if (!empty(@$request->document_type) && @$request->document_type == '1') {  // 1 = marksheet Pass only
            $folderPath = public_path('bulk_documents\pass-marksheets' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
            foreach (@$ai_code_arr as $ai_code) {
                $offset = 0;
                $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code, @$request->course, @$request->stream, 'PASS');
                $loopLimit = intval(ceil($enrollmentCount / $limit));
                for ($loop = 0; $loop < $loopLimit; $loop++) {
                    $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code, @$request->course, @$request->stream, 'PASS', $offset, $limit);
                    if (!empty($enrollmentList)) {
                        $finalPdfHtml = '';
                        $enrollmentListcount = count($enrollmentList);
                        $i = 1;
                        foreach (@$enrollmentList as $enrollmentObj) {
                            $enrollment = $enrollmentObj->enrollment;
                            if (!empty($enrollment) && $enrollment != 'NULL') {
                                $marksheetPdfHtml = $customComponentObj->downloadCustomSingleMarksheetPdf(@$enrollment, @$request->type);
                                if (!empty($marksheetPdfHtml->html)) {
                                    if ($enrollmentListcount == $i) {
                                        $finalPdfHtml .= @$marksheetPdfHtml->html;
                                    } else {
                                        $finalPdfHtml .= @$marksheetPdfHtml->html . "<div class='page-break'></div>";
                                    }
                                }
                                $i++;
                            }
                        }
                        if (!empty($finalPdfHtml)) {
                            $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code . DIRECTORY_SEPARATOR . $ai_code . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                            $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                            $pdf->setTimeout(60000);
                            $pdf->save($path, $pdf, true);
                            // dd($path);
                            // return $path;
                        }
                    }
                    $offset += $limit;
                }
                if (!empty($finalPdfHtml)) {
                    return $folderPath;
                }
            }
        } else if (!empty(@$request->document_type) && @$request->document_type == '2') {  // 2 = marksheet Fail only
            $folderPath = public_path('bulk_documents\fail-marksheets' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
            foreach (@$ai_code_arr as $ai_code) {
                $offset = 0;
                $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code, @$request->course, @$request->stream, 'XXXX');
                $loopLimit = intval(ceil($enrollmentCount / $limit));
                for ($loop = 0; $loop < $loopLimit; $loop++) {
                    $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code, @$request->course, @$request->stream, 'XXXX', $offset, $limit);

                    if (!empty($enrollmentList)) {
                        $finalPdfHtml = '';
                        $enrollmentListcount = count($enrollmentList);
                        $i = 1;
                        foreach (@$enrollmentList as $enrollmentObj) {
                            $enrollment = $enrollmentObj->enrollment;
                            if (!empty($enrollment) && $enrollment != 'NULL') {
                                $marksheetPdfHtml = $customComponentObj->downloadCustomSingleMarksheetPdf(@$enrollment, @$request->type);

                                if (!empty($marksheetPdfHtml->html)) {
                                    if ($enrollmentListcount == $i) {
                                        $finalPdfHtml .= @$marksheetPdfHtml->html;
                                    } else {
                                        $finalPdfHtml .= @$marksheetPdfHtml->html . "<div class='page-break'></div>";
                                    }
                                }
                                $i++;
                            }

                        }
                        //  print_r($finalPdfHtml);

                        if (!empty($finalPdfHtml)) {
                            $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code . DIRECTORY_SEPARATOR . $ai_code . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                            $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                            $pdf->setTimeout(60000);
                            $pdf->save($path, $pdf, true);

                            // return $path;
                        }
                    }
                    $offset += $limit;
                }

                if (!empty($finalPdfHtml)) {
                    return $folderPath;
                }
            }

        } else if (!empty(@$request->document_type) && @$request->document_type == '3') {  // 3 = certificate only Pass
            $folderPath = public_path('bulk_documents\certificate' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
            foreach (@$ai_code_arr as $ai_code) {
                $offset = 0;
                $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code, @$request->course, @$request->stream, 'PASS');

                $loopLimit = intval(ceil($enrollmentCount / $limit));
                for ($loop = 0; $loop < $loopLimit; $loop++) {
                    $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code, @$request->course, @$request->stream, 'PASS', $offset, $limit);
                    if (!empty($enrollmentList)) {
                        $finalPdfHtml = '';
                        $enrollmentListcount = count($enrollmentList);
                        $i = 1;
                        foreach (@$enrollmentList as $enrollmentObj) {
                            $enrollment = $enrollmentObj->enrollment;
                            if (!empty($enrollment) && $enrollment != 'NULL') {
                                $checkResultAcoordingStatus = $customComponentObj->getResultByEnrollment($enrollment, @$request->type);
                                if (@$checkResultAcoordingStatus == 'PASS') {
                                    $certificatePdfHtml = $customComponentObj->downloadCustomSingleCertificatePdf(@$enrollment, 'pass');
                                    if (!empty($certificatePdfHtml->html)) {
                                        if ($enrollmentListcount == $i) {
                                            $finalPdfHtml .= @$certificatePdfHtml->html;
                                        } else {
                                            $finalPdfHtml .= @$certificatePdfHtml->html . "<div class='page-break'></div>";
                                        }
                                    }
                                    $i++;
                                }
                            }
                        }
                        if (!empty($finalPdfHtml)) {
                            $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code . DIRECTORY_SEPARATOR . $ai_code . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                            $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                            $pdf->setTimeout(60000);
                            $pdf->save($path, $pdf, true);
                            // return $path;
                        }
                    }
                    $offset += $limit;
                }

                if (!empty($finalPdfHtml)) {
                    return $folderPath;
                }
            }

        } else if (!empty(@$request->document_type) && $request->document_type == '4') {  // 4 = STR Pass & fail Both

            $folderPath = public_path('bulk_documents\str' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
            foreach (@$ai_code_arr as $ai_code) {
                $offset = 0;
                $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code, @$request->course, @$request->stream, null);
                $loopLimit = intval(ceil($enrollmentCount / $limit));
                //$loopLimit = $enrollmentCount;
                for ($loop = 0; $loop < $loopLimit; $loop++) {
                    $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code, @$request->course, @$request->stream, null, $offset, $limit);

                    $finalPdfHtml = '';
                    if (!empty(@$enrollmentList)) {
                        $enrollmentListcount = count($enrollmentList);
                        $counter = 1;
                        $finalArray = array();
                        foreach (@$enrollmentList as $enrollmentObj) {
                            $enrollment = $enrollmentObj->enrollment;

                            if (!empty($enrollment) && $enrollment != 'NULL') {
                                $strData = $customComponentObj->downloadCustomSingleStrPdf(@$enrollment, @$request->stream, @$request->type, $counter);

                                if (!empty($strData)) {
                                    $finalArray[] = $strData;
                                }
                                $counter++;
                            }
                        }
                        $dataSave = $finalArray;

                        $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code . DIRECTORY_SEPARATOR . $ai_code . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                        $pdf = PDF::loadView('str_certificate_marksheet.str_bigfont_view', compact('dataSave', 'ai_code'))->setOrientation('landscape');
                        $pdf->setTimeout(60000);
                        $pdf->setOption('page-size', 'Legal');
                        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                        $pdf->setOption("encoding", "UTF-8");
                        $pdf->setOption('margin-left', '3mm');
                        $pdf->setOption('margin-right', '3mm');
                        $pdf->setOption('margin-top', '5mm');

                        $pdf->save($path, $pdf, true);
                        // return $path;

                        $offset += $limit;
                    }
                }
                if (!empty($finalPdfHtml)) {
                    return $folderPath;
                }
            }
        }
    }

    public function documentDownloadByAllAiCenter($request)
    { // all ai_center bulk download
        ini_set('memory_limit', '100000M');
        ini_set('max_execution_time', '-1');

        $current_admission_session_id = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');
        $customComponentObj = new CustomComponent;
        $ControllerObj = new Controller;
        $limit = 100;
        $ai_code_arr = $customComponentObj->getAiCenters(null, null, 1); // $formId=null,$isPaginate=true
        //$ai_code_arr = array('50068'=>'50068','50069'=>'50069','50070'=>'50070','50072'=>'50072');
        if (!empty(@$ai_code_arr)) {
            if (!empty(@$request->document_type) && @$request->document_type == '1') {   // 1 = marsheet Pass only
                $folderPath = public_path('bulk_documents\pass-marksheets' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                    $offset = 0;
                    $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS');
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    $finalPdfHtml = '';
                    for ($loop = 0; $loop < $loopLimit; $loop++) {
                        $customComponentObj = new CustomComponent();
                        $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS', $offset, $limit);
                        if (!empty($enrollmentList)) {
                            $enrollmentListcount = count($enrollmentList);
                            $i = 1;
                            foreach ($enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $marksheetPdfHtml = $customComponentObj->downloadCustomSingleMarksheetPdf(@$enrollment, @$request->type);
                                    if (!empty($marksheetPdfHtml->html)) {
                                        if ($enrollmentListcount == $i) {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html;
                                        } else {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html . "<div class='page-break'></div>";
                                        }
                                    }
                                    $i++;
                                }
                            }

                            if (!empty($finalPdfHtml)) {
                                $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                                $pdf->setTimeout(60000);
                                $pdf->save($path, $pdf, true);
                                $offset += $limit;
                            }
                        }
                    }
                }

            } else if (!empty(@$request->document_type) && @$request->document_type == '2') {   // 2 = marsheet Fail only
                $folderPath = public_path('bulk_documents\fail-marksheets' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
                $i = 1;
                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                    $offset = 0;
                    $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'XXXX');
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    for ($loop = 0; $loop < $loopLimit; $loop++) {
                        $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'XXXX', $offset, $limit);
                        if (!empty($enrollmentList)) {
                            $finalPdfHtml = '';
                            $enrollmentListcount = count($enrollmentList);
                            $i = 1;
                            foreach (@$enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $marksheetPdfHtml = $customComponentObj->downloadCustomSingleMarksheetPdf(@$enrollment, @$request->type);
                                    if (!empty($marksheetPdfHtml->html)) {
                                        if ($enrollmentListcount == $i) {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html;
                                        } else {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html . "<div class='page-break'></div>";
                                        }
                                    }
                                    $i++;
                                }
                            }

                            if (!empty($finalPdfHtml)) {
                                $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                                $pdf->setTimeout(60000);
                                $pdf->save($path, $pdf, true);
                                $offset += $limit;
                            }
                        }
                    }
                }

            } else if (!empty(@$request->document_type) && @$request->document_type == '3') {  // 3 = certificate  only Pass
                $folderPath = public_path('bulk_documents\certificate' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                    $offset = 0;
                    $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS');
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    for ($loop = 0; $loop < $loopLimit; $loop++) {
                        $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS', $offset, $limit);
                        if (!empty($enrollmentList)) {
                            $finalPdfHtml = '';
                            $enrollmentListcount = count($enrollmentList);
                            $i = 1;
                            foreach (@$enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $certificatePdfHtml = $customComponentObj->downloadCustomSingleCertificatePdf(@$enrollment, 'pass');
                                    if (!empty($certificatePdfHtml->html)) {
                                        if ($enrollmentListcount == $i) {
                                            $finalPdfHtml .= @$certificatePdfHtml->html;
                                        } else {
                                            $finalPdfHtml .= @$certificatePdfHtml->html . "<div class='page-break'></div>";
                                        }
                                    }
                                    $i++;
                                }
                            }
                            if (!empty($finalPdfHtml)) {
                                $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                                $pdf->setTimeout(60000);
                                $pdf->save($path, $pdf, true);
                                $offset += $limit;
                            }
                        }
                    }
                }

            } else if (!empty(@$request->document_type) && $request->document_type == '4') {  // 4 = STR Pass & Fail
                $folderPath = public_path('bulk_documents\str' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                    $ai_code = $ai_code_id;

                    $offset = 0;
                    $enrollmentCount = $customComponentObj->getEnrollmentCountByAiCode(@$ai_code, @$request->course, @$request->stream, null);
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    for ($loop = 0; $loop < $loopLimit; $loop++) {
                        $enrollmentList = $customComponentObj->getEnrollmentListByAiCode(@$ai_code, @$request->course, @$request->stream, null, $offset, $limit);
                        $finalPdfHtml = '';
                        if (!empty(@$enrollmentList)) {
                            $enrollment_count = count($enrollmentList);
                            $counter = 1;
                            $finalArray = array();
                            foreach (@$enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $strData = $customComponentObj->downloadCustomSingleStrPdf(@$enrollment, @$request->stream, @$request->type, $counter);
                                    if (!empty($strData)) {
                                        $finalArray[] = $strData;
                                    }
                                    $counter++;
                                }
                            }
                            $dataSave = $finalArray;
                            $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                            $pdf = PDF::loadView('str_certificate_marksheet.str_bigfont_view', compact('dataSave', 'ai_code'))->setOrientation('landscape');
                            $pdf->setTimeout(60000);
                            $pdf->setOption('page-size', 'Legal');
                            $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                            $pdf->setOption("encoding", "UTF-8");
                            $pdf->setOption('margin-left', '1mm');
                            $pdf->setOption('margin-right', '3mm');
                            $pdf->setOption('margin-top', '5mm');
                            $pdf->save($path, $pdf, true);

                            $offset += $limit;
                        }
                    }
                }

            } else {
                return redirect()->back()->with('error', 'Please select correct "Document Type" field');
            }
        } else {
            return redirect()->back()->with('error', 'Ai Center does not found.');
        }
    }


    public function tempDocumentDownloadByAllAiCenter($request)
    { // all ai_center bulk download
        ini_set('memory_limit', '100000M');
        ini_set('max_execution_time', '-1');

        $current_admission_session_id = CustomHelper::_get_selected_sessions();
        $exam_month = Config::get('global.current_exam_month_id');
        $customComponentObj = new CustomComponent;
        $ControllerObj = new Controller;
        $limit = 100;
        $ai_code_arr = $customComponentObj->getAiCenters(null, null, 1); // $formId=null,$isPaginate=true


        if (!empty(@$ai_code_arr)) {
            if (!empty(@$request->document_type) && @$request->document_type == '1') {   // 1 = marsheet Pass only
                $folderPath = public_path('bulk_documents\temp_pass-marksheets' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);


                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {


                    $offset = 0;
                    $enrollmentCount = $customComponentObj->tempgetEnrollmentCountByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS');
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    $finalPdfHtml = '';

                    for ($loop = 0; $loop < $loopLimit; $loop++) {


                        $customComponentObj = new CustomComponent();
                        $enrollmentList = $customComponentObj->tempgetEnrollmentListByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS', $offset, $limit);


                        if (!empty($enrollmentList)) {
                            $enrollmentListcount = count($enrollmentList);
                            $i = 1;
                            foreach ($enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $marksheetPdfHtml = $customComponentObj->downloadCustomSingleMarksheetPdf(@$enrollment, @$request->type);
                                    if (!empty($marksheetPdfHtml->html)) {
                                        if ($enrollmentListcount == $i) {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html;
                                        } else {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html . "<div class='page-break'></div>";
                                        }
                                    }
                                    $i++;
                                }
                            }

                            if (!empty($finalPdfHtml)) {
                                $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                                $pdf->setTimeout(60000);
                                $pdf->save($path, $pdf, true);
                                $offset += $limit;
                            }
                        }
                    }
                }

            } else if (!empty(@$request->document_type) && @$request->document_type == '2') {   // 2 = marsheet Fail only
                $folderPath = public_path('bulk_documents\temp_fail-marksheets' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
                $i = 1;
                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                    $offset = 0;
                    $enrollmentCount = $customComponentObj->tempgetEnrollmentCountByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'XXXX');
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    for ($loop = 0; $loop < $loopLimit; $loop++) {
                        $enrollmentList = $customComponentObj->tempgetEnrollmentListByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'XXXX', $offset, $limit);
                        if (!empty($enrollmentList)) {
                            $finalPdfHtml = '';
                            $enrollmentListcount = count($enrollmentList);
                            $i = 1;
                            foreach (@$enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $marksheetPdfHtml = $customComponentObj->downloadCustomSingleMarksheetPdf(@$enrollment, @$request->type);
                                    if (!empty($marksheetPdfHtml->html)) {
                                        if ($enrollmentListcount == $i) {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html;
                                        } else {
                                            $finalPdfHtml .= @$marksheetPdfHtml->html . "<div class='page-break'></div>";
                                        }
                                    }
                                    $i++;
                                }
                            }

                            if (!empty($finalPdfHtml)) {
                                $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                                $pdf->setTimeout(60000);
                                $pdf->save($path, $pdf, true);
                                $offset += $limit;
                            }
                        }
                    }
                }

            } else if (!empty(@$request->document_type) && @$request->document_type == '3') {  // 3 = certificate  only Pass
                $folderPath = public_path('bulk_documents\temp_certificate' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                    $offset = 0;
                    $enrollmentCount = $customComponentObj->tempgetEnrollmentCountByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS');
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    for ($loop = 0; $loop < $loopLimit; $loop++) {
                        $enrollmentList = $customComponentObj->tempgetEnrollmentListByAiCode(@$ai_code_id, @$request->course, @$request->stream, 'PASS', $offset, $limit);
                        if (!empty($enrollmentList)) {
                            $finalPdfHtml = '';
                            $enrollmentListcount = count($enrollmentList);
                            $i = 1;
                            foreach (@$enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $certificatePdfHtml = $customComponentObj->downloadCustomSingleCertificatePdf(@$enrollment, 'pass');
                                    if (!empty($certificatePdfHtml->html)) {
                                        if ($enrollmentListcount == $i) {
                                            $finalPdfHtml .= @$certificatePdfHtml->html;
                                        } else {
                                            $finalPdfHtml .= @$certificatePdfHtml->html . "<div class='page-break'></div>";
                                        }
                                    }
                                    $i++;
                                }
                            }
                            if (!empty($finalPdfHtml)) {
                                $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                                $pdf = PDF::loadView('str_certificate_marksheet.document_pdf_view', compact('finalPdfHtml'));
                                $pdf->setTimeout(60000);
                                $pdf->save($path, $pdf, true);
                                $offset += $limit;
                            }
                        }
                    }
                }

            } else if (!empty(@$request->document_type) && $request->document_type == '4') {  // 4 = STR Pass & Fail
                $folderPath = public_path('bulk_documents\temp_str' . DIRECTORY_SEPARATOR . $current_admission_session_id . DIRECTORY_SEPARATOR . @$request->stream . DIRECTORY_SEPARATOR . @$request->course . DIRECTORY_SEPARATOR . 'stream' . @$request->stream);
                foreach (@$ai_code_arr as $ai_code_id => $ai_code) {
                    $ai_code = $ai_code_id;
                    $offset = 0;
                    $enrollmentCount = $customComponentObj->tempgetEnrollmentCountByAiCode(@$ai_code, @$request->course, @$request->stream, null);
                    $loopLimit = intval(ceil($enrollmentCount / $limit));
                    for ($loop = 0; $loop < $loopLimit; $loop++) {
                        $enrollmentList = $customComponentObj->tempgetEnrollmentListByAiCode(@$ai_code, @$request->course, @$request->stream, null, $offset, $limit);
                        $finalPdfHtml = '';
                        if (!empty(@$enrollmentList)) {
                            $enrollment_count = count($enrollmentList);
                            $counter = 1;
                            $finalArray = array();
                            foreach (@$enrollmentList as $enrollmentObj) {
                                $enrollment = $enrollmentObj->enrollment;
                                if (!empty($enrollment) && $enrollment != 'NULL' && $enrollment != '') {
                                    $strData = $customComponentObj->downloadCustomSingleStrPdf(@$enrollment, @$request->stream, @$request->type, $counter);
                                    if (!empty($strData)) {
                                        $finalArray[] = $strData;
                                    }
                                    $counter++;
                                }
                            }
                            $dataSave = $finalArray;
                            $path = $folderPath . DIRECTORY_SEPARATOR . $ai_code_id . DIRECTORY_SEPARATOR . $ai_code_id . "-" . ($offset + 1) . '-' . ($offset + $limit) . '.pdf';
                            $pdf = PDF::loadView('str_certificate_marksheet.str_bigfont_view', compact('dataSave', 'ai_code'))->setOrientation('landscape');
                            $pdf->setTimeout(60000);
                            $pdf->setOption('page-size', 'Legal');
                            $pdf->setOption('footer-right', 'Page [page] of [toPage]');
                            $pdf->setOption("encoding", "UTF-8");
                            $pdf->setOption('margin-left', '1mm');
                            $pdf->setOption('margin-right', '3mm');
                            $pdf->setOption('margin-top', '5mm');
                            $pdf->save($path, $pdf, true);

                            $offset += $limit;
                        }
                    }
                }

            } else {
                return redirect()->back()->with('error', 'Please select correct "Document Type" field');
            }
        } else {
            return redirect()->back()->with('error', 'Ai Center does not found.');
        }
    }


}
	
