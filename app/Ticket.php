<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    protected $table = 'ticket_tic';
    protected $primaryKey = 'cd_ticket_tic';
    protected $fillable = [
                            'cd_conta_con',
                            'cd_redmine_task_tic',
                            'user_id'
                          ];
}
