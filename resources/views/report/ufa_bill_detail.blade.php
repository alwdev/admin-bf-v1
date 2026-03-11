@extends('layouts.guest')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">UFABET Bill Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">/</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('report.ufa_transactions') }}">UFABET Bet Report</a></li>
                        <li class="breadcrumb-item active">Bill #{{ $betId }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 card">
            <div class="card-body">
                <div class="mb-3">
                    <strong>Bet ID:</strong> {{ $betId }}<br>
                    <strong>Username:</strong> {{ $tx->username }}<br>
                    <strong>Type:</strong> {{ $tx->type }}<br>
                    <strong>Bet Type:</strong> {{ $tx->bet_type }}<br>
                    <strong>Amount:</strong> {{ $tx->amount }}<br>
                    <strong>Bonus:</strong> {{ $tx->bonus }}<br>
                    <strong>Status:</strong> {{ $tx->status }}<br>
                    <strong>Balance Before:</strong> {{ $tx->balance_before }}<br>
                    <strong>Balance After:</strong> {{ $tx->balance_after }}<br>
                    <strong>Created At:</strong> {{ $tx->created_at }}<br>
                </div>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-rendered" role="tab">Bill Detail</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-api" role="tab">API Raw</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-request" role="tab">Stored Request Data</a>
                    </li>
                </ul>

                <div class="tab-content p-3 border border-top-0">
                    <div class="tab-pane active" id="tab-rendered" role="tabpanel">
                        @if ($apiResponseHtml)
                            <div class="border rounded p-2 bg-white">
                                {!! $apiResponseHtml !!}
                            </div>
                        @elseif ($apiError)
                            <div class="alert alert-warning mb-0">{{ $apiError }}</div>
                        @else
                            <div class="text-muted">ไม่มีข้อมูลจาก API</div>
                        @endif
                    </div>

                    <div class="tab-pane" id="tab-api" role="tabpanel">
                        <div class="mb-2">
                            <strong>Endpoint:</strong> {{ $apiUrl }}/bill
                        </div>

                        @if ($apiError)
                            <div class="alert alert-warning mb-3">{{ $apiError }}</div>
                        @endif

                        @if ($apiResponseJson)
                            <pre style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($apiResponseJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                        @elseif ($apiResponseRaw)
                            <pre style="white-space: pre-wrap; word-break: break-word;">{{ $apiResponseRaw }}</pre>
                        @else
                            <div class="text-muted">ไม่มีข้อมูลจาก API</div>
                        @endif
                    </div>
                    <div class="tab-pane" id="tab-request" role="tabpanel">
                        <pre style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($tx->request_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
