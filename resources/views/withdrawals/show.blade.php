@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Withdrawal #{{ $requestItem->id }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('withdrawals.index') }}">Withdrawals</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">
    {{ implode(', ', $errors->all()) }}
</div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">ข้อมูลคำขอ</h5>
        <table class="table table-sm">
            <tr><th>Member</th><td>{{ optional($requestItem->member)->username }}</td></tr>
            <tr><th>Destination Address</th><td>{{ $member->wallet_address ?: '-' }}</td></tr>
            <tr><th>Requested Amount (USDT)</th><td>{{ $requestItem->amount_usdt }}</td></tr>
            <tr><th>Fee (6.5%)</th><td>{{ $requestItem->fee_amount }}</td></tr>
            <tr><th>Net To Transfer</th><td><strong>{{ $requestItem->net_amount }}</strong></td></tr>
            <tr><th>Status</th><td>{{ $requestItem->status }}</td></tr>
            <tr><th>TX Hash</th><td>{{ $requestItem->tx_hash ?: '-' }}</td></tr>
            <tr><th>Created At</th><td>{{ $requestItem->created_at }}</td></tr>
        </table>

        @if($requestItem->status !== 'paid' && $requestItem->status !== 'rejected')
        <hr>
        <h5>บันทึก Hash หลังโอน</h5>
        <form method="POST" action="{{ route('withdrawals.markPaid',$requestItem->id) }}">
            @csrf
            <div class="form-group">
                <label for="tx_hash">TX Hash</label>
                <input type="text" name="tx_hash" id="tx_hash" class="form-control" value="{{ old('tx_hash') }}" required>
            </div>
            <button type="submit" class="btn btn-success mt-2">บันทึกและเปลี่ยนสถานะเป็นโอนแล้ว</button>
        </form>
        @else
        <div class="alert alert-info mt-3">สถานะ: {{ $requestItem->status }} @if($requestItem->status==='paid') (โอนไปแล้ว) @endif</div>
        @endif
    </div>
</div>
@endsection
