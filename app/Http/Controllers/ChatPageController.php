<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Members;
use Illuminate\Http\Request;

class ChatPageController extends Controller
{
    // รายการห้องที่แอดมินอยู่ (ล่าสุด)
    public function index(Request $req) {
        $me = $req->user();
        $convs = Conversation::whereHas('members', fn($q)=>$q->whereKey(1))
                 ->with(['members:id,username,nickname,fullname'])
                 ->latest('updated_at')
                 ->get();
                //  return $convs;
        return view('chat.index', compact('convs'));
    }

    // เปิดห้อง
    public function show(Request $req, Conversation $conversation) {
        $me = $req->user();
        // แนบตัวเองเข้าห้องถ้ายังไม่ได้แนบ (กัน 403)
        $conversation->members()->syncWithoutDetaching([$me->id]);

        $messages = $conversation->messages()
            ->with(['member:id,username,nickname,fullname'])
            ->orderBy('id','asc')->take(200)->get();
        // return [
        //     'thread' => $conversation,
        //     'messages' => $messages
        // ];
        return view('chat.show', [
            'thread'=>$conversation,
            'messages'=>$messages
        ]);
    }

    // (ออปชัน) สร้างห้องแชตตรงกับสมาชิก
    public function directWithMember(Request $req, Members $member) {
        $thread = Conversation::firstOrCreateDirectBetween($req->user()->id, $member->id);
        return redirect()->route('chat.show', $thread);
    }
}
