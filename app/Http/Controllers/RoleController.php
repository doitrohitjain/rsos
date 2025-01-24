<?php

namespace App\Http\Controllers;

use App\Component\CustomComponent;
use auth;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //  $this->middleware('permission:role-list', ['only' => ['index','store']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-show', ['only' => ['show']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::all();
        return view('role.index', compact('roles'));
    }

    public function googleindex(Request $request)
    {
        $roles = Role::all();
        return view('role.googlespechindex', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'name' => 'required|unique:roles,name',
        //     'permission' => 'required',
        // 	'guard_name' => 'required',

        // ]);

        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
            'guard_name' => 'required',
        ], [
            'name.required' => 'Name is Required.',
            'name.unique' => 'Name is Already Taken.'
        ]);

        if ($validate->fails()) {
            return back()->withErrors($validate->errors())->withInput();
        }
        $role = Role::create(['guard_name' => 'web', 'name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        if ($role) {
            return redirect()->route('roles.index')->with('message', 'Role created successfully');
        } else {
            return redirect()->route('roles.index')->with('error', 'Failed! Role not created');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        return view('role.create', compact('permission'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return view('role.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = $result = null;
        $permissions = Permission::orderby("permissions.name", "asc")->get();

        foreach ($permissions as $kid => $permission) {
            $name = $permission->name;
            $itemId = $permission->id;
            $current_letter = ucfirst(substr($name, 0, 1));
            $result[$current_letter][$itemId] = $name;
        }
        $permission = $result;
        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('role.edit', compact('role', 'permission', 'rolePermissions'));
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
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        if ($role) {
            return redirect()->route('roles.index')->with('message', 'Role updated successfully');
        } else {
            return redirect()->route('roles.index')->with('error', 'Failed! Role not updated');
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
        $roles = DB::table("roles")->where('id', $id)->delete();
        return response()->json(['success' => 'Record successfully Deleted']);
    }

    public function query()
    {
        $custom_component_obj = new CustomComponent;
        $master = $custom_component_obj->getWithPaginationAiCentersdata();

        return view('role.query', compact('master'));

    }
}