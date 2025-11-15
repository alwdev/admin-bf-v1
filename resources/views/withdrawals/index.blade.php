@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">USDT Withdrawal Requests</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Withdrawals</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="form-inline">
            <div class="form-group mr-2">
                <label for="status" class="mr-2">สถานะ</label>
                <select name="status" id="status" class="form-control">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Member</th>
                    <th>Requested Amount (USDT)</th>
                    <th>Fee 6.5%</th>
                    <th>Net To Transfer</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ optional($item->member)->username }}</td>
                    <td>{{ $item->amount_usdt }}</td>
                    <td>{{ $item->fee_amount }}</td>
                    <td>{{ $item->net_amount }}</td>
                    <td>
                        @if($item->status==='paid')
                            <span class="badge badge-success">paid</span>
                        @elseif($item->status==='approved')
                            <span class="badge badge-info">approved</span>
                        @elseif($item->status==='rejected')
                            <span class="badge badge-danger">rejected</span>
                        @else
                            <span class="badge badge-secondary">pending</span>
                        @endif
                    </td>
                    <td>{{ $item->created_at }}</td>
                    <td><a href="{{ route('withdrawals.show',$item->id) }}" class="btn btn-sm btn-outline-primary">ดู</a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">ไม่พบรายการ</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $requests->links() }}
    </div>
</div>
@endsection
