<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ContatoRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nm_contato_cot' => 'required',
            'cd_tipo_contato_tct' => 'not_in:0'
        ];
    }

    public function messages()
    {
        return [
            'nm_contato_cot.required' => 'Campo nome obrigatório',
            'cd_tipo_contato_tct.not_in' => 'Campo tipo do contato obrigatório'
        ];
    }
}
