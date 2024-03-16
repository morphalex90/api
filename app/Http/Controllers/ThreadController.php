<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $threads = Thread::with('lastMessage', 'participants')->get();

        return response()->json(['threads' => $threads], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $thread = Thread::with('messages', 'participants')->where('id', $request->route('thread_id'))->first();

        if ($thread != null) {
            return response()->json(['thread' => $thread], 200);
        } else {
            return response()->json(['message' => 'Thread not found'], 404);
        }
    }

    /**
     * Show messages.
     *
     * @return \Illuminate\Http\Response
     */
    public function messages(Request $request)
    {
        $messages = Message::with('user')->where('thread_id', $request->route('thread_id'))->get();

        if ($messages != null) {
            return response()->json(['messages' => $messages], 200);
        } else {
            return response()->json(['message' => 'Thread not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thread $thread)
    {
        //
    }
}
