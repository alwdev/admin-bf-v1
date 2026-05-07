@extends('layouts.guest')

@section('styles')
    @include('amb._list-styles')
    <style>
        .amb-api-check-pre {
            max-height: 420px;
            overflow: auto;
            font-size: 12px;
            background: #1e1e2e;
            color: #e0e0e0;
            border-radius: 6px;
            padding: 12px;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .amb-api-check-meta {
            font-size: 13px;
        }
        .amb-api-check-meta code {
            font-size: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="amb-list-page">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between flex-wrap">
                    <h4 class="mb-0 font-size-18">AMB — เช็ค API กับ DB</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">AMB API Check</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info mb-3">
            ตารางด้านล่างมาจาก <strong>ฐานข้อมูล</strong> (<code>amb_products</code>) — กดปุ่มเพื่อเรียก Seamless API จริงแล้วดู response ด้านขวา
            (ตั้งค่า <code>AMB_SEAMLESS_BASE_URL</code> / token ใน .env)
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">เช็ค endpoint ทั่วไป</h5>
                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm amb-probe-global" data-target="categories">
                        GET /seamless/categories
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm amb-probe-global" data-target="products">
                        GET /seamless/products
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Providers ใน DB</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ชื่อ</th>
                                        <th>หมวด</th>
                                        <th class="text-right">เกมใน DB</th>
                                        <th style="width: 110px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $p)
                                        <tr>
                                            <td><code>{{ $p->product_code }}</code></td>
                                            <td>{{ $p->product_name }}</td>
                                            <td>
                                                @if ($p->category)
                                                    {{ $p->category->code }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($p->games_count) }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-primary btn-sm btn-block amb-probe-games py-1"
                                                    data-code="{{ $p->product_code }}">
                                                    เช็ค API
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">ยังไม่มี provider ใน DB</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">ผลลัพธ์ล่าสุด</h5>
                        <div id="amb-api-check-meta" class="amb-api-check-meta text-muted mb-2">ยังไม่ได้เรียก API</div>
                        <pre id="amb-api-check-output" class="amb-api-check-pre mb-0">—</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            const metaEl = document.getElementById('amb-api-check-meta');
            const outEl = document.getElementById('amb-api-check-output');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = @json(route('amb.api-check.probe'));

            function showResult(data) {
                const lines = [];
                if (data.url) lines.push('URL: ' + data.url);
                if (data.http_status !== undefined) lines.push('HTTP: ' + data.http_status);
                if (data.api_status_code !== undefined && data.api_status_code !== null) {
                    lines.push('API statusCode (ใน body): ' + data.api_status_code);
                }
                if (data.list_item_count !== undefined) {
                    lines.push('จำนวนรายการ (parse เป็น list): ' + data.list_item_count);
                }
                if (data.body_truncated) lines.push('(body ตัดใน preview — ดูด้านล่าง)');
                if (data.response_json_omitted) lines.push('(JSON เต็มใหญ่เกินไป — แสดงเฉพาะ body_preview)');
                if (data.error) lines.push('Error: ' + data.error);
                metaEl.textContent = lines.join(' · ') || (data.ok ? 'สำเร็จ' : 'ล้มเหลว');

                let pretty = '';
                if (data.response_json !== null && data.response_json !== undefined) {
                    try {
                        pretty = JSON.stringify(data.response_json, null, 2);
                    } catch (e) {
                        pretty = String(data.response_json);
                    }
                } else if (data.body_preview) {
                    try {
                        const j = JSON.parse(data.body_preview);
                        pretty = JSON.stringify(j, null, 2);
                    } catch (e) {
                        pretty = data.body_preview;
                    }
                } else {
                    pretty = JSON.stringify(data, null, 2);
                }
                outEl.textContent = pretty;
            }

            async function probe(payload) {
                metaEl.textContent = 'กำลังเรียก API…';
                outEl.textContent = '…';
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    showResult(data);
                } catch (e) {
                    metaEl.textContent = 'เรียกไม่สำเร็จ';
                    outEl.textContent = String(e);
                }
            }

            document.querySelectorAll('.amb-probe-global').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    probe({ target: btn.getAttribute('data-target') });
                });
            });

            document.querySelectorAll('.amb-probe-games').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const code = btn.getAttribute('data-code');
                    probe({ target: 'games', product_code: code });
                });
            });
        })();
    </script>
@endsection
