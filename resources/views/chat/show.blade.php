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
            <div class="card-header d-flex align-items-center justify-content-between"
                style="background-color: #818181;color: #ffffff">
                <div class="d-flex align-items-center gap-2">
                    {{-- <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;font-weight:600;">
                                {{ strtoupper(substr($thread->title ?? 'A', 0, 1)) }}
                            </div> --}}
                    <div>
                        <div class="fw-semibold" style="color: #ffffff">ลูกค้า :{{ $member->fullname }} |
                            {{ $member->username }} | email : {{ $member->email }}</div>
                        <div id="presence" class="small text-muted">กำลังเชื่อมต่อ…</div>
                    </div>
                </div>
                <div class="d-none d-md-block" style="color: #ffffff">
                    หมายเลขห้อง #{{ $thread->id }}
                </div>
                {{-- Button end chat --}}
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
                        <div class="d-flex mb-2 {{ $mine ? 'justify-content-end' : 'justify-content-start' }}"
                            data-message-id="{{ $m->id }}">
                            @unless ($mine)
                                <div class="me-2  d-none d-md-flex align-items-center justify-content-center"
                                    style="width:100px;height:28px;">
                                    <span class="small">{{ $m->member->username }}</span>
                                </div>
                            @endunless
                            <div class="px-3 py-2 rounded-3 {{ $mine ? 'bg-primary text-white' : 'bg-light' }}"
                                style="max-width: 70%;">
                                @if (!empty($m->body))
                                    <div class="white-space-prewrap">{{ $m->body }}</div>
                                @endif
                                @if ($m->attachments && is_array($m->attachments) && count($m->attachments))
                                    <div class="mt-2 d-flex gap-2 flex-wrap">
                                        @foreach ($m->attachments as $path)
                                            @php $url = $path; @endphp
                                            <a href="{{ $url }}" target="_blank" class="d-inline-block">
                                                <img src="{{ $url }}" class="img-thumbnail"
                                                    style="max-width:160px; max-height:160px; object-fit:cover;">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="small opacity-75 text-end mt-1">{{ $m->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">เริ่มบทสนทนาได้เลย</div>
                    @endforelse
                </div>
            </div>

            {{-- Composer: ปุ่มแนบ + input file + preview --}}
            <div class="card-footer bg-white">
                <form id="sendMessageForm" class="d-flex gap-2 align-items-center" autocomplete="off">
                    @csrf
                    <button type="button" id="btnAttach" class="btn btn-outline-secondary">แนบรูป</button>
                    <input id="chatFiles" type="file" accept="image/*" multiple class="d-none">
                    <input id="chatInput" name="body" type="text" class="form-control" placeholder="พิมพ์ข้อความ…"
                        maxlength="5000">
                    <button class="btn btn-primary px-4" type="submit">ส่ง</button>
                </form>
                <div id="preview" class="mt-2 d-flex gap-2 flex-wrap"></div>
                <div id="typing" class="small text-muted mt-1 d-none">กำลังพิมพ์…</div>
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
            const elFiles = document.getElementById('chatFiles');
            const btnAttach = document.getElementById('btnAttach');
            const elPreview = document.getElementById('preview');

            btnAttach.addEventListener('click', () => elFiles.click());
            // preview รูปที่เลือก
            let pendingFiles = [];
            elFiles.addEventListener('change', () => {
                pendingFiles = Array.from(elFiles.files || []);
                elPreview.innerHTML = '';
                pendingFiles.forEach(f => {
                    if (!f.type.startsWith('image/')) return;
                    const url = URL.createObjectURL(f);
                    const img = document.createElement('img');
                    img.src = url;
                    img.className = 'img-thumbnail';
                    img.style.maxWidth = '120px';
                    img.style.maxHeight = '120px';
                    img.onload = () => URL.revokeObjectURL(url);
                    elPreview.appendChild(img);
                });
            });

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

            function norm(u) {
                if (!u) return '';
                if (u.startsWith('http://') || u.startsWith('https://') || u.startsWith('/') || u.startsWith('blob:')) {
                    return u;
                }
                // เป็นพาธจาก DB เช่น "chat/xxx.png"
                return '/storage/' + u.replace(/^\/+/, '');
            }

            function normalizeAttachments(att) {
                if (!att) return [];
                if (typeof att === 'string') {
                    try {
                        const parsed = JSON.parse(att);
                        att = Array.isArray(parsed) ? parsed : [att];
                    } catch {
                        att = [att]; // เป็นสตริงพาธเดี่ยว
                    }
                }
                if (!Array.isArray(att)) return [];
                return att.map(norm);
            }

            const renderMsg = (m) => {
                if (!m) return;
                const mine = Number(m.member_id) === meId;
                const wrap = document.createElement('div');
                wrap.className = `d-flex mb-2 ${mine ? 'justify-content-end' : 'justify-content-start'}`;
                wrap.setAttribute('data-message-id', m.id);

                const atts = normalizeAttachments(m.attachments);
                const imgs = atts.length ?
                    `<div class="mt-2 d-flex gap-2 flex-wrap">
         ${atts.map(u => `
               <a href="${u}" target="_blank" class="d-inline-block">
                 <img src="${u}" class="img-thumbnail" style="max-width:160px; max-height:160px; object-fit:cover;">
               </a>`).join('')}
       </div>` :
                    '';

                wrap.innerHTML = `
    ${mine ? '' : `
          <div class="me-2 d-none d-md-flex align-items-center justify-content-center" style="width:100px;height:28px;">
            <span class="small">${m.member?.username || 'Customer'}</span>
          </div>`}
    <div class="px-3 py-2 rounded-3 ${mine ? 'bg-primary text-white' : 'bg-light'}" style="max-width:70%;">
      ${m.body ? `<div class="white-space-prewrap">${escapeHtml(m.body)}</div>` : ''}
      ${imgs}  <!-- ✅ แทรกรูปเข้ามาที่นี่ -->
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
                const hasFiles = pendingFiles.length > 0;
                if (!body && !hasFiles) return;

                // optimistic (ใช้ temp id ติดลบ) + preview รูป
                const tempId = -Date.now();
                const tempUrls = pendingFiles
                    .filter(f => f.type.startsWith('image/'))
                    .map(f => URL.createObjectURL(f));
                renderMsg({
                    id: tempId,
                    member_id: meId,
                    body,
                    created_at: new Date().toISOString(),
                    attachments: tempUrls
                });
                scrollToBottom();

                const fd = new FormData();
                if (body) fd.append('body', body);
                pendingFiles.forEach(f => fd.append('attachments[]', f));

                // clear UI
                elInput.value = '';
                elFiles.value = '';
                elPreview.innerHTML = '';
                pendingFiles = [];

                try {
                    const resp = await fetch(sendUrl, {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: fd
                    });
                    if (!resp.ok) {
                        console.error('send failed', resp.status, await resp.text());
                        return;
                    }
                    const data = await resp.json(); // {ok,id,created_at,attachments:[urls]}
                    // แทนที่ temp id ด้วย id จริง + URL จริง
                    const node = document.querySelector(`[data-message-id="${tempId}"]`);
                    if (node) {
                        node.setAttribute('data-message-id', data.id);
                        if (data.created_at) {
                            const t = node.querySelector('.small.text-end, .small.opacity-75.text-end');
                            if (t) t.textContent = formatTime(data.created_at);
                        }
                        if (Array.isArray(data.attachments) && data.attachments.length) {
                            const wrap = node.querySelector('.d-flex.gap-2.flex-wrap') || document
                                .createElement('div');
                            wrap.className = 'mt-2 d-flex gap-2 flex-wrap';
                            wrap.innerHTML = data.attachments.map(u =>
                                `<a href="${u}" target="_blank" class="d-inline-block">
             <img src="${u}" class="img-thumbnail" style="max-width:160px; max-height:160px; object-fit:cover;">
           </a>`).join('');
                            const block = node.querySelector('.white-space-prewrap')?.parentElement || node
                                .children[0];
                            if (block && !block.contains(wrap)) block.insertAdjacentElement('beforeend',
                                wrap);
                        }
                    }
                    lastId = Math.max(lastId, Number(data.id));
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
                }, 4000);
            }
        })();
    </script>
@endsection
