<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Request $req, Conversation $conversation)
    {
        abort_unless($conversation->members()->whereKey($req->user()->id)->exists(), 403);

        $after = (int) $req->query('after', 0);
        $q = $conversation->messages()->with(['member:id,username,nickname,fullname'])->orderBy('id', 'asc');
        if ($after > 0) $q->where('id', '>', $after);

        return response()->json(
            $q->take(100)->get()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'conversation_id' => $m->conversation_id,
                    'member_id' => $m->member_id,
                    'is_bot' => (bool) $m->is_bot,
                    'body' => $m->body,
                    // แปลง path -> URL พร้อมใช้
                    'attachments' =>  $m->attachments,
                    'created_at' => $m->created_at?->toISOString(),
                    'member' => [
                        'id' => $m->member_id,
                        'name' => $m->member->nickname ?? ($m->member->fullname ?? $m->member->username),
                        'username' => $m->member->username
                    ],

                ];
            })
        );
    }

    public function store(Request $req, Conversation $conversation)
    {
        abort_unless($conversation->members()->whereKey($req->user()->id)->exists(), 403);

        $data = $req->validate([
            'body' => 'nullable|string|max:5000',
            'attachments' => 'nullable',
            'attachments.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB/ไฟล์
        ]);

        $paths = [];
        if ($req->hasFile('attachments')) {
            foreach ($req->file('attachments') as $file) {
                $paths[] = $file->store('chat', 'public'); // storage/app/public/chat/...
            }
            $paths[0] = env('APP_URL') . '/storage/' . $paths[0];
        }

        // if (!($data['body'] ?? null) && empty($paths)) {
        //     return response()->json(['ok' => false, 'msg' => 'empty'], 422);
        // }
        // error_log($paths[0]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'member_id'       => $req->user()->id,
            'body'            => $data['body'] ?? null,
            'attachments'     => $paths,
            'is_admin'        => 1,
        ]);

        // broadcast ทันที
        try {
            broadcast(new \App\Events\ChatMessageSent($msg))->toOthers();
        } catch (\Throwable $e) {
        }

        return response()->json([
            'ok'          => true,
            'id'          => $msg->id,
            'created_at'  => $msg->created_at?->toISOString(),
            'attachments' => $paths,
            'is_admin'    => $msg->is_admin
        ], 201);
    }
    public function destroy(Request $req, Message $message)
    {
        abort_unless($message->member_id == $req->user()->id, 403);

        $message->delete();

        return response()->json(['ok' => true]);
    }

    public function update_attachments(Request $req)
    {
        $data = $req->validate([
            'message_id' => 'required|integer|exists:messages,id',
            'attachments.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $message = \App\Models\Message::findOrFail($data['message_id']);
        $paths = [];
        if ($req->hasFile('attachments')) {
            foreach ($req->file('attachments') as $file) {
                $paths[] = $file->store('chat', 'public');
            }
        }
        $paths[0] = env('APP_URL') . '/storage/' . $paths[0];
        $message->attachments = $paths;
        $message->save();

        $urls = array_map(fn($p) => Storage::url($p), $paths);

        // (ออปชัน) บรอดแคสต์ “อัปเดตรูป” อีกที ถ้าข้อความถูกสร้างก่อนหน้านี้
        try {
            broadcast(new \App\Events\ChatMessageSent($message))->toOthers();
        } catch (\Throwable $e) {
        }

        return response()->json(['ok' => true, 'paths' => $paths, 'urls' => $urls]);
    }
}
