<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function imageCrop()
    {
        return view('imageCrop');
    }

    public function imageCropPost(Request $request)
    {
        $data = $request->image;

        $id = 'ent'.Auth::user()->cd_entidade_ete;

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);

        $data = base64_decode($data);
        $image_name = $id.'.png';
        $path = public_path() . "/img/users/" . $image_name;

        file_put_contents($path, $data);

        return response()->json(['success'=>'done']);
    }
}