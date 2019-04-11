<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
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
            'nm_fantasia_cli' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'nm_fantasia_cli.required' => 'Campo <strong>Nome/Nome Fantasia</strong> é obrigatório'
        ];
    }
}
