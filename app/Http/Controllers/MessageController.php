<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $req, Conversation $conversation) {
        abort_unless($conversation->members()->whereKey($req->user()->id)->exists(), 403);
        $after = (int) $req->query('after', 0);
        $q = $conversation->messages()->with(['member:id,username,nickname,fullname'])->orderBy('id','asc');
        if ($after>0) $q->where('id','>', $after);
        return response()->json($q->take(100)->get()->map(function($m){
            return [
                'id'=>$m->id,'conversation_id'=>$m->conversation_id,'member_id'=>$m->member_id,
                'body'=>$m->body,'attachments'=>$m->attachments,
                'created_at'=>$m->created_at?->toISOString(),
                'member'=>['id'=>$m->member_id,'name'=>$m->member->nickname ?: ($m->member->fullname ?: $m->member->username),
                           'username'=>$m->member->username],
            ];
        }));
    }

    public function store(Request $req, Conversation $conversation) {
        abort_unless($conversation->members()->whereKey($req->user()->id)->exists(), 403);

        $data = $req->validate([
            'body'=>'nullable|string|max:5000',
            'attachments.*'=>'file|max:10240',
        ]);

        $attachments = null;
        if ($req->hasFile('attachments')) {
            $paths = [];
            foreach ($req->file('attachments') as $f) $paths[] = $f->store('chat','public');
            $attachments = $paths;
        }

        $msg = Message::create([
            'conversation_id'=>$conversation->id,
            'member_id'=>$req->user()->id,
            'body'=>$data['body'] ?? null,
            'attachments'=>$attachments,
        ]);

        try { broadcast(new \App\Events\ChatMessageSent($msg))->toOthers(); } catch (\Throwable $e) {}

        return response()->json(['ok'=>true,'id'=>$msg->id], 201);
    }
}
