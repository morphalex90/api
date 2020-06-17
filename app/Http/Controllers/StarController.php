<?php
 
namespace App\Http\Controllers;
 
use App\Star;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
class StarController extends Controller {

    ##### Create new star
    public function createStar(Request $request) {
        $star = Star::create($request->all());
        return response()->json($star);
    }

    ##### Get the average value
    public function averageStar() {
        $stars  = Star::all();
        $countStars = number_format($stars->avg('vote'), 2);
        $averageStars = $stars->count();
        return response()->json(['count' => $averageStars, 'average' => $countStars]);
    }
}
