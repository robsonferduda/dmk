<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProcessoRequest extends FormRequest
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
            'cd_cliente_cli'       => 'required',
            'nu_processo_pro'      => 'required',
            'cd_tipo_processo_tpo' => 'required',
            'cd_cidade_cde'        => 'required'
        ];

        return $data;
    }

    public function messages()
    {
        return [
            'cd_cliente_cli.required'       => 'O campo Cliente é obrigatório. Selecione um cliente existente.',
            'nu_processo_pro.required'      => 'O campo Nº processo é obrigatório',
            'cd_tipo_processo_tpo.required' => 'O campo Tipo de Processo é obrigatório',
            'cd_cidade_cde.required'        => 'O campo Cidade é obrigatório'
        ];
    }
}
