<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerJoinEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $player;

    public function __construct($player)
    {
        $this->player = $player;
    }

    public function broadcastOn()
    {
        return ['session-' . $this->player->session_id];
    }

    public function broadcastAs()
    {
        return 'player-join-session';
    }
}
