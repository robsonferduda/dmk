<?php

namespace App\Http\Controllers;

use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClienteController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function novo()
    {
        return view('cliente/novo');
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function listar()
    {
        dd(Cliente::all());
    }

}