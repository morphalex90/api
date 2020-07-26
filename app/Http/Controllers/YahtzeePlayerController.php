<?php
 
namespace App\Http\Controllers;
 
use App\YahtzeePlayer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Events\PlayerJoinEvent;

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

}
