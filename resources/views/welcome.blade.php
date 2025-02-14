@extends('layouts.guest')
@section('styles')
<link href="{{asset('daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<style>
.icon-cdc {
	background: #037cc3;
}
.icon-int {
	background: #0330c3;
}
.icon-qr {
	background: #ed2d36;
}
.icon-int-mb {
	background: #fdc803;
}
.icon-bp {
	background: #8c00ff;
}
.icon-tmw {
	background: #fd5403;
}
.icon-wc-ap {
	background: #84c44c;
}
.icon-cp {
	background: #ff00ba;
}
.icon-xnap {
	background: #00ffc2;
}
.icon-atome {
	background: #FFC6C6;
}

.icon-size {
    width: 12px;
    height: 12px;
    border-radius: 10px;
    float: left;
    margin-right: 10px;
    margin-top: 7px;
}
</style>
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
        <input type="text" name="date_picker" class="form-control form-select" id="date_picker" style="font-size:15px;">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                    {{-- <span class="avatar-title bg-soft-primary rounded-circle">
                        <i class="bx bx-dollar m-0 h3 text-primary"></i>
                    </span> --}}
                </div>
                <h6 class="text-muted text-uppercase mt-0">ยอดฝาก</h6>
                <h3 class="my-3">{{ number_format((float) $total_deposit,2) }} ฿</h3>
                {{-- <span class="badge badge-soft-primary mr-1"> +11% </span> <span class="text-muted">From previous period</span> --}}
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                    {{-- <span class="avatar-title bg-soft-primary rounded-circle">
                        <i class="bx bx-dollar-circle m-0 h3 text-primary"></i>
                    </span> --}}
                </div>
                <h6 class="text-muted text-uppercase mt-0">ยอดถอน</h6>
                <h3 class="my-3">{{ number_format((float) $total_withdraw,2) }} ฿</h3>
                {{-- <span class="badge badge-soft-primary mr-1"> -29% </span> <span class="text-muted">This Month</span> --}}
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                    {{-- <span class="avatar-title bg-soft-primary rounded-circle">
                        <i class="bx bx-dollar-circle m-0 h3 text-primary"></i>
                    </span> --}}
                </div>
                @php
                    $total_profit =   (float) $total_deposit - (float) $total_withdraw;
                @endphp
                <h6 class="text-muted text-uppercase mt-0">กำไรสุทธิ</h6>
                {{-- <h3 class="my-3 @if((float) $total_withdraw - (float) $total_withdraw > 0) text-success @elseif((float) $total_withdraw - (float) $total_withdraw < 0) text-danger @endif">{{ number_format((float) $total_withdraw - (float) $total_withdraw,2) }} ฿</h3> --}}
                <h3 class="my-3">{{ number_format((float) $total_profit,2) }} ฿</h3>
                {{-- <span class="badge badge-soft-primary mr-1"> -29% </span> <span class="text-muted">This Month</span> --}}
            </div>
        </div>
    </div>

</div>
<!-- end row -->

<div class="row">
    @php
     $all_bank = \App\Models\Bank::where('enable',1)->get();
    @endphp
    @foreach ( $all_bank as $a_bank)

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="avatar-sm float-right">
                    {{-- <span class="avatar-title bg-soft-primary rounded-circle">
                        <i class="bx bx-dollar-circle m-0 h3 text-primary"></i>
                    </span> --}}
                </div>
                <h6 class="text-muted text-uppercase mt-0">ยอดเงินคงเหลือ  <span class="badge rounded-pill text-bg-primary" style="font-weight: 300;background: green;color: white;">{{ $a_bank->bank_name }} {{ $a_bank->account_no }}</span></h6>
                <h3 class="my-3" data-plugin="counterup">{{ number_format((float) $a_bank->balance,2) }}</h3>
            </div>
        </div>
    </div>
    @endforeach

</div>
<!-- end row-->

<div class="row">
    <div class="col-lg-12">
         <div class="card card-animate"  style="min-height: 400px;">
            <div class="card-body">

                <h4 class="card-title d-inline-block mb-3">รายได้ตามช่วงเวลา</h4>

                <div class="">
                    
                </div>

            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-lg-6">
         <div class="card card-animate"  style="min-height: 400px;">
            <div class="card-body">

                <h4 class="card-title d-inline-block mb-3">ช่องทางชำระเงิน (ตามช่วงเวลา)</h4>

                <div class="">
                    <div class="row">
                        <div class="col-md-6">
                          
                        </div>
                        <div class="col-md-6">
                            <div class="row clearfix m-l-20">
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b">
                                    <div class="icon icon-size icon-cdc"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">Credit
                                            / Debit
                                            Card</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b">
                                    <div class="icon icon-size icon-int">-</div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">Installment</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b">
                                    <div class="icon icon-size icon-qr"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">QR
                                            Promptpay /
                                            Paypal</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b">
                                    <div class="icon icon-size icon-int-mb"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">Internet
                                            / Mobile
                                            Banking</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b">
                                    <div class="icon icon-size icon-bp"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">Bill
                                            Payment</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b">
                                    <div class="icon icon-size icon-tmw"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">True
                                            Money
                                            Wallet</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b">
                                    <div class="icon icon-size icon-wc-ap"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">WeChat
                                            / Ali
                                            Pay</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b" style="margin-bottom: 0px;">
                                    <div class="icon icon-size icon-cp"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">Crypto
                                            Payment</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b" style="margin-bottom: 0px;">
                                    <div class="icon icon-size icon-xnap"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">Xnap</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 txt-b" style="margin-bottom: 0px;">
                                    <div class="icon icon-size icon-atome"></div>
                                    <div class="col-in">
                                        <small class="text-muted m-t-0 text-11">Atome</small>
                                    </div>
                                </div>
                            </div>
                        </div>
  
                    </div>
                </div>

            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-lg-6">
        <div class="card card-animate"  style="min-height: 400px;">
           <div class="card-body">

               <h4 class="card-title d-inline-block mb-3">ชำระเต็มจำนวน</h4>

               <div class="">
                   
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
<script src="{{asset('moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    function formatDate(date = new Date()) {
      const year = date.toLocaleString('default', {year: 'numeric'});
      const month = date.toLocaleString('default', {
        month: 'short',
      });
      const day = date.toLocaleString('default', {day: '2-digit'});

      return [day, month, year].join(' ');
    }
    let sdate = moment()
    let edate = moment();
    @if(!is_null(request()->get('date_')))
        let pdate =  "{{request()->get('date_')}}";
        sdate = moment(pdate.split('-')[0]);
        edate = moment(pdate.split('-')[1]);
    @endif
    $('#date_picker').daterangepicker(
        {
        ranges   : {
          "Today"      : [moment(), moment()],
          "Yesterday"  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          "Last 7 Days": [moment().subtract(6, 'days'), moment()],
          "Last 30 Days" : [moment().subtract(29, 'days'), moment()],
          "This Month" : [moment().startOf('month'), moment().endOf('month')],
          "Last Month" : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          "This Year" : [moment().startOf('year'), moment().endOf('year')],
        },
        startDate: sdate,
        endDate  : edate
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    );
    $('.ranges > ul > li:last')[0].textContent = "Custom Range";

    $('#date_picker').change(function(){
        $('#date_').val($('#date_picker').val());
        $('#form_dashboard').submit();
    })
</script>
@endsection
