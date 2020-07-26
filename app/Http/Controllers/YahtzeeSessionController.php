<?php
 
namespace App\Http\Controllers;
 
use App\YahtzeeSession;
use App\YahtzeePlayer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Events\SessionEvent;

class YahtzeeSessionController extends Controller {

    ##### List all sessions
    public function index(Request $request) {

        $sessions  = YahtzeeSession::all();
        return response()->json(['count' => count($sessions), 'sessions' => $sessions]);
    }

    ##### Create new session
    public function store(Request $request) {

        $this->validate($request, [
            'partecipants_max_number' => 'numeric|min:0|max:50',
            'name' => 'string|max:40'
        ]);
        $data = $request->all();
        $data['status'] = 'open';

        $session = YahtzeeSession::create($data);

        event(new SessionEvent($session));

        return response()->json($session);
    }

    ##### List players of a session
    public function players(Request $request, $id) {

        $players = YahtzeePlayer::where('session_id', $id)->get();

        return response()->json(['count' => count($players), 'players' => $players]);
    }
}
