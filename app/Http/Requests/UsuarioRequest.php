<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
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
            'name'     => 'required',
            'email'    => 'required'
        ];

        if($this->request->get('_method') != 'PUT'){
            $data['password'] = 'required'; 
        }

        if(!empty($this->request->get('nu_fone_fon'))){
            $data['cd_tipo_fone_tfo'] = 'required'; 
        }

        if(!empty($this->request->get('data_nascimento'))){
            $data['data_nascimento'] = 'date_format:d/m/Y'; 
        }

        if(!empty($this->request->get('data_admissao'))){
            $data['data_admissao'] = 'date_format:d/m/Y'; 
        }

        return $data;
    }

    public function messages()
    {
        return [
            'name.required'     => 'O campo Nome é obrigatório',
            'email.required'    => 'O campo E-mail é obrigatório',
            'password.required' => 'O campo Senha é obrigatório',
            'data_nascimento.date_format' => 'Campo Data de Nascimento com formato inválido',
            'data_admissao.date_format' => 'Campo Data de Admissão com formato inválido',
            'cd_tipo_fone_tfo.required' => 'Campo Tipo do Telefone é obrigatório quanto o telefone está preenchido'

        ];
    }
}
