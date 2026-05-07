@extends('layouts.guest')

@section('styles')
    @include('amb._list-styles')
    <style>
        .amb-debug-pre {
            max-height: 55vh;
            overflow: auto;
            font-size: 12px;
            background: #1a1b26;
            color: #c8d3f5;
            border-radius: 6px;
            padding: 14px;
            white-space: pre-wrap;
            word-break: break-word;
            margin: 0;
        }
        .amb-debug-stat {
            font-size: 14px;
        }
        .amb-debug-issue-ok {
            color: #28a745;
            font-weight: 600;
        }
        .amb-debug-table-actions {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <div class="amb-list-page">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between flex-wrap">
                    <h4 class="mb-0 font-size-18">AMB Debug</h4>
                    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                        <a href="{{ route('amb.games.index') }}" class="btn btn-outline-secondary btn-sm">กลับ AMB Games</a>
                        <a href="{{ route('amb.api-check.index') }}" class="btn btn-outline-primary btn-sm">หน้าเช็ค API (ตาราง DB)</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Config</h5>
                        <ul class="list-unstyled amb-debug-stat mb-0">
                            <li><strong>Base URL:</strong> <code>{{ $baseUrl !== '' ? $baseUrl : '— ยังไม่ตั้ง AMB_SEAMLESS_BASE_URL' }}</code></li>
                            <li><strong>Agent:</strong> @if ($agentOk)<span class="text-success">OK</span>@else<span class="text-danger">ไม่ครบ</span> <small class="text-muted">(AMB_AGENT หรือ Bearer)</small>@endif</li>
                            <li><strong>API Secret:</strong> @if ($secretOk)<span class="text-success">OK</span>@else<span class="text-danger">ไม่ครบ</span> <small class="text-muted">(AMB_API_SECRET หรือ Bearer)</small>@endif</li>
                            <li><strong>Ready:</strong> @if ($ready)<span class="text-success font-weight-bold">YES</span>@else<span class="text-warning font-weight-bold">NO</span>@endif</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">DB Summary</h5>
                        <ul class="list-unstyled amb-debug-stat mb-0">
                            <li><strong>Categories:</strong> {{ $catActive }}/{{ $catTotal }} active</li>
                            <li><strong>Products:</strong> {{ $prodActive }}/{{ $prodTotal }} active</li>
                            <li><strong>Games:</strong> {{ $gameActive }}/{{ $gameTotal }} active</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Detected Issues</h5>
                <ul class="mb-0 pl-3">
                    <li>Active categories without products:
                        @if ($issues['categories_no_products'] === 0)
                            <span class="amb-debug-issue-ok">none</span>
                        @else
                            <span class="text-warning">{{ $issues['categories_no_products'] }}</span>
                        @endif
                    </li>
                    <li>Active categories without games:
                        @if ($issues['categories_no_games'] === 0)
                            <span class="amb-debug-issue-ok">none</span>
                        @else
                            <span class="text-warning">{{ $issues['categories_no_games'] }}</span>
                        @endif
                    </li>
                    <li>Active products without games:
                        @if ($issues['products_no_games'] === 0)
                            <span class="amb-debug-issue-ok">none</span>
                        @else
                            <span class="text-warning">{{ $issues['products_no_games'] }}</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        <div class="alert alert-secondary">
            <strong>ทดสอบ API:</strong> กดปุ่ม <strong>ทดสอบ</strong> ในแต่ละแถว — จะเรียก API จริงแล้วแสดง response ในกล่องป๊อปอัป
            (ใช้ <code>AMB_SEAMLESS_BASE_URL</code> และ token ตาม config)
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">API Checks</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Endpoint</th>
                                <th>Product</th>
                                <th class="amb-debug-table-actions" style="width: 100px;">ทดสอบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($checkRows as $row)
                                <tr>
                                    <td><code>{{ $row['endpoint'] }}</code></td>
                                    <td>@if ($row['product'])<code>{{ $row['product'] }}</code>@else—@endif</td>
                                    <td class="amb-debug-table-actions">
                                        <button type="button"
                                            class="btn btn-sm btn-primary amb-debug-probe"
                                            data-target="{{ $row['target'] }}"
                                            data-product="{{ $row['product'] ?? '' }}">
                                            ทดสอบ
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ambDebugProbeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Response</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="amb-debug-probe-meta" class="small text-muted mb-2"></div>
                        <pre id="amb-debug-probe-body" class="amb-debug-pre">—</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            const metaEl = document.getElementById('amb-debug-probe-meta');
            const outEl = document.getElementById('amb-debug-probe-body');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const probeUrl = @json(route('amb.api-check.probe'));
            const $modal = $('#ambDebugProbeModal');

            function showPayload(data) {
                const bits = [];
                if (data.url) bits.push('URL: ' + data.url);
                if (data.http_status !== undefined) bits.push('HTTP: ' + data.http_status);
                if (data.api_status_code !== undefined && data.api_status_code !== null) {
                    bits.push('API statusCode: ' + data.api_status_code);
                }
                if (data.list_item_count !== undefined) bits.push('รายการ (parsed): ' + data.list_item_count);
                if (data.body_truncated) bits.push('body ถูกตัดใน preview');
                if (data.response_json_omitted) bits.push('JSON เต็มใหญ่เกิน — ดูใน body_preview');
                if (data.error) bits.push('Error: ' + data.error);
                metaEl.textContent = bits.join(' · ') || '';

                let pretty = '';
                if (data.response_json !== null && data.response_json !== undefined) {
                    try {
                        pretty = JSON.stringify(data.response_json, null, 2);
                    } catch (e) {
                        pretty = String(data.response_json);
                    }
                } else if (data.body_preview) {
                    try {
                        pretty = JSON.stringify(JSON.parse(data.body_preview), null, 2);
                    } catch (e) {
                        pretty = data.body_preview;
                    }
                } else {
                    pretty = JSON.stringify(data, null, 2);
                }
                outEl.textContent = pretty;
            }

            document.querySelectorAll('.amb-debug-probe').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const target = btn.getAttribute('data-target');
                    const product = btn.getAttribute('data-product') || '';
                    metaEl.textContent = 'กำลังเรียก…';
                    outEl.textContent = '…';
                    $modal.modal('show');

                    const payload = { target: target };
                    if (target === 'games' && product) payload.product_code = product;

                    fetch(probeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    })
                        .then(function (r) { return r.json(); })
                        .then(showPayload)
                        .catch(function (e) {
                            metaEl.textContent = 'ล้มเหลว';
                            outEl.textContent = String(e);
                        });
                });
            });
        })();
    </script>
@endsection
