<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class TipoProcessoRequest extends FormRequest
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
            'nm_tipo_processo_tpo' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'nm_tipo_processo_tpo.required' => 'Campo Nome do Tipo de Processo é Obrigatório'
        ];
    }
}
