<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookInbox extends Model
{
    protected $table = 'webhook_inbox';

    protected $fillable = [
        'source','message_key','request_id','event','from','body',
        'status','attempts','payload_json','last_error',
    ];
}
