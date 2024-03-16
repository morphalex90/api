<?php

namespace App\Http\Controllers\SW;

use App\Http\Controllers\Controller;
use App\Models\SW\Planet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $planets = Planet::orderBy('name')->get();

        if ($planets) {
            return response()->json(['planets' => $planets], 200);
        }

        return response()->json(['message' => 'No planets available'], 404);
    }
}
