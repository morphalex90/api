<?php
 
namespace App\Http\Controllers;
 
use App\YahtzeeSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DOMDocument;
use SimpleXMLElement;
 
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

        $star = YahtzeeSession::create($data);

        return response()->json($star);
    }

}
