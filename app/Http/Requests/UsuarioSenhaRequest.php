<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UsuarioSenhaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!Auth::guest())
            return true;
        else
            return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $data = [
            'password'     => 'required|confirmed',
        ];

        return $data;
    }

    public function messages()
    {
        return [
            'password.required' => 'O campo Senha é obrigatório',
            'password.confirmed' => 'As senhas não conferem!'
        ];
    }
}
