@extends('layouts.guest')

@section('content')
   <!-- start page title -->
   <div class="row">
      <div class="col-12">
         <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">รายงานสมาชิก Win/Lose</h4>
            <div class="page-title-right">
               <ol class="breadcrumb m-0">
                  <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                  <li class="breadcrumb-item active">รายงานสมาชิก Win/Lose</li>
               </ol>
            </div>
         </div>
      </div>
   </div>
   <!-- end page title -->

   <div class="row">
      <div class="col-12">
         <div class="card-body">
            <div class="row mb-3">
               <div class="col-2 text-right">
                    <input type="hidden" id="txt_date1" value="{{ $date_id }}">
                    <select id="date_picker" name="date_picker" class="form-control">
                        <option value="0">Today</option>
                        <option value="1">Yesterday</option>
                        <option value="2">Last 7 days</option>
                        <option value="3">Last 30 days</option>
                    </select>
                </div>
            </div>

            <div class="card">
               <div class="card-body p-0">
                  <div>
                     <table id="table" class="table m-0 table-bordered"
    data-filter-control="true"
    data-toggle="table"
    data-search="true"
    data-show-export="false"
    data-click-to-select="false"
    data-pagination="true"
    data-page-size="50"
    data-page-list="[10,25,50,100,200,All]"
>
    <thead>
        <tr class="text-center">
            <th data-formatter="runningFormatter">#</th> <!-- 👈 คอลัมน์ลำดับ -->
            <th>ชื่อผู้ใช้</th>
            <th>Turnover</th>
            <th>Valid Amount</th>
            <th>Win/Loss</th>
            <th>Commission</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        <tr>
            <td></td> <!-- ตัวเลขจะเติมโดย formatter -->
            <td>{{ $row->username }}</td>
            <td class="text-center">{{ number_format($row->turnover, 2) }}</td>
            <td class="text-center">{{ number_format($row->valid_amount, 2) }}</td>
            <td class="text-center" style="color: {{ $row->winloss < 0 ? 'red' : 'green' }}">
                {{ number_format($row->winloss, 2) }}
            </td>
            <td class="text-center">{{ number_format($row->commission, 2) }}</td>
            <td class="text-center" style="color: {{ $row->total < 0 ? 'red' : 'green' }}">
                {{ number_format($row->total, 2) }}
            </td>
        </tr>
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
   <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}">
   <link rel="stylesheet" href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}">
   <link rel="stylesheet" href="{{ asset('plugins/datatables/buttons.bootstrap4.css') }}">
   <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">

   <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>
   <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
   <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
   <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
   <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>

<script>
   let date1 = document.getElementById('txt_date1').value;

   $('#date_picker').on('change', function() {
       var idDate = this.value;
       window.location.href = "/report/member-winlose?range=" + idDate;
   });

   $(document).ready(function() {
       document.getElementById('date_picker').value = date1;
   });
</script>
<script>
function runningFormatter(value, row, index) {
    return index + 1;
}
</script>
@endsection
