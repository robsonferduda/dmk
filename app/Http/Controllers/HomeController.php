<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    
    public function __construct()
    {
        
    }

    public function index()
    {
        if(!Auth::guest()){

            $role = (User::find(Auth::user()->id)->role()->first()) ? User::find(Auth::user()->id)->role()->first()->slug : null; 

            switch ($role) {
                case 'correspondente':
                    return redirect('correspondente/dashboard/'.Auth::user()->cd_entidade_ete);
                    break;
                
                default:
                    return view('home');
                    break;
            }
            
        }else{
            return view('conta/novo');
        }
            
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