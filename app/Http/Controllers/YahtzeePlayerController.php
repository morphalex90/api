<?php
 
namespace App\Http\Controllers;
 
use App\YahtzeePlayer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Events\PlayerJoinEvent;
use App\Events\PlayerLeaveEvent;

class YahtzeePlayerController extends Controller {

    ##### Create new player
    public function store(Request $request) {

        $this->validate($request, [
            'playername' => 'required|string|max:40'
        ]);
        $data = $request->all();
        $data['status'] = 'waiting';

        $player = YahtzeePlayer::create($data);

        event(new PlayerJoinEvent($player));

        return response()->json($player);
    }

     ##### Delete player from channel
     public function player_leave(Request $request, $session_id, $player_id) {

        $player = YahtzeePlayer::find($player_id);
        $player->delete();

        event(new PlayerLeaveEvent($session_id, $player_id));

        return response()->json($player_id);
    }

}
