<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $userName;
    public $chatRoomId;
    public $isTyping;

    public function __construct($userId, $userName, $chatRoomId, $isTyping = true)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->chatRoomId = $chatRoomId;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chatRoomId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}

