<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerLeaveEvent implements ShouldBroadcast {
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message) {
        $this->message = $message;
    }

    public function broadcastOn() {
        return ['players'];
    }

    public function broadcastAs() {
        return 'player-leave-session';
    }
}
