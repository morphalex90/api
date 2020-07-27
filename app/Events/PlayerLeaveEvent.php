<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerLeaveEvent implements ShouldBroadcast {
    use InteractsWithSockets, SerializesModels;

    public $session_id;
    public $player_id;

    public function __construct($session_id, $player_id) {
        $this->session_id = $session_id;
        $this->player_id = $player_id;
    }

    public function broadcastOn() {
        return ['session-'.$this->session_id];
    }

    public function broadcastAs() {
        return 'player-leave-session';
    }
}
