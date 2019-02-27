<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'cd_conta_con',
        'cd_nivel_niv',
        'cd_entidade_ete',
        'data_nascimento',
        'data_admissao'
    ];

    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
}
