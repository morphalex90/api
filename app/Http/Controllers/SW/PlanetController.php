<?php

namespace App\Http\Controllers\SW;

use App\Http\Controllers\Controller;
use App\Models\SW\Planet;

class PlanetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $planets = Planet::orderBy('name')->get();

        if ($planets) {
            return response()->json(['planets' => $planets], 200);
        }

        return response()->json(['message' => 'No planets available'], 404);
    }
}
