<?php

namespace App\Http\Controllers\SW;

use App\Http\Controllers\Controller;
use App\Models\SW\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $people = Person::with('planet')->orderBy('name')->get();

        if ($people) {
            return response()->json(['people' => $people], 200);
        }
        return response()->json(['message' => 'No people available'], 404);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
