@extends('layouts.guest')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Admin Login Logs</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">/</a></li>
                        <li class="breadcrumb-item active">Admin Login Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 card">
            <div class="card-body">
                <form class="form-inline mb-3" method="GET" action="{{ route('admin.login_logs.index') }}">
                    <div class="form-group mr-2">
                        <select name="action" class="form-control">
                            <option value="">ทุกเหตุการณ์</option>
                            @foreach (['login','logout','failed'] as $a)
                                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ strtoupper($a) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <input type="text" name="identifier" value="{{ request('identifier') }}" class="form-control" placeholder="User/Email">
                    </div>
                    <div class="form-group mr-2">
                        <input type="text" name="ip" value="{{ request('ip') }}" class="form-control" placeholder="IP">
                    </div>
                    <div class="form-group mr-2">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>
                    <div class="form-group mr-2">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>
                    <div class="form-group mr-2">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>
                    <div class="form-group mr-2">
                        <select name="per_page" class="form-control">
                            @foreach ([50, 100, 150, 200] as $pp)
                                <option value="{{ $pp }}" {{ (int) request('per_page', $perPage ?? 50) === $pp ? 'selected' : '' }}>{{ $pp }}/หน้า</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-secondary mr-2">ค้นหา</button>
                    <a href="{{ route('admin.login_logs.index') }}" class="btn btn-light">ล้าง</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 160px;">เวลา</th>
                                <th>ผู้ใช้</th>
                                <th style="width: 90px;">Action</th>
                                <th style="width: 140px;">IP</th>
                                <th>User Agent</th>
                                <th style="width: 180px;">Session</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at }}</td>
                                    <td>
                                        @if ($log->user)
                                            {{ $log->user->name }} <small class="text-muted">({{ $log->user->email }})</small>
                                        @else
                                            {{ $log->identifier }}
                                        @endif
                                    </td>
                                    <td>{{ strtoupper($log->action) }}</td>
                                    <td>{{ $log->ip }}</td>
                                    <td style="max-width: 520px; word-break: break-word;">{{ $log->user_agent }}</td>
                                    <td style="max-width: 240px; word-break: break-word;">{{ $log->session_id }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $logs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
