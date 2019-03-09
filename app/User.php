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
        'cd_estado_civil_esc'
    ];

    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function entidade()
    {
        return $this->hasOne('App\Entidade','cd_entidade_ete', 'cd_entidade_ete');
    }
     
}