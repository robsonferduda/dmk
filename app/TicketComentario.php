<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketComentario extends Model
{
    protected $table = 'ticket_comentario_tco';
    protected $primaryKey = 'cd_ticket_comentario_tco';
    protected $fillable = [
                            'cd_conta_con',
                            'cd_issue_note_tco',
                            'user_id',
                            'cd_ticket_tic'
                          ];
}
