<?php

// app/Models/Message.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['conversation_id','member_id','body','attachments'];
    protected $casts = ['attachments' => 'array'];

    public function conversation(): BelongsTo { return $this->belongsTo(Conversation::class); }
    public function member(): BelongsTo { return $this->belongsTo(Members::class, 'member_id'); }
}
