<?php

namespace App\Http\Controllers;

use App\models\ExamLateFeeDate;
use DB;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class ExamLateFeeDatesController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:examdate_add', ['only' => ['add']]);
        $this->middleware('permission:examdate_edit', ['only' => ['edit']]);
        $this->middleware('permission:examdate_index', ['only' => ['index']]);
        $this->middleware('permission:examdate_delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $exam_late_dates_arr = ExamLateFeeDate::all();
        $combo_name = 'stream_id';
        $stream_arr = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_arr = $this->master_details($combo_name);
        $combo_name = 'yesno';
        $yes_no = $this->master_details($combo_name);

        return view('ExamLateFeeDates.index', compact('exam_late_dates_arr', 'yes_no', 'stream_arr', 'gender_arr'));
    }

    public function add(Request $request)
    {
        $table = $model = "Exam Late Fee Dates";
        $page_title = $model;

        if (count($request->all()) > 0) {
            $modelObj = new ExamLateFeeDate;
            $validator = Validator::make($request->all(), $modelObj->rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $custom_data = array(
                'is_supplementary' => strip_tags($request->is_supplementary),
                'stream' => strip_tags($request->stream),
                'gender_id' => strip_tags($request->gender_id),
                'ordering' => strip_tags($request->ordering),
                'latefee_extra_days' => strip_tags($request->latefee_extra_days),
                'from_date' => date("Y-m-d 00:00:00", strtotime($request->from_date)),
                'to_date' => date("Y-m-d 23:59:59", strtotime($request->to_date)),
                'late_fee' => strip_tags($request->late_fee)
            );
            $newDate = ExamLateFeeDate::updateOrCreate($custom_data);
            if ($newDate) {
                return redirect()->route('examdateindex')->with('message', $model . ' successfully saved');
            } else {
                return redirect()->back()->with('error', 'Failed! Student not created');
            }
        }
        $combo_name = 'yesno';
        $yes_no = $this->master_details($combo_name);
        $combo_name = 'stream_id';
        $stream_arr = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_arr = $this->master_details($combo_name);
        return view('ExamLateFeeDates.add', compact('model', 'page_title', 'stream_arr', 'gender_arr', 'yes_no'));
    }

    public function edit(Request $request, $id)
    {
        $table = $model = "ExamLateFeeDate";
        $page_title = $model . ' Details';

        if (empty($id)) {
            return redirect()->route('index')->with('error', ' You are not authorized for this page');
        }
        $combo_name = 'yesno';
        $yes_no = $this->master_details($combo_name);
        $master = ExamLateFeeDate::where('id', $id)->first();
        if (count(@$request->all()) > 0) {
            $modelObj = new ExamLateFeeDate;
            $validator = Validator::make($request->all(), $modelObj->rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $custom_data = array(
                'id' => $id,
                'stream' => strip_tags($request->stream),
                'gender_id' => strip_tags($request->gender_id),
                'is_supplementary' => strip_tags($request->is_supplementary),
                'ordering' => strip_tags($request->ordering),
                'latefee_extra_days' => strip_tags($request->latefee_extra_days),
                'from_date' => date("Y-m-d 00:00:00", strtotime($request->from_date)),
                'to_date' => date("Y-m-d 23:59:59", strtotime($request->to_date)),
                'late_fee' => strip_tags($request->late_fee)
            );

            $newDate = ExamLateFeeDate::where('id', $id)->update($custom_data);

            if ($newDate) {
                return redirect()->route('examdateindex')->with('message', $model . ' successfully saved');
            } else {
                return redirect()->back()->with('error', 'Failed! Student not created');
            }
        }

        $combo_name = 'stream_id';
        $stream_arr = $this->master_details($combo_name);
        $combo_name = 'gender';
        $gender_arr = $this->master_details($combo_name);
        return view('ExamLateFeeDates.edit', compact('id', 'master', 'yes_no', 'model', 'page_title', 'stream_arr', 'gender_arr'));
    }


    public function destroy($id)
    {
        $deleteStatus = ExamLateFeeDate::where('id', $id)->delete();
        return response()->json(['success' => 'Record successfully Deleted']);
    }
}
