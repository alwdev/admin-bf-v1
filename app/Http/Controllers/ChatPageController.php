<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $member = $conversation->members()->whereKeyNot($me->id)->first();
        // return ($member);
        return view('chat.show', [
            'thread'=>$conversation,
            'messages'=>$messages,
            'member'=>$member,
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
            // ด้วย schema ที่ใช้ constrained()->cascadeOnDelete():
            // ลบ conversation จะ cascade ลบ conversation_member, messages
            // และจาก messages จะ cascade ต่อไปยัง message_readers
            $conversation->delete();
        });

        // ลบไฟล์แนบใน storage (ถ้าเก็บใน 'public' หรือปรับ disk ตามจริง)
        foreach ($attachments as $path) {
            try { Storage::disk('public')->delete($path); } catch (\Throwable $e) {}
        }

        // แจ้งให้ client ปิดห้อง/รีเฟรช (ยิงทันที ไม่ต้องคิว)
        try { broadcast(new \App\Events\ChatMessageSent($conversationId))->toOthers(); } catch (\Throwable $e) {}

        return redirect()->route('chat.index')->with('status', 'ปิดการสนทนาเรียบร้อยแล้ว');
    }

    // (ออปชัน) สร้างห้องแชตตรงกับสมาชิก
    public function directWithMember(Request $req, Members $member) {
        $thread = Conversation::firstOrCreateDirectBetween($req->user()->id, $member->id);
        return redirect()->route('chat.show', $thread);
    }
}
