<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class CategoriaCorrespondenteRequest extends FormRequest
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
            'dc_categoria_correspondente_cac' => 'required',
            'color_cac' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'dc_categoria_correspondente_cac.required' => 'Campo Nome da Categoria é Obrigatório',
            'color_cac.required' => 'Campo Cor é Obrigatório'
        ];
    }
}
