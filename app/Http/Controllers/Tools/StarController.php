<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Tools\Star;
use Illuminate\Http\Request;

class StarController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'vote' => 'required|numeric|min:1|max:5',
        ]);

        Star::create([
            'vote' => $request->get('vote'),
            'ip_address' => $request->ip(),
        ]);

        //# Mail to myself
        // $headers = "MIME-Version: 1.0" . "\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // $headers .= 'From: Info <info@morpheus90.com>' . "\r\n";
        // $content = 'Stars: ' . $star->vote . '<br>';
        // mail('piero.nanni@gmail.com', 'Tools By Piero Nanni - New Vote', $content, $headers);

        return response()->json(['message' => 'success']);
    }

    //#### Get the average value
    public function averageStar()
    {
        $stars = Star::all();
        $countStars = number_format($stars->avg('vote'), 2);
        $averageStars = $stars->count();

        return response()->json(['count' => $averageStars, 'average' => $countStars]);
    }
}
