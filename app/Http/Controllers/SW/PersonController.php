<?php

namespace App\Http\Controllers\SW;

use App\Http\Controllers\Controller;
use App\Models\SW\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $people = Person::with('planet')->orderBy('name')->get();

        if ($people) {
            return response()->json(['people' => $people], 200);
        }

        return response()->json(['message' => 'No people available'], 404);
    }
}
