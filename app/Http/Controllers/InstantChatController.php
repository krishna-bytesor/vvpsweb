<?php

namespace App\Http\Controllers;

use App\Models\InstantChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstantChatController extends Controller
{
    public function getChatRecords()
    {
        $chatRecords = InstantChat::with(['swami', 'user'])
            ->where('user_id', Auth::id())
            ->orWhere('swami_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->paginate();
        return response()->json(['data'=>$chatRecords, 'is_swami'=> (Auth::user()->email == config('app.swami_email'))]);
    }

    public function initiateChatRecord(Request $request)
    {
        $swami_id = User::where('email', config('app.swami_email'))->first()->id;

        $existingChatRecord = InstantChat::where('user_id', Auth::id())
            ->where('swami_id', $swami_id)
            ->first();

        if ($existingChatRecord) {
            return response()->json(['data' => $existingChatRecord, 'message' => 'Chat record already exists']);
        }

        $chatRecord = new InstantChat();
        $chatRecord->user_id = Auth::id();
        $chatRecord->swami_id = $swami_id;
        $chatRecord->save();

        return response()->json(['data' => $chatRecord, 'message' => 'Chat record created']);
    }

    public function addLatestMessage(Request $request)
    {
        $chatRecord = InstantChat::findOrFail($request->chat_id);
        $chatRecord->update(['latest_message' => $request->latest_message]);
        return response()->json(['data' => $chatRecord, 'message' => 'Latest message added']);
    }

}
