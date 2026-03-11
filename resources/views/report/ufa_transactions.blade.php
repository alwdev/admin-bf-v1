@extends('layouts.guest')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">UFABET Bet Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">/</a></li>
                        <li class="breadcrumb-item active">UFABET Bet Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 card">
            <div class="card-body">
                <form class="form-inline mb-3" method="GET" action="{{ route('report.ufa_transactions') }}">
                    <div class="form-group mr-2 mb-2">
                        <input type="text" name="username" value="{{ request('username') }}" class="form-control"
                            placeholder="Username">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <input type="text" name="bet_id" value="{{ request('bet_id') }}" class="form-control"
                            placeholder="Bet ID">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <select name="type" class="form-control">
                            <option value="">ทุกประเภท</option>
                            @foreach ($typeOptions as $opt)
                                <option value="{{ $opt }}" {{ request('type') === $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <select name="bet_type" class="form-control">
                            <option value="">ทุก bet_type</option>
                            @foreach ($betTypeOptions as $opt)
                                <option value="{{ $opt }}" {{ request('bet_type') == $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <select name="status" class="form-control">
                            <option value="">ทุกสถานะ</option>
                            @foreach ($statusOptions as $opt)
                                <option value="{{ $opt }}" {{ request('status') == $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <select name="per_page" class="form-control">
                            @foreach ([50, 100, 150, 200] as $pp)
                                <option value="{{ $pp }}"
                                    {{ (int) request('per_page', $perPage ?? 50) === $pp ? 'selected' : '' }}>
                                    {{ $pp }}/หน้า</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-secondary mr-2 mb-2">ค้นหา</button>
                    <a href="{{ route('report.ufa_transactions') }}" class="btn btn-light mb-2">ล้าง</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th style="width: 150px;">Username</th>
                                <th style="width: 120px;">Bet ID</th>
                                <th style="width: 90px;">Bet Type</th>
                                <th style="width: 120px;">Amount</th>
                                <th style="width: 120px;">Bonus</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 130px;">Before</th>
                                <th style="width: 130px;">After</th>
                                <th style="width: 140px;">Type</th>
                                <th style="width: 170px;">Created At</th>
                                <th style="width: 90px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $tx)
                                <tr>
                                    <td>{{ $tx->id }}</td>
                                    <td>{{ $tx->username }}</td>
                                    <td><a
                                            href="{{ route('report.ufa_bill_detail', $tx->bet_id) }}">{{ $tx->bet_id }}</a>
                                    </td>
                                    <td>{{ $tx->bet_type }}</td>
                                    <td>{{ $tx->amount }}</td>
                                    <td>{{ $tx->bonus }}</td>
                                    <td>{{ $tx->status }}</td>
                                    <td>{{ $tx->balance_before }}</td>
                                    <td>{{ $tx->balance_after }}</td>
                                    <td>{{ $tx->type }}</td>
                                    <td>{{ $tx->created_at }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                            data-target="#reqModal{{ $tx->id }}">Req</button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="reqModal{{ $tx->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Request Data #{{ $tx->id }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <pre style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($tx->request_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">ไม่มีข้อมูล</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transactions->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
