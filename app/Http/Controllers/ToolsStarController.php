<?php

namespace App\Http\Controllers;

use App\Models\ToolsStar;
use Illuminate\Http\Request;

class ToolsStarController extends Controller
{
    public function store(Request $request) ##### Create new star
    {

        $this->validate($request, [
            'vote' => 'required|numeric|min:1|max:5'
        ]);

        ToolsStar::create([
            'vote' => $request->get('vote'),
            'ip_address' => $request->ip(),
        ]);

        ## Mail to myself
        // $headers = "MIME-Version: 1.0" . "\r\n";
        // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // $headers .= 'From: Info <info@morpheus90.com>' . "\r\n";
        // $content = 'Stars: ' . $star->vote . '<br>';
        // mail('piero.nanni@gmail.com', 'Tools By Piero Nanni - New Vote', $content, $headers);

        return response()->json(['message' => 'success']);
    }

    ##### Get the average value
    public function averageStar()
    {
        $stars  = ToolsStar::all();
        $countStars = number_format($stars->avg('vote'), 2);
        $averageStars = $stars->count();
        return response()->json(['count' => $averageStars, 'average' => $countStars]);
    }
}
