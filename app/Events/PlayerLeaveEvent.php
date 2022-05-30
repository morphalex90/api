<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerLeaveEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session_id;
    public $player_id;

    public function __construct($session_id, $player_id)
    {
        $this->session_id = $session_id;
        $this->player_id = $player_id;
    }

    public function broadcastOn()
    {
        return ['session-' . $this->session_id];
    }

    public function broadcastAs()
    {
        return 'player-leave-session';
    }
}
