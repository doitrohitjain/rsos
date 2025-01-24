<?php

namespace App\Http\Controllers;

use App\models\District;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Redirect;

class DistrictController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:district-list', ['only' => ['index', 'store']]);
        $this->middleware('permission:district-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:district-show', ['only' => ['show']]);
        $this->middleware('permission:district-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:district-delete', ['only' => ['destroy']]);
    }

    public function index()
    {


        //$districts = District::with('state')->paginate(2);
        $districts = District::with('state')->get();
        //dd($districts);
        return view('district.index', compact('districts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Form validation
        $this->validate($request, [
            'state_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'code' => 'required',
            'name' => 'required',
            'name_mangal' => 'required',
        ]);

        //  Store data in database
        $District = District::create($request->all());
        if ($District) {
            return redirect()->route('districts.index')->with('message', 'District successfully created');
        } else {
            return redirect()->route('districts.index')->with('error', 'Failed! District not created');
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $state = DB::table('states')->pluck('name', 'id');
        return view('district.create', compact('state'));
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
        $districts = District::findOrFail(Crypt::decrypt($id));
        $state = DB::table('states')->pluck('name', 'id');
        //dd($state);

        return view('district.edit', compact('districts', 'state'));
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
            'state_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'code' => 'required',
            'name' => 'required',
            'name_mangal' => 'required',
        ]);
        $district = District::findOrFail($id);
        $input = $request->all();
        $district->fill($input)->save();
        if ($district) {
            return redirect()->route('districts.index')->with('message', 'District successfully updated');
        } else {
            return redirect()->route('districts.index')->with('error', 'Failed! District not updated');
        }
        //
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
