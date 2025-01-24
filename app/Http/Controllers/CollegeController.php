<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\models\Student;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class CollegeController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:college_dashboard', ['only' => ['dashboard']]);
        $this->middleware('permission:college-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:college-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:college-show', ['only' => ['show']]);
        $this->middleware('permission:college-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:college-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = College::all();
        return view('college.index', compact('data'));
    }

    public function dashboard()
    {
        $records['total_students'] = Student::where('id', '!=', '')->count();
        return view('college.dashboard', ['records' => $records]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'ssoid' => 'required',
            'email' => 'required',
            'password' => 'required',
            'roles' => 'required',
            'name' => 'required',

        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $college = College::create($input);
        $college->assignRole($request->input('roles'));
        if ($college) {
            return redirect()->route('colleges.index')->with('message', 'college successfully created');
        } else {
            return redirect()->route('colleges.index')->with('error', 'college! User not created');
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('college.create', compact('roles'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $college = College::find($id);
        return view('college.show', compact('college'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $college = College::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $college->roles->pluck('name', 'name')->all();

        return view('college.edit', compact('college', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'ssoid' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'roles' => 'required'
        ]);

        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $college = College::find($id);
        $college->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $college->assignRole($request->input('roles'));

        if ($college) {
            return redirect()->route('colleges.index')->with('message', 'college successfully updated');
        } else {
            return redirect()->route('colleges.index')->with('error', 'Failed! college not updated');
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
        $college = College::where('id', $id)->delete();
        return response()->json(['success' => 'Record successfully Deleted']);
    }
}
