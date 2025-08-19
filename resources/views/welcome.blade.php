@extends('layouts.guest')
@section('styles')
    <link href="{{ asset('daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endsection
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Dashboard</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="content-header mb-3">
        <div class="row">
            <div class="col-10"></div>
            <div class="col-2 text-right">
                <input type="text" name="date_picker" class="form-control form-select" id="date_picker"
                    style="font-size:15px;">
            </div>
        </div>
    </div>
    <div class="row" id="dashboard-row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            <img src="{{ asset('images/dashboard_icon/savings.png') }}" alt="" width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.deposit_amount') }}</h6>
                    <h3 class="my-3">{{ number_format((float) $total_deposit, 2) }}</h3>

                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <h5 class="mb-0 text-primary">{{ number_format((float) $total_deposit_abc, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">ABC</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-info">{{ number_format((float) $total_deposit_fnx, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">FNX</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-success">{{ number_format((float) $total_deposit_usdt, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">USDT</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-warning">{{ number_format((float) $total_deposit_ktx, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">KTX</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            <img src="{{ asset('images/dashboard_icon/cash-withdrawal.png') }}" alt=""
                                width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.withdraw_amount') }}</h6>
                    <h3 class="my-3">{{ number_format((float) $total_withdraw, 2) }} <span
                            style="font-size: 14px;color:rgb(145, 143, 143);">USDF/USDT</span></h3>

                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <h5 class="mb-0 text-danger">{{ number_format((float) $total_withdraw_abc, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">ABC</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-primary">{{ number_format((float) $total_withdraw_usdf, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">USDF</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-success">{{ number_format((float) $total_withdraw_usdt, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">USDT</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-dollar-circle m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/earnings.png') }}" alt="" width="32">
                        </span>
                    </div>
                    @php
                        $total_profit = (float) $total_deposit - (float) $total_withdraw_abc;

                        $thb_ = 33.0;
                        $fee = 6.5 / 100; // 0.065
                        $amount__ = $total_profit;

                        $exchanged_amount = $amount__ / $thb_;
                        $fee_amount = $exchanged_amount * $fee;

                        $total__ = $exchanged_amount - $fee_amount;
                    @endphp
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.net_profit') }}</h6>
                    {{-- <h3 class="my-3 @if ((float) $total_withdraw - (float) $total_withdraw > 0) text-success @elseif((float) $total_withdraw - (float) $total_withdraw < 0) text-danger @endif">{{ number_format((float) $total_withdraw - (float) $total_withdraw,2) }} ฿</h3> --}}
                    <h3 class="my-3">{{ number_format((float) $total_profit, 2) }} <span
                            style="font-size: 14px;color:rgb(145, 143, 143);">ABC</span></h3>
                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <h5 class="mb-0 text-danger">{{ number_format((float) $amount__, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">ABC</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-primary">{{ number_format((float) $total__, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">USDF</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-success">{{ number_format((float) $total__, 2) }}</h5>
                            <p class="text-muted font-size-14 mb-0">USDT</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-analyse m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/followers.png') }}" alt="" width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.new_member') }}</h6>
                    <h3 class="my-3"><span data-plugin="counterup">{{ number_format((float) $new_member, 0) }}</span>
                    </h3>
                    {{-- <span class="badge badge-soft-primary mr-1"> 0% </span> <span class="text-muted">This Month</span> --}}
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-user m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/team.png') }}" alt="" width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.all_member') }}</h6>
                    <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $total_member, 0) }}</h3>
                    {{-- <span class="badge badge-soft-primary mr-1"> +89% </span> <span class="text-muted">This Month</span> --}}
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-user m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/www.png') }}" alt="" width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.Online_today') }}</h6>
                    <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $total_online, 0) }}</h3>
                    {{-- <span class="badge badge-soft-primary mr-1"> +89% </span> <span class="text-muted">This Month</span> --}}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-dollar-circle m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/bonus.png') }}" alt="" width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">Bonus</h6>
                    <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $total_bonus, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-dollar-circle m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/mobile-banking.png') }}" alt=""
                                width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.Add_normal') }}</h6>
                    <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $manual_topup, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-dollar-circle m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/refund.png') }}" alt="" width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.Customer_Refund') }}</h6>
                    <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $manual_cashback, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate auto-height **h-100**">
                <div class="card-body">
                    <div class="avatar-sm float-right">
                        <span class="avatar-title bg-soft-primary rounded-circle">
                            {{-- <i class="bx bx-dollar-circle m-0 h3 text-primary"></i> --}}
                            <img src="{{ asset('images/dashboard_icon/bank.png') }}" alt="" width="32">
                        </span>
                    </div>
                    <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.Balance') }} <span
                            class="badge rounded-pill text-bg-primary"
                            style="font-weight: 300;background: green;color: white;">{{ __('dashboard.Bankquantity') }}
                            0xF7259643d1913878CEB3...</span></h6>
                    {{-- <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $banks->sum('balance'), 2) }}
                    </h3> --}}
                    <div class="row text-center mt-4">
                        <div class="col-4">
                            <h5 class="mb-0 text-danger">
                                {{ number_format((float) $amount__ > 0 ? (float) $amount__ : 0, 2) }}
                            </h5>
                            <p class="text-muted font-size-14 mb-0">ABC</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-primary">
                                {{ number_format((float) $total__ > 0 ? (float) $total__ : 0, 2) }}
                            </h5>
                            <p class="text-muted font-size-14 mb-0">USDF</p>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0 text-success">
                                {{ number_format((float) $total__ > 0 ? (float) $total__ : 0, 2) }}
                            </h5>
                            <p class="text-muted font-size-14 mb-0">USDT</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- @php
            $all_bank = \App\Models\Bank::where('enable', 1)->get();
        @endphp
        @foreach ($all_bank as $a_bank)
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate auto-height **h-100**">
                    <div class="card-body">
                        <div class="avatar-sm float-right">
                            <span class="avatar-title bg-soft-primary rounded-circle">
                                <img src="{{ asset('images/dashboard_icon/bank.png') }}" alt="" width="32">
                            </span>
                        </div>
                        <h6 class="text-muted text-uppercase mt-0">{{ __('dashboard.Balance') }} <span
                                class="badge rounded-pill text-bg-primary"
                                style="font-weight: 300;background: green;color: white;">{{ $a_bank->bank_name }}
                                {{ $a_bank->account_no }}</span></h6>
                        <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $a_bank->balance, 2) }}</h3>
                    </div>
                </div>
            </div>
        @endforeach --}}





    </div>
    <!-- end row -->

    <div class="row">

        {{-- <div class="col-lg-8">
         <div class="card card-animate auto-height **h-100**">
            <div class="card-body">

                <h4 class="card-title d-inline-block">Total Online</h4>
                <canvas id="line-chart" class="morris-chart"  height="94"></canvas>

                <div class="row text-center mt-4">
                    <div class="col-6">
                        <h4>{{ $total_online }}</h4>
                        <p class="text-muted mb-0">Total Online today.</p>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col --> --}}

        {{-- <div class="col-lg-12">
         <div class="card card-animate auto-height **h-100**">
            <div class="card-body">

                <h4 class="card-title d-inline-block">Top 10 Games</h4>
                <canvas id="donut-chart" class="morris-chart"  height="234"></canvas>

                <div class="row text-center mt-4">

                </div>
            </div>
        </div>
    </div> --}}
    </div>
    <!-- end row-->

    <div class="row">
        <div class="col-lg-5">
            <div class="card card-animate">
                <div class="card-body">

                    <h4 class="card-title d-inline-block mb-3">{{ __('dashboard.new_member') }}</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover mb-0">
                            <thead>
                                <tr>
                                    <th data-field="username" data-filter-control="input" data-sortable="true">
                                        {{ __('dashboard.member') }}</th>
                                    <th data-field="type" data-filter-control="select" data-sortable="true">
                                        {{ __('dashboard.name') }}</th>
                                    <th data-field="amount" data-sortable="true">{{ __('dashboard.date') }}</th>
                                    <th data-sortable="true">{{ __('dashboard.Total_Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($member_new as $item)
                                    @if (++$i == 7)
                                        @break;
                                    @endif
                                    <tr>
                                        <td>{{ $item->username }}</td>
                                        <td>{{ $item->fullname }}</td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>{{ number_format((float) $item->wallet_balance, 2) }} <i
                                                class="bx bx-bitcoin"></i></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-lg-7">
            <div class="card card-animate">
                <div class="card-body">

                    <h4 class="card-title d-inline-block">{{ __('dashboard.Latest_Deposit_Withdraw') }}</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover mb-0">
                            <thead>
                                <tr>
                                    <th data-field="username" data-filter-control="input" data-sortable="true">
                                        {{ __('dashboard.member') }}</th>
                                    <th data-field="type" data-filter-control="select" data-sortable="true">
                                        {{ __('dashboard.type') }}</th>
                                    <th data-field="amount" data-sortable="true">{{ __('dashboard.Total_Amount') }}</th>
                                    <th data-sortable="true">{{ __('dashboard.Date_of_transaction') }}</th>
                                    <th data-sortable="true">{{ __('dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfer as $item)
                                    <tr>
                                        <td>{{ $item->username }}</td>
                                        @if ($item->type == 'deposit')
                                            <td>{{ __('dashboard.deposit') }}</td>
                                        @elseif ($item->type == 'withdraw')
                                            <td>{{ __('dashboard.withdraw') }}</td>
                                        @else
                                            <td>{{ $item->type }}</td>
                                        @endif

                                        <td>{{ number_format((float) $item->amount, 2) }} <i class="bx bx-bitcoin"></i>
                                        </td>
                                        <td>{{ date('d/m/Y H:i:s', $item->transfer_date) }}</td>
                                        <td>
                                            @php
                                                $status = '';
                                                if ($item->status_code == 'อนุมัติ') {
                                                    $status = __('dashboard.approval');
                                                } elseif ($item->status_code == 'ปฏิเสธ') {
                                                    $status = __('dashboard.refuse');
                                                } elseif ($item->status_code == 'BOT.อนุมัติ') {
                                                    $status = __('dashboard.bot_approval');
                                                } elseif ($item->status_code == 'กำลังดำเนินการ') {
                                                    $status = __('dashboard.In_progress');
                                                }
                                            @endphp
                                            @if ($item->status == 1)
                                                <h5><span
                                                        class="badge badge-pill badge-warning text-bg-warning">{{ $status }}</span>
                                                </h5>
                                            @elseif ($item->status == 2)
                                                <h5><span
                                                        class="badge badge-pill badge-success text-bg-success">{{ $status }}</span>
                                                </h5>
                                            @else
                                                <h5><span
                                                        class="badge badge-pill badge-danger text-bg-danger">{{ $status }}</span>
                                                </h5>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div> <!-- end col -->

    </div>
    <form action="{{ route('dashboard_date') }}" id="form_dashboard" method="get">
        @csrf
        <input type="hidden" name="date_" id="date_">
    </form>
    <!-- end row-->
@endsection
@section('scripts')
    <link rel="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    {{-- <script>
        const backgroundColor = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ];

    const time_ = [];
    const count_ = [];
    @foreach ($players as $key => $player)
    time_.push("{{ $player[0]->hour.':00' }}");
        count_.push("{{ Count($player) }}");
    @endforeach
    let ctx = document.getElementById("line-chart").getContext("2d");
    let myChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: time_,
            datasets: [
                {
                    label: "Online players",
                    data: count_,
                    backgroundColor: "rgba(153,205,1,0.6)",
                },

            ],
        },
    });



    const game_ = [];
    const gamecount_ = [];
    @foreach ($topgame as $key => $game)
        game_.push("{!! $game->gameName !!}");
        gamecount_.push("{{ $game->count }}");
    @endforeach
    $(document).ready(function() {
        var ctxD = $("#donut-chart");
        var myLineChart = new Chart(ctxD, {
            type: 'doughnut',
            data: {
                labels: game_,
                datasets: [{
                    data: gamecount_,
                    backgroundColor: backgroundColor
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Games'
                },
                legend: {
                    display: false
                }

            }
        });
    });
</script> --}}
    <script src="{{ asset('moment/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        function formatDate(date = new Date()) {
            const year = date.toLocaleString('default', {
                year: 'numeric'
            });
            const month = date.toLocaleString('default', {
                month: 'short',
            });
            const day = date.toLocaleString('default', {
                day: '2-digit'
            });

            return [day, month, year].join(' ');
        }
        let sdate = moment()
        let edate = moment();
        @if (!is_null(request()->get('date_')))
            let pdate = "{{ request()->get('date_') }}";
            sdate = moment(pdate.split('-')[0]);
            edate = moment(pdate.split('-')[1]);
        @endif
        $('#date_picker').daterangepicker({
                ranges: {
                    "Today": [moment(), moment()],
                    "Yesterday": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    "Last 7 Days": [moment().subtract(6, 'days'), moment()],
                    "Last 30 Days": [moment().subtract(29, 'days'), moment()],
                    "This Month": [moment().startOf('month'), moment().endOf('month')],
                    "Last Month": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                    "This Year": [moment().startOf('year'), moment().endOf('year')],
                },
                startDate: sdate,
                endDate: edate
            },
            function(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
            }
        );
        $('.ranges > ul > li:last')[0].textContent = "Custom Range";

        $('#date_picker').change(function() {
            $('#date_').val($('#date_picker').val());
            $('#form_dashboard').submit();
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            function setEqualHeight(selector) {
                let elements = document.querySelectorAll(selector);
                let maxHeight = 0;

                // ค้นหาความสูงสูงสุด
                elements.forEach(el => {
                    el.style.height = 'auto'; // รีเซ็ตความสูงก่อน
                    if (el.offsetHeight > maxHeight) {
                        maxHeight = el.offsetHeight;
                    }
                });

                // กำหนดความสูงเท่ากัน
                elements.forEach(el => {
                    el.style.height = maxHeight + 'px';
                });
            }

            // เรียกใช้ฟังก์ชันเมื่อโหลดหน้า
            setEqualHeight('.auto-height');
        });
    </script>
@endsection
