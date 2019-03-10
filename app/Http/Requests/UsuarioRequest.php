<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
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
        return [
            'name'     => 'required',
            'email'    => 'required',
            'password' => 'required',
            'data_nascimento' => 'date_format:d/m/Y'
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'O campo Nome é obrigatório',
            'email.required'    => 'O campo E-mail é obrigatório',
            'password.required' => 'O campo Senha é obrigatório',
            'data_nascimento'   => 'Campo Data de Nascimento com formato inválido'

        ];
    }
}
