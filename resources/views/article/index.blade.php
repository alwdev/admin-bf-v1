@extends('layouts.guest')
@section('styles')
<style>
    .badge {

        padding: 10px;
        font-weight: 400;
    }
</style>
@endsection
@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">{{__('main.article')}}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item active">{{__('main.article')}}</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
<div class="card-header" style="background: transparent;">
    {{-- @if( json_decode(auth()->user()->permissions)->manageuser > 2  ) --}}

    {{-- @endif --}}
</div>
<div class="row">
    <div class="col-12 card">
        <div class="card-body">
            <a type="button" class="btn btn-primary btn-gold waves-effect waves-light" href="{{ route('article.create') }}">{{__('main.Add an article')}}</a>
            <a type="button" class="btn btn-info waves-effect waves-light" href="{{ route('links.index') }}">{{__('managemember.manage')}} # Hashtag</a>
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
    <thead class="table-light">
        <tr>
            <th data-sortable="true">Title</th>
            <th>{{__("main.Category")}}</th>
            <th>{{__('main.picture')}}</th>
            <th data-sortable="true">{{__('main.Creation Date')}}</th>
            <th data-sortable="true">{{__('dashboard.status')}}</th>
            <th>Actions</th> <!-- เพิ่มคอลัมน์สำหรับปุ่มลบ -->
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $item)
            <tr>
                <td>
                    <a href="{{ route('article.edit', $item->id) }}">{{ $item->title }}</a>
                </td>
                <td>{{ $item->category }}</td>
                <td>
                    @if(!is_null($item->image))
                        <img src="{{ asset($item->image) }}" alt="Image" style="width: 100px; height: 100px;">
                    @endif
                </td>
                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                <td>
                    @if($item->status == 1)
                        <span class="badge badge-success">{{__('main.Use')}}</span>
                    @else
                        <span class="badge badge-danger">{{__('main.Not in use')}}</span>
                    @endif
                </td>
                <td>
                    <!-- ปุ่มลบ -->
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteArticle({{ $item->id }})">{{__('managemember.delete')}}</button>
                    {{-- <form action="{{ route('article.destroy', $item->id) }}" method="POST" id="form_del{{ $item->id }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('คุณต้องการลบบทความนี้?')">ลบ</button>
                    </form> --}}
                </td>
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
                    title: 'warning',
                    text: '{{__('main.Do you want to change your status?')}}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{__('main.yes')}}',
                    cancelButtonText: '{{__('main.no')}}, !',
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



<script>
    function deleteArticle(id) {
        // SweetAlert ยืนยันการลบ
        Swal.fire({
            title: 'Are you sure?',
            text: '{{__('main.This article will be permanently deleted!')}}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{__('main.Yes, delete it!')}}',
            cancelButtonText: '{{__('main.cancel')}}',
            reverseButtons: true
        }).then((result) => {

            if (result.value) {
                // // ส่งคำขอลบ
                // alert('#form_del' + id);
                // $('#form_del' + id).submit();
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/article/' + id;

                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                var methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE'; // ใช้ method DELETE
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit(); // ส่งฟอร์มไปยัง server
            }
        });
    }
</script>

@endsection
