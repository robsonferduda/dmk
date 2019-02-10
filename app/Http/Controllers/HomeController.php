<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function menu(Request $request, $id)
    {
        if(session('menu_pai') == $id)
            Session::put('menu_pai', "");
        else
            Session::put('menu_pai', $id);

        return $request->url();
    }

    public function minify()
    {
        if(session('menu_minify') == 'on')
            Session::put('menu_minify', 'off');
        else
            Session::put('menu_minify', 'on');

        return back();

    }
}