<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'employee')->get();
        return view('users.index', compact('users'));
    }

}
