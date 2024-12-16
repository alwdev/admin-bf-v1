@extends('layouts.guest')
@section('styles')
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">โปรโมชั่น</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">เพจ</a></li>
                    <li class="breadcrumb-item active">โปรโมชั่น</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
<div class="card-header" style="background: transparent;">
    {{-- @if( json_decode(auth()->user()->permissions)->manageuser > 2  ) --}}
    <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="{{ route('promotion.create') }}">เพิ่มโปรโมชั่น</a>
    {{-- @endif --}}
</div>
<div class="row">
    <div class="col-12 card">
        <div class="card-body">
            <h4 class="card-title"></h4>
            <p class="card-subtitle mb-4">
            </p>

            <table id="basic-datatable" class="table nowrap"
            data-filter-control="true"
                data-toggle="table"
                data-search="true"
                data-show-export="false"
                data-click-to-select="false"
                data-pagination="true"
                data-url="">
                <thead>
                    <tr>
                        <th data-field="name" data-filter-control="input" data-sortable="true">โปรโมชั่น</th>
                        <th data-field="bonus" data-filter-control="input" data-sortable="true">โบนัส (%)</th>
                        <th data-field="turnover" data-filter-control="select" data-sortable="true">เทิร์นโอเวอร์ (%)</th>
                        <th data-sortable="true">วันที่สร้าง</th>
                        {{-- @if( json_decode(auth()->user()->permissions)->transfer > 2  ) --}}
                        <th data-sortable="true"></th>
                        {{-- @endif --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($promotions as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->bonus }}</td>
                            <td>{{ $item->turnover }}</td>  
                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                            {{-- @if( json_decode(auth()->user()->permissions)->transfer > 2  ) --}}
                            <td class="text-right">
                                <a href="{{ route('promotion.edit',$item->id) }}" type="button" class="btn btn-warning btn-sm waves-effect waves-light" style="width: 80px;"><i class="bx bx-edit-alt" ></i> แก้ไข</a>
                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="approveDeposit('#formdel{{ $item->id }}')"  style="width: 80px;"><i class="bx bx-trash"></i> ลบ</button>
                                <form method="post" action="{{ route('promotion.destroy') }}" id="formdel{{ $item->id }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                </form>
                            </td>
                            {{-- @endif --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>

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
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
    <script>
    @if (session('status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif
    @if (session('del_status'))

        Swal.fire({
            position: 'top-end',
            type: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })

    @endif

        function showEvidence($img){
            Swal.fire({
                imageUrl: $img,
                imageHeight: 600,
                imageAlt: "A tall image"
            });
        }

        function approveDeposit(form){
            Swal.fire({
                    title: 'แจ้งเตือน',
                    text: "ต้องการอัพเดตสถานะหรือไม่?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่',
                    cancelButtonText: 'ไม่, ยกเลิก!',
                    confirmButtonClass: 'btn btn-success mt-2',
                    cancelButtonClass: 'btn btn-danger ml-2 mt-2',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                       $(form).submit();
                    }
                });
        }
    </script>
@endsection
