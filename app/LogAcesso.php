<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogAcesso extends Model
{
    public $timestamps = false;

    protected $table = 'log_acesso';

    protected $fillable = ['user_id', 'ip_address', 'user_agent'];

    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
