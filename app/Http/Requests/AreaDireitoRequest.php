<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class AreaDireitoRequest extends FormRequest
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
            'dc_area_direito_ado' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'dc_area_direito_ado.required' => 'Campo descrição obrigatório'
        ];
    }
}
