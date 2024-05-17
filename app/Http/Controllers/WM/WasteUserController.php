<?php

namespace App\Http\Controllers\WM;

use App\Http\Controllers\Controller;
use App\Models\WM\WasteUser;
use Illuminate\Http\Request;

class WasteUserController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'profile_id' => 'required|string',
        ]);

        WasteUser::firstOrCreate(
            [
                'provider' => $request->get('provider'),
                'profile_id' => $request->get('profile_id'),
                // 'score' => $request->get('provider'),
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(WasteUser $wasteUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WasteUser $wasteUser)
    {
        //
    }
}
