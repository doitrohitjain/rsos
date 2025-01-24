<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Hash;
use Session;

class GraphicalController extends Controller
{
    function __construct()
    {
        // $this->middleware('permission:logslisting', ['only' => ['logslisting']]);
        //$this->middleware('permission:logdebug', ['only' => ['logDebug']]);
    }

    public function index()
    {
        $data = array();
        $title = "Application Dashboard Count";
        $breadcrumbs = array(
            array(
                "label" => "Dashboard",
                'url' => ""
            ),
            array(
                "label" => $title,
                'url' => ''
            )
        );
        return view('graphical.index', compact('title', 'breadcrumbs', 'data'));
    }
}
