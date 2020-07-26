<?php
 
namespace App\Http\Controllers;
 
use App\YahtzeePlayer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// use App\Events\SessionEvent;

class YahtzeePlayerController extends Controller {

    ##### Create new player
    public function store(Request $request) {

        $this->validate($request, [
            'playername' => 'required|string|max:40'
        ]);
        $data = $request->all();

        $player = YahtzeePlayer::create($data);

        // event(new SessionEvent($player));

        return response()->json($player);
    }

}
