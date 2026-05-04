<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_messages';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'session_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

