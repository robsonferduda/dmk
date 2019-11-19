<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kodeine\Acl\Traits\HasPermission;

class Role extends Model
{
    use HasPermission;

    protected $fillable = ['name', 'slug', 'description'];

    protected $table = 'roles';

    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model', config('auth.model')))->withTimestamps();
    }

    public function permissao()
    {
        return $this->belongsToMany('App\Permissao','permission_role','role_id', 'permission_id');
    }

}
