<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kodeine\Acl\Traits\HasRole;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use App\Notifications\MailResetPasswordNotification as MailResetPasswordNotification;

class User extends Authenticatable implements AuditableContract
{
    use Notifiable,SoftDeletes,HasRole, Auditable;

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
        'cd_cargo_car',
        'observacao'
    ];

    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }

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

    public function departamento()
    {
        return $this->hasOne('App\Departamento','cd_departamento_dep', 'cd_departamento_dep');
    }

    public function cargo()
    {
        return $this->hasOne('App\Cargo','cd_cargo_car', 'cd_cargo_car');
    }

    public function role()
    {
        return $this->belongsToMany('Kodeine\Acl\Models\Eloquent\Role','role_user','user_id', 'role_id');
    }

    public static function boot(){

        parent::boot();

        
        static::deleting(function($usuario)
        {
            foreach($usuario->entidade()->first()->identificacao()->get() as $identificacao){
                $identificacao->delete();
            }            

            $usuario->entidade()->first()->banco()->delete();
            $usuario->entidade()->first()->endereco()->delete();
            $usuario->entidade()->first()->fone()->delete();
            $usuario->entidade()->delete();
        });

    }
     
}