@extends('layouts.guest')
@section('styles')
    <style>
        .badge {

            padding: 10px;
            font-weight: 400;
        }
    </style>
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Chat</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Chat</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div id="chat-app" data-thread-id="{{ $thread->id }}" data-me-id="{{ auth()->id() }}"
        data-fetch-url="{{ route('chat.messages.index', $thread) }}"
        data-send-url="{{ route('chat.messages.store', $thread) }}">
        {{-- เหมือนตัวอย่าง UI ที่ผมให้ก่อนหน้าได้เลย --}}
        <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header bg-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;font-weight:600;">
                                {{ strtoupper(substr($thread->title ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $thread->title ?? 'สนทนาลูกค้า' }}</div>
                                <div id="presence" class="small text-muted">กำลังเชื่อมต่อ…</div>
                            </div>
                        </div>
                        <div class="small text-muted d-none d-md-block">
                            หมายเลขห้อง #{{ $thread->id }}
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div class="card-body p-0">
                        <div id="chatMessages" class="p-3" style="height: calc(100vh - 270px); overflow-y:auto;">
                            @forelse($messages as $m)
                                @php $mine = $m->user_id === 1; @endphp
                                <div class="d-flex mb-2 {{ $mine ? 'justify-content-end' : 'justify-content-start' }}">
                                    @unless ($mine)
                                        <div class="me-2 rounded-circle bg-light d-none d-md-flex align-items-center justify-content-center"
                                            style="width:28px;height:28px;">
                                            <span class="small">{{ strtoupper(substr($m->user->name ?? 'A', 0, 1)) }}</span>
                                        </div>
                                    @endunless
                                    <div class="px-3 py-2 rounded-3 {{ $mine ? 'bg-primary text-white' : 'bg-light' }}"
                                        style="max-width: 70%;">
                                        @if (!empty($m->body))
                                            <div class="white-space-prewrap">{{ $m->body }}</div>
                                        @endif
                                        <div class="small opacity-75 text-end mt-1">
                                            {{ $m->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">เริ่มบทสนทนาได้เลย</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Composer --}}
                    <div class="card-footer bg-white">
                        <form id="sendMessageForm" class="d-flex gap-2" autocomplete="off">
                            @csrf
                            <input id="chatInput" name="body" type="text" class="form-control"
                                placeholder="พิมพ์ข้อความ…" maxlength="5000">
                            <button class="btn btn-primary px-4" type="submit">ส่ง</button>
                        </form>
                        <div id="typing" class="small text-muted mt-1 d-none">แอดมินกำลังพิมพ์…</div>
                    </div>
                </div>
    </div>
@endsection
