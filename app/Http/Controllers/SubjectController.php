<?php

namespace App\Http\Controllers;

use App\models\Subject;
use DB;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class SubjectController extends Controller
{
    function __construct()
    {
        //$this->middleware('permission:district-list', ['only' => ['index','store']]);
        //$this->middleware('permission:district-create', ['only' => ['create','store']]);
        //$this->middleware('permission:district-show', ['only' => ['show']]);
        //$this->middleware('permission:district-edit', ['only' => ['edit','update']]);
        //$this->middleware('permission:district-delete', ['only' => ['destroy']]);
    }

    public function index()
    {

        //$districts = District::with('state')->paginate(2);
        $subjects = Subject::withTrashed()->get();
        $yes_no = $this->master_details('yesno');
        return view('subject.index', compact('subjects', 'yes_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (count($request->all()) > 0) {
            $responses = $this->subjectsDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach ($responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                }
            }
            if ($responses == false) {

                if ($request->practical_type == 1) {
                    $practical_max_marks = $request->practical_max_marks;
                    $practical_min_marks = $request->practical_min_marks;
                } elseif ($request->practical_type == 0) {
                    $practical_max_marks = 0;
                    $practical_min_marks = 0;
                }

                if ($request->course == 12) {
                    $is_science_faculty = ($request->is_science_faculty == 1) ? $request->is_science_faculty : null;
                    $is_commerce_faculty = ($request->is_commerce_faculty == 1) ? $request->is_commerce_faculty : null;
                    $is_arts_faculty = ($request->is_arts_faculty == 1) ? $request->is_arts_faculty : null;
                    $is_allow_faculty = ($request->is_allow_faculty == 1) ? $request->is_allow_faculty : null;
                    $is_agricultre_faculty = ($request->is_agricultre_faculty == 1) ? $request->is_agricultre_faculty : null;
                } elseif ($request->course == 10) {
                    $is_science_faculty = Null;
                    $is_commerce_faculty = null;
                    $is_arts_faculty = null;
                    $is_allow_faculty = null;
                    $is_agricultre_faculty = null;
                }
                $subject_data = array(
                    'real_name' => $request->real_name,
                    'name' => $request->name,
                    'subject_type' => $request->subject_type,
                    'sessional_max_marks' => $request->sessional_max_marks,
                    'practical_type' => $request->practical_type,
                    'course' => $request->course,
                    'subject_code' => $request->subject_code,
                    'theory_max_marks' => $request->theory_max_marks,
                    'theory_min_marks' => $request->theory_min_marks,
                    'practical_max_marks' => $practical_max_marks,
                    'practical_min_marks' => $practical_min_marks,
                    'sessional_min_marks' => $request->sessional_min_marks,
                    'is_science_faculty' => $is_science_faculty,
                    'is_commerce_faculty' => $is_commerce_faculty,
                    'is_arts_faculty' => $is_arts_faculty,
                    'is_allow_faculty' => $is_allow_faculty,
                    'is_agricultre_faculty' => $is_agricultre_faculty
                );

                $Subject = Subject::create($subject_data);
                if ($Subject) {
                    return redirect()->route('subjects.index')->with('message', 'Subject successfully created');
                } else {
                    return redirect()->route('subjects.index')->with('error', 'Failed! District not created');
                }
            } else {
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = 'subjects';
        $subjecttype = array("A" => "A", "B" => "B");
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $yes_no = $this->master_details('yesno');
        return view('subject.create', compact('subjecttype', 'course', 'yes_no', 'model'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = 'subjects';
        $subjecttype = array("A" => "A", "B" => "B");
        $combo_name = 'course';
        $course = $this->master_details($combo_name);
        $yes_no = $this->master_details('yesno');
        $subject = Subject::findOrFail($id);
        return view('subject.edit', compact('subjecttype', 'course', 'yes_no', 'model', 'subject'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function subjectsactive($id, $active)
    {
        if ($active == 1) {
            $subjects_details = DB::table('subjects')->where('id', $id)->update(['deleted_at' => NULL]);
            return back()->with('message', 'Subjects UnDeleted Successfully ');
        } elseif ($active == 0)
            $subjects_details = DB::table('subjects')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return back()->with('message', 'Subjects Deleted Successfully ');
    }

    public function update(Request $request, $id)
    {
        if (count($request->all()) > 0) {
            $responses = $this->subjectsDetailsValidation($request);
            $responseFinal = null;
            if (@$responses) {
                foreach ($responses as $k => $response) {
                    if (!$response['isValid']) {
                        $isValid = false;
                    }
                    $responseFinal[$k]['isValid'] = $response['isValid'];
                    $responseFinal[$k]['customerrors'] = $response['errors'];
                    $responseFinal[$k]['validator'] = $response['validator'];

                }
            }
            if ($responses == false) {
                if ($request->practical_type == 1) {
                    $practical_max_marks = $request->practical_max_marks;
                    $practical_min_marks = $request->practical_min_marks;
                } elseif ($request->practical_type == 0) {
                    $practical_max_marks = '0';
                    $practical_min_marks = '0';
                }


                if ($request->course == 12) {
                    $is_science_faculty = ($request->is_science_faculty == 1) ? $request->is_science_faculty : null;
                    $is_commerce_faculty = ($request->is_commerce_faculty == 1) ? $request->is_commerce_faculty : null;
                    $is_arts_faculty = ($request->is_arts_faculty == 1) ? $request->is_arts_faculty : null;
                    $is_allow_faculty = ($request->is_allow_faculty == 1) ? $request->is_allow_faculty : null;
                    $is_agricultre_faculty = ($request->is_agricultre_faculty == 1) ? $request->is_agricultre_faculty : null;
                } elseif ($request->course == 10) {
                    $is_science_faculty = Null;
                    $is_commerce_faculty = null;
                    $is_arts_faculty = null;
                    $is_allow_faculty = null;
                    $is_agricultre_faculty = null;
                }
                $subject_data = array(
                    'real_name' => $request->real_name,
                    'name' => $request->name,
                    'subject_type' => $request->subject_type,
                    'sessional_max_marks' => $request->sessional_max_marks,
                    'practical_type' => $request->practical_type,
                    'course' => $request->course,
                    'subject_code' => $request->subject_code,
                    'theory_max_marks' => $request->theory_max_marks,
                    'theory_min_marks' => $request->theory_min_marks,
                    'practical_max_marks' => $practical_max_marks,
                    'practical_min_marks' => $practical_min_marks,
                    'sessional_min_marks' => $request->sessional_min_marks,
                    'is_science_faculty' => $is_science_faculty,
                    'is_commerce_faculty' => $is_commerce_faculty,
                    'is_arts_faculty' => $is_arts_faculty,
                    'is_allow_faculty' => $is_allow_faculty,
                    'is_agricultre_faculty' => $is_agricultre_faculty
                );
                $Subject = Subject::where('id', $id)->update($subject_data);
                if ($Subject) {
                    return redirect()->route('subjects.index')->with('message', 'Subject Update successfully created');
                } else {
                    return redirect()->route('subjects.index')->with('error', 'Failed! District not created');
                }
            } else {
                $customerrors = implode(",", @$responseFinal[$k]['customerrors']);
                return redirect()->back()->withErrors($responseFinal['validator'])->withInput($request->all());

            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $district = District::where('id', $id)->delete();
        return response()->json(['success' => 'Record successfully Deleted']);
    }
}


