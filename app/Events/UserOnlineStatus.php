<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnlineStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $isOnline;
    public $lastSeen;

    public function __construct($userId, $isOnline = true)
    {
        $this->userId = $userId;
        $this->isOnline = $isOnline;
        $this->lastSeen = now()->toISOString();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('online-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.status';
    }
}
