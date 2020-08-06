<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class CategoriaDespesaRequest extends FormRequest
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
            'nm_categoria_despesa_cad' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'nm_categoria_despesa_cad.required' => 'Campo Nome da Categoria é Obrigatório'
        ];
    }
}
