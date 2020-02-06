<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use Hazzard\Filepicker\Handler;
use Hazzard\Filepicker\Uploader;
use Intervention\Image\ImageManager;
use Hazzard\Config\Repository as Config;

class FilepickerController extends BaseController
{
    /**
     * @var \Hazzard\Filepicker\Handler
     */
    protected $handler;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->handler = new Handler(
            new Uploader($config = new Config, new ImageManager)
        );

        $config['upload_dir'] =  public_path('files');
        $config['upload_url'] = '/files';
        $config['debug'] = config('app.debug');
    }

    public function index()
    {
        return view('upload/index');
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request)
    {
        return $this->handler->handle($request);
    }
}