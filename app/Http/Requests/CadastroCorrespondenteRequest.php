<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class CadastroCorrespondenteRequest extends FormRequest
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
            'nm_razao_social_con' => 'required',
            'email' => 'required'   
        ];
    }

    public function messages()
    {
        return [
            'nm_razao_social_con.required' => 'Campo <strong>Nome</strong> é obrigatório',
            'email.required' => 'Campo <strong>Email</strong> é obrigatório'
        ];
    }
}
