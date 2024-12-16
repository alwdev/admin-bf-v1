@extends('layouts.guest')
@section('styles')
<link href="{{asset('daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<style>
    .dt-button {
        margin-right:5px;
        color: #fff;
        background-color: #E3A941;
        border-color: #E3A941;
        display: inline-block;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid transparent;
        padding: .5rem 1.2rem;
        font-size: .825rem;
        line-height: 1.5;
        border-radius: .25rem;
        -webkit-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
    }
    a.paginate_button.current {
    background: #E3A941;
    color: white;
    border-radius: 25%;
}

a.paginate_button {
    padding: 10px;
    cursor: pointer;
}
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">รายงาน</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">รายงาน</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card-body">
            <h4 class="card-title"></h4>

            <div class="card">
                <div class="card-body">
                    <form action="" method="get">
                        @csrf
                    <div class="row">
                            <div class="col-12 mb-3">
                                <h6 class="mt-4">EGAMES</h6>
                                    <div class="row ">
                                        @foreach ($prduct_list->where('category','EGAMES') as $item)
                                            @if(($item->active == 1 && $item->hasslotgame ==true) || ($item->active == 0 && $item->hasslotgame2 ==true))
                                                <div class="custom-control custom-radio col-2 mb-2">
                                                    <input type="radio" id="{{ $item->product_id }}" required
                                                    @if(!is_null(request()->get('product_code')))
                                                        @if(request()->get('product_code') == $item->product_id) 
                                                            @checked(true) 
                                                        @endif 
                                                    @endif name="product_code" class="custom-control-input" value="{{ $item->product_id }}">
                                                    <label class="custom-control-label" for="{{ $item->product_id }}">{{ $item->product_name }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                        
                                    </div>
                                    <h6 class="mt-4">LIVECASINO</h6>
                                    <div class="row ">

                                        @foreach ($prduct_list->where('category','LIVECASINO')->where('active',1) as $item)
                         
                                                <div class="custom-control custom-radio col-2 mb-2">
                                                    <input type="radio" id="{{ $item->product_id }}" required
                                                    @if(!is_null(request()->get('product_code')))
                                                        @if(request()->get('product_code') == $item->product_id) 
                                                            @checked(true) 
                                                        @endif 
                                                    @endif name="product_code" class="custom-control-input" value="{{ $item->product_id }}">
                                                    <label class="custom-control-label" for="{{ $item->product_id }}">{{ $item->product_name }}</label>
                                                </div>
                                 
                                        @endforeach
                                        
                                    </div>

                                    <h6 class="mt-4">SPORT</h6>
                                    <div class="row ">

                                        @foreach ($prduct_list->where('category','SPORT')->where('active',1) as $item)
                         
                                                <div class="custom-control custom-radio col-2 mb-2">
                                                    <input type="radio" id="{{ $item->product_id }}" required
                                                    @if(!is_null(request()->get('product_code')))
                                                        @if(request()->get('product_code') == $item->product_id) 
                                                            @checked(true) 
                                                        @endif 
                                                    @endif name="product_code" class="custom-control-input" value="{{ $item->product_id }}">
                                                    <label class="custom-control-label" for="{{ $item->product_id }}">{{ $item->product_name }}</label>
                                                </div>
                                 
                                        @endforeach
                                        
                                    </div>

                            </div>
                            <div class="col-4 mb-3">
                                <input type="text" name="date_picker" class="form-control form-select" id="date_picker" style="font-size:15px;">
                            </div>
                            <div class="col-12">
                                <button type="submit"  class="btn btn-primary waves-effect waves-light">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div>
                        <table id="table" class="table m-10 table-bordered"
                        data-filter-control="true"
                        data-toggle="table"
                        data-search="true"
                        data-show-export="false"
                        data-click-to-select="false"
                        data-pagination="true"
                        data-url="">
                            <thead>
                                <tr class="text-center">
                                    <th data-field="username" data-filter-control="select" data-sortable="true">ชื่อผู้ใช้</th>
                                    
                                    @if($type == "LIVECASINO")
                                        @if(request()->get('product_code') == "SEXY")
                                            <th data-field="game" data-filter-control="select" data-sortable="true">เกมส์</th>
                                            <th>ค่ายเกมส์</th>
                                            <th data-sortable="true">เดิมพัน</th>
                                            <th data-sortable="true">ได้-เสีย</th>
                                        @elseif(request()->get('product_code') == "WM")
                                            <th data-field="game" data-filter-control="select" data-sortable="true">เกมส์</th>
                                            <th>ค่ายเกมส์</th>
                                            <th data-sortable="true">เดิมพัน</th>
                                            <th data-sortable="true">ประเภท</th>
                                            <th data-sortable="true">สถานะ</th>
                                        @else
                                            <th data-field="game" data-filter-control="select" data-sortable="true">เกมส์</th>
                                            <th>ค่ายเกมส์</th>
                                            <th data-sortable="true">เดิมพัน</th>
                                            <th data-sortable="true">สถานะ</th>
                                        @endif
                                    @elseif($type == "EGAMES")
                                        <th data-field="game" data-filter-control="select" data-sortable="true">เกมส์</th>
                                        <th data-sortable="true">ได้-เสีย</th>
                                        <th data-sortable="true">ยอดเริ่มต้น</th>
                                        <th data-sortable="true">ยอดคงเหลือ</th>
                                        <th data-field="" data-filter-control="select" data-sortable="true">ประเภท</th>
                                    @elseif($type == "SPORT")
                                        <th>ค่ายเกมส์</th>
                                        <th data-sortable="true">เดิมพัน</th>
                                        <th data-sortable="true">ประเภท</th>
                                        <th data-sortable="true">สถานะ</th>
                                    @endif

                                    <th data-sortable="true">วันที่/เวลา</th>
            
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($member_play as $member)
                                
                                @if(!is_null(request()->get('product_code')))
                                    @if($type == "LIVECASINO")
                                        @if(request()->get('product_code') == "SEXY")
                                            <tr>
                                                <td>{{ $member->userId }}</td>
                                                <td>{{ $member->gameName }}</td>
                                                <td>{{ $member->platform }}</td>
                                                <td class="text-center">{{ intval($member->betAmount) }}</td>
                                                <td class="text-center">{{ intval($member->winAmount) }}</td>
                                                <td class="text-center">{{ Carbon\Carbon::parse($member->created_at)->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                        @elseif(request()->get('product_code') == "WM")
                                        <tr>
                                            <td>{{ $member->username }}</td>
                                            <td>{{ $member->gameId }}</td>
                                            <td>WM Casino</td>
                                            <td class="text-center">{{ intval($member->amount) }}</td>
                                            <td class="text-center">{{ ($member->type) }}</td>
                                            <td class="text-center">{{ ($member->status) }}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($member->created_at)->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td>{{ $member->username }}</td>
                                            <td>{{ $member->gameCode }}</td>
                                            <td>{{ $member->productId }}</td>
                                            <td class="text-center">{{ intval($member->betAmount) }}</td>
                                            <td class="text-center">{{ ($member->status) }}</td>
                                            <td class="text-center">{{ Carbon\Carbon::parse($member->created_at)->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        @endif
                                    @elseif($type == "EGAMES")
                                    <tr>
                                        <td>{{ $member->username }}</td>
                                        <td>{{ $member->gameCode }}</td>
                                        <td class="text-center">{{ intval($member->winlose) }}</td>
                                        <td class="text-center">{{ intval($member->betAmount) }}</td>
                                        <td class="text-center">{{ intval($member->amount) }}</td>
                                        @if($is_payout_db == 1)
                                            <td class="text-center">
                                                @if ( (intval($member->betAmount) == abs(intval($member->winlose))) &&  ($member->endround == 0) && ($member->betAmount >= 50) )
                                                    Free Spin
                                                    @else
                                                    Normal
                                                @endif

                                            </td>
                                        @endif
                                        <td class="text-center">{{ Carbon\Carbon::parse($member->created_at)->format('d/m/Y H:i:s') }}</td>
                    
                                    </tr>
                                    @elseif($type == "SPORT")
                                    <tr>
                                        <td>{{ $member->username }}</td>
                                        <td>{{ $member->productId }}</td>
                                        <td class="text-center">{{ intval($member->betAmount) }}</td>
                                        <td class="text-center">{{ ($member->type) }}</td>
                                        <td class="text-center">{{ ($member->status) }}</td>
                                        <td class="text-center">{{ Carbon\Carbon::parse($member->created_at)->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    @endif
               
                                @endif

                              
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>


            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->
@endsection
@section('scripts')
    <!-- Datatables init -->
    {{-- <script src="{{ asset('pages/datatables-demo.js')}}"></script> --}}
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}">
    <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">

    {{-- <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script> --}}

    {{-- <link rel="stylesheet" type="text/css"  href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css"  href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" /> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.4.0/js/buttons.print.min.js"></script>

<script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>

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
        @if(!is_null(request()->get('date_picker')))
            let pdate =  "{{request()->get('date_picker')}}";
            sdate = moment(pdate.split('-')[0]);
            edate = moment(pdate.split('-')[1]);
        @endif
        $('#date_picker').daterangepicker(
            {
            ranges   : {
              "Today"      : [moment(), moment()],
              "Yesterday"  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              "Last 7 Days": [moment().subtract(6, 'days'), moment()],
            //   "Last 15 Days" : [moment().subtract(14, 'days'), moment()],
            //   "This Month" : [moment().startOf('month'), moment().endOf('month')],
            //   "Last Month" : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            //   "This Year" : [moment().startOf('year'), moment().endOf('year')],
            },
            startDate: sdate,
            endDate  : edate
          },
          function (start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
          }
        );
        $('.ranges > ul > li:last')[0].style.display = "none";
    
        $('#table').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel'
            ],searching: false, paging: true, info: false
        } );

    </script>
@endsection
