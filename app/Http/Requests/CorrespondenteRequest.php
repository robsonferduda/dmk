<?php

namespace App\Http\Requests;

use Auth;
use App\Enums\Nivel;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CorrespondenteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'g-recaptcha-response' => 'required|captcha',
            'nm_razao_social_con' => 'required',
            'email' => Rule::unique('users')->where(function ($query) {
                return $query->where('cd_nivel_niv', Nivel::CORRESPONDENTE)->where('email', $this->email);
            })
        ];
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => 'É obrigatório marcar "Não sou um robô"',
            'nm_razao_social_con.required' => 'Campo nome obrigatório',
            'email.unique'                 => 'Esse email já foi cadastrado por um correspondente em nosso sistema. <strong><a style="color: #b94a48;" href="autenticacao">Clique aqui</a></strong> para acessar sua conta ou recuperar sua senha'
        ];
    }
}
