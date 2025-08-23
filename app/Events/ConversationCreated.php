<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Conversation;

class ConversationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Conversation $conversation, public array $creator)
    {
        // $creator = ['id'=>..., 'name'=>...]
    }
    public function broadcastOn()
    {
        return new PrivateChannel('admins');
    }
    public function broadcastAs()
    {
        return 'chat.conversation.created';
    }
    public function broadcastWith(): array
    {
        return [
            'conversation' => [
                'id'    => $this->conversation->id,
                'title' => $this->conversation->title,
                'type'  => $this->conversation->type,
            ],
            'creator' => $this->creator,
            'created_at' => $this->conversation->created_at?->toISOString(),
        ];
    }
}
