<?php

// app/Events/ChatMessageSent.php
namespace App\Events;
use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatMessageSent implements ShouldBroadcast
{
    public function __construct(public Message $message) {}

    public function broadcastOn() { return new PrivateChannel('conversations.'.$this->message->conversation_id); }
    public function broadcastAs() { return 'chat.message.sent'; }
    public function broadcastWith(): array {
        return [
            'id'=>$this->message->id,
            'conversation_id'=>$this->message->conversation_id,
            'member_id'=>$this->message->member_id,
            'body'=>$this->message->body,
            'attachments'=>$this->message->attachments,
            'created_at'=>$this->message->created_at?->toISOString(),
            'member'=>[
                'id'=>$this->message->member->id,
                'name'=>$this->message->member->nickname ?: ($this->message->member->fullname ?: $this->message->member->username),
            ],
        ];
    }
}
