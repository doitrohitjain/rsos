<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function employee_register()
    {
        return view('home.employee_register');
    }

    public function employee_listing()
    {
        $users = User::latest()->paginate(10);
        return view('home.employee_listing', compact('users'));
    }

    public function employee_store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data = ($request->all());
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'employee', // Assign 'user' role by default
        ]);
        return redirect('home/employee_listing')->with('success', 'Registration has been successfully.');
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $user = User::findOrFail($id);
        $user->delete(); // Soft Delete worksheet
        return redirect('home/employee_listing')->with('success', 'Staff profile has been deleted successfully!');
    }


}
