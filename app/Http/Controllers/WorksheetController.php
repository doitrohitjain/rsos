<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WorksheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd(auth()->user()->role);
        $userNameList = $userEmailList = [];
        if (auth()->user()->role == 'admin') {
            $userNameList = User::pluck('name', 'id');
            $userEmailList = User::pluck('email', 'id');
            $worksheets = Worksheet::latest()->paginate(10);
        } else {
            $worksheets = auth()->user()->worksheets()->latest()->paginate(10);
        }

        return view('worksheets.index', compact('worksheets', 'userNameList', 'userEmailList'));
    }

    public function store(Request $request)
    {


        $request->validate([
            'date' => 'required|date',
            'task' => 'required|string',
            'status' => 'required|string',
            'description' => 'required|string',
        ]);
        $inputs = $request->all();
        auth()->user()->worksheets()->create($inputs);
        return redirect()->back()->with('success', 'Worksheet added successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $worksheet = Worksheet::findOrFail($id); // Fetch worksheet by ID or throw 404

        return view('worksheets.show', compact('worksheet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $worksheet = Worksheet::findOrFail($id); // Fetch worksheet by ID or throw 404
        return view('worksheets.edit', compact('worksheet'));
    }


    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'task' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:pending,completed',
        ]);

        $worksheet = Worksheet::findOrFail($id);
        $worksheet->update($request->all()); // Update worksheet with validated data

        return redirect()->route('worksheets.index')->with('success', 'Worksheet updated successfully!');
    }


    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $worksheet = Worksheet::findOrFail($id);
        $worksheet->delete(); // Soft Delete worksheet

        return redirect()->route('worksheets.index')->with('success', 'Worksheet deleted successfully!');
    }

}
