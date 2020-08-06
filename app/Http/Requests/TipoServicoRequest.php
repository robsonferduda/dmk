<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class TipoServicoRequest extends FormRequest
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
            'nm_tipo_servico_tse' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'nm_tipo_servico_tse.required' => 'Campo Nome do Serviço é Obrigatório'
        ];
    }
}
