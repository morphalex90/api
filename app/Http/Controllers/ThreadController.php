<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $threads = Thread::with('lastMessage', 'participants')->get();
        return response()->json(['threads' => $threads], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
