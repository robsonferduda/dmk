<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    
    protected $fillable = ['name', 'slug', 'description', 'inherit_id'];

    protected $table = 'permissions';

    public function roles()
    {
        $model = config('acl.role', 'Kodeine\Acl\Models\Eloquent\Role');

        return $this->belongsToMany($model)->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model', config('auth.model')))->withTimestamps();
    }

    public static function loadPermissoes()
    {
        $permissoes = array();

        if (empty(\Cache::tags('dmk_permissoes','listaPermissoes')->get('permissoes')))
        {
            $permissoes = Permissao::select('id')->get()->toArray();

            $expiresAt = \Carbon\Carbon::now()->addMinutes(1440);
            \Cache::tags('dmk_permissoes','listaPermissoes')->put('permissoes', $permissoes, $expiresAt);

        }

        return $permissoes = \Cache::tags('dmk_permissoes','listaPermissoes')->get('permissoes');
    }

}