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
        $average = $stars->avg('vote');
        return response()->json($average);
    }

    ##### TEST - Create dummy data
    // public function createStarr(Request $request) {
        
    //     $star = new Star();
    //     $star->vote = 5;
    //     // $star->added_on = date('Y-m-d H:i:s');

    //     $star->save();

    //     return response()->json($star);
    // }
}
