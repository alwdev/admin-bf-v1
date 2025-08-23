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
    <meta name="csrf-token" content="{{ csrf_token() }}">
@php
                $meId = auth()->id();
            @endphp
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
        <div class="card shadow-sm border-0">
                    {{-- Header --}}
                    <div class="card-header d-flex align-items-center justify-content-between" style="background-color: #818181;color: #ffffff">
                        <div class="d-flex align-items-center gap-2">
                            {{-- <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;font-weight:600;">
                                {{ strtoupper(substr($thread->title ?? 'A', 0, 1)) }}
                            </div> --}}
                            <div>
                                <div class="fw-semibold" style="color: #ffffff">ลูกค้า :{{ $member->fullname  }} | {{ $member->username  }} | email : {{ $member->email }}</div>
                                <div id="presence" class="small text-muted">กำลังเชื่อมต่อ…</div>
                            </div>
                        </div>
                        <div class="d-none d-md-block" style="color: #ffffff">
                            หมายเลขห้อง #{{ $thread->id }}
                        </div>
                        {{-- Button end chat--}}
                        <div>
                            <form method="POST" action="{{ route('chat.conversations.close', $thread) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-light" type="submit"
                                    onclick="return confirm('ยืนยันปิดการสนทนา?');">จบการสนทนา</button>
                            </form>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div class="card-body p-0">
                        <div id="chatMessages" class="p-3" style="height: calc(100vh - 270px); overflow-y:auto;">
                            @forelse($messages as $m)
                                @php $mine = $m->member_id === $meId; @endphp
                                <div class="d-flex mb-2 {{ $mine ? 'justify-content-end' : 'justify-content-start' }}">
                                    @unless ($mine)
                                        <div class="me-2  d-none d-md-flex align-items-center justify-content-center"
                                            style="width:100px;height:28px;">
                                            <span class="small">{{ $m->member->username  }}</span>
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
                        <div id="typing" class="small text-muted mt-1 d-none">ลูกค้ากำลังพิมพ์…</div>
                    </div>
                </div>
    </div>
@endsection
@section('scripts')
    {{-- Logic (Echo realtime ถ้ามี / ถ้าไม่มีจะ fallback เป็น polling) --}}
    <script>
        (function() {
            const elApp = document.getElementById('chat-app');
            const elBox = document.getElementById('chatMessages');
            const elForm = document.getElementById('sendMessageForm');
            const elInput = document.getElementById('chatInput');
            const elTyping = document.getElementById('typing');
            const elPresence = document.getElementById('presence');

            const threadId = elApp.dataset.threadId;
            const meId = Number(elApp.dataset.meId);
            const fetchUrl = elApp.dataset.fetchUrl;
            const sendUrl = elApp.dataset.sendUrl;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            let lastId = (function getLastIdFromDOM() {
                const items = elBox.querySelectorAll('[data-message-id]');
                return items.length ? Number(items[items.length - 1].dataset.messageId) : (
                    {{ $messages->last()->id ?? 0 }});
            })();

            const scrollToBottom = (smooth = true) => {
                elBox.scrollTo({
                    top: elBox.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            };
            scrollToBottom(false);

            const renderMsg = (m) => {
                console.log(m, meId);
                const mine = Number(m.member_id) === meId;
                const wrap = document.createElement('div');
                wrap.className = `d-flex mb-2 ${mine ? 'justify-content-end' : 'justify-content-start'}`;
                wrap.setAttribute('data-message-id', m.id);
                wrap.innerHTML = `
      ${mine ? '' : `
                        <div class="me-2  d-none d-md-flex align-items-center justify-content-center" style="width:100px;height:28px;">
                          <span class="small">${m.member?.username || 'Customer'}</span>
                        </div>`}
      <div class="px-3 py-2 rounded-3 ${mine ? 'bg-primary text-white' : 'bg-light'}" style="max-width:70%;">
        ${m.body ? `<div class="white-space-prewrap">${escapeHtml(m.body)}</div>` : ''}
        <div class="small ${mine ? 'opacity-75' : 'text-muted'} text-end mt-1">${formatTime(m.created_at)}</div>
      </div>
    `;
                elBox.appendChild(wrap);
            };

            function escapeHtml(s) {
                return s.replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                } [m]));
            }

            function formatTime(iso) {
                try {
                    const d = new Date(iso);
                    return d.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch {
                    return '';
                }
            }

            // Submit
            elForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const body = elInput.value.trim();
                if (!body) return;

                // optimistic UI
                const temp = {
                    id: ++lastId,
                    member_id: meId,
                    body,
                    created_at: new Date().toISOString(),
                    user: {
                        name: 'ฉัน'
                    }
                };
                renderMsg(temp);
                scrollToBottom();

                elInput.value = '';
                try {
                    await fetch(sendUrl, {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            body
                        })
                    });
                } catch (err) {
                    console.error(err);
                }
            });

            // Realtime via Echo (ถ้ามี)
            let polling;
            if (window.Echo) {
                elPresence.textContent = 'ออนไลน์';
                const channel = window.Echo.private(`conversations.${threadId}`);

                channel.listen('.chat.message.sent', (e) => {
                    // รับข้อความใหม่
                    renderMsg(e);
                    lastId = e.id;
                    scrollToBottom();
                });

                // typing indicators
                let typingTimer;
                elInput.addEventListener('input', () => {
                    channel.whisper('typing', {
                        user_id: meId
                    });
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => channel.whisper('stop-typing', {
                        user_id: meId
                    }), 1500);
                });
                channel.listenForWhisper('typing', () => {
                    elTyping.classList.remove('d-none');
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => elTyping.classList.add('d-none'), 1500);
                });
                channel.listenForWhisper('stop-typing', () => elTyping.classList.add('d-none'));

            } else {
                // Fallback polling
                elPresence.textContent = '';
                polling = setInterval(async () => {
                    try {
                        const res = await fetch(`${fetchUrl}?after=${lastId}`, {
                            credentials: 'include',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!res.ok) return;
                        const data = await res.json(); // [{id, user_id, body, created_at, user:{name}}]
                        data.forEach(m => {
                            renderMsg(m);
                            lastId = Math.max(lastId, Number(m.id));
                        });
                        if (data.length) scrollToBottom();
                    } catch (e) {}
                }, 2000);
            }
        })();
    </script>
@endsection
