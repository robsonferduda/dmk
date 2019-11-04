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


}