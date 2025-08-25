<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Members;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatPageController extends Controller
{
    // รายการห้องที่แอดมินอยู่ (ล่าสุด)
    public function index(Request $req)
    {
        $me = $req->user();
        $convs = Conversation::whereHas('members', fn($q) => $q->whereKey(1))
            ->with(['members:id,username,nickname,fullname'])
            ->latest('updated_at')
            ->get();
        // $convs = Conversation::latest('updated_at')
        //     ->get();
        //  return $convs;
        return view('chat.index', compact('convs'));
    }

    // เปิดห้อง
    public function show(Request $req, Conversation $conversation)
    {
        $me = $req->user();
        // แนบตัวเองเข้าห้องถ้ายังไม่ได้แนบ (กัน 403)
        $conversation->members()->syncWithoutDetaching([$me->id]);

        $messages = $conversation->messages()
            ->with(['member:id,username,nickname,fullname'])
            ->orderBy('id', 'asc')->take(200)->get();
        $m =Message::where('conversation_id', $conversation->id)->where('member_id','<>', $me->id)->first();
        if (!$m) {
            return redirect()->back()->with('error', 'ไม่มีข้อความในห้องนี้');
        }
        $member = $conversation->members()->whereKey($m->member_id)->first();
        // return ($member);
        return view('chat.show', [
            'thread' => $conversation,
            'messages' => $messages,
            'member' => $member,
        ]);
    }
    // ปิดห้อง (แอดมิน)
    public function closeConversation(Request $req, Conversation $conversation)
    {
        $me = $req->user();


        $conversationId = $conversation->id;

        // เก็บ path ของไฟล์แนบไว้ลบหลังลบแถว DB (ป้องกัน orphan files)
        $attachments = $conversation->messages()
            ->pluck('attachments')     // อาจเป็น array/json/NULL
            ->filter()
            ->flatMap(function ($val) {
                if (is_array($val)) return $val;
                $decoded = json_decode($val, true);
                return is_array($decoded) ? $decoded : [];
            })->values()->all();

        DB::transaction(function () use ($conversation) {
            $conversation->delete();
        });

        // ลบไฟล์แนบใน storage (ถ้าเก็บใน 'public' หรือปรับ disk ตามจริง)
        foreach ($attachments as $path) {
            try {

                $delpath = explode('storage',$path);
                Storage::disk('public')->delete($delpath[1]);
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }

        // แจ้งให้ client ปิดห้อง/รีเฟรช (ยิงทันที ไม่ต้องคิว)
        try {
            broadcast(new \App\Events\ChatMessageSent($conversationId))->toOthers();
        } catch (\Throwable $e) {
        }

        return redirect()->route('chat.index')->with('status', 'ปิดการสนทนาเรียบร้อยแล้ว');
    }

    // (ออปชัน) สร้างห้องแชตตรงกับสมาชิก
    public function directWithMember(Request $req, Members $member)
    {
        $thread = Conversation::firstOrCreateDirectBetween($req->user()->id, $member->id);

        $me = $req->user(); // Members
        $admin = \App\Models\User::where('role', 'admin')->whereKeyNot($member->id)->firstOrFail();
        if ($thread->wasRecentlyCreated) {
            broadcast(new \App\Events\ConversationCreated($thread, [
                'id'   => $me->id,
                'name' => $me->nickname ?: ($me->fullname ?: $me->username),
            ]));
        }
        return redirect()->route('chat.show', $thread);
    }

    public function conversations_count(Request $req)
    {
        $count = Conversation::count();
        return $count;
    }
}
