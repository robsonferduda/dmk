<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable,SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'cd_conta_con',
        'cd_nivel_niv',
        'cd_entidade_ete',
        'data_nascimento',
        'data_admissao',
        'cd_estado_civil_esc',
        'cd_departamento_dep',
        'cd_cargo_car'
    ];

    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'cd_entidade_ete');
    }

    public function tipoPerfil()
    {
        return $this->hasOne('App\Nivel','cd_nivel_niv', 'cd_nivel_niv');
    }

    public function estadoCivil()
    {
        return $this->hasOne('App\EstadoCivil','cd_estado_civil_esc', 'cd_estado_civil_esc');
    }
     
}