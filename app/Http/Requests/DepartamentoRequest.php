<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class DepartamentoRequest extends FormRequest
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
            'nm_departamento_dep' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'nm_departamento_dep.required' => 'Campo Nome do Departamento é Obrigatório'
        ];
    }
}
