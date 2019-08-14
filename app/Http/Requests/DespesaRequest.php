<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class DespesaRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        return [
            'cd_tipo_despesa_tds' => 'not_in:0',
            'dc_descricao_des' => 'required',
            'dt_vencimento_des' => 'required',
            'vl_valor_des' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'cd_tipo_despesa_tds.not_in' => 'Campo tipo de despesa obrigatório',
            'dc_descricao_des.required' => 'Campo descrição obrigatório',
            'dt_vencimento_des.required' => 'Campo data de vencimento obrigatório',
            'vl_valor_des.required' => 'Campo valor obrigatório'
        ];
    }
}
