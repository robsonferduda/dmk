<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PasswordResets extends Model
{

    protected $table = 'password_resets';
    protected $primaryKey = "email";
   
    public $timestamps = true;
}
