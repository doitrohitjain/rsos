<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;


class RegisterController extends Controller
{

    public function register(Request $request)
    {
        $validated = $request->validate([
            'captcha' => ['required', 'captcha']

        ]);
    }


    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    public function kal()
    {
        return view('rahul');
    }

}
	
