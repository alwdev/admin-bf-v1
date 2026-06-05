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
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Banner Setting</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                    <li class="breadcrumb-item">{{ __('main.setting') }}</li>
                    <li class="breadcrumb-item active">Banner Setting</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 card">
        <div class="card-body">
            <a type="button" class="btn btn-primary btn-gold waves-effect waves-light mb-3" href="{{ route('promotion_ads.create') }}">{{ __('main.Add') }}</a>

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
                        <th></th>
                        <th data-field="title" data-sortable="true">Banner</th>
                        <th data-field="updated_at">{{ __('main.Latest_update') }}</th>
                        <th>{{ __('dashboard.status') }}</th>
                        <th data-sortable="true"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $item)
                        <tr>
                            <td class="text-center">
                                <img src="{{ $item->image }}" class="img-thumbnail rounded" style="height:100px;cursor:pointer;" onclick="showImage('{{ $item->image }}')">
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $item->title }}</div>
                                @if (!empty($item->description))
                                    <small class="text-muted">{{ $item->description }}</small>
                                @endif
                            </td>
                            <td>{{ $item->updated_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if ($item->enable)
                                    <span class="badge badge-pill badge-success">{{ __('main.Publish') }}</span>
                                @else
                                    <span class="badge badge-pill badge-secondary">{{ __('main.Draft') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('promotion_ads.edit', $item->id) }}" type="button" class="btn btn-warning btn-sm waves-effect waves-light" style="width: 80px;"><i class="bx bx-edit-alt"></i> {{ __('main.edit') }}</a>
                                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="confirmDelete('#formdel{{ $item->id }}')" style="width: 80px;"><i class="bx bx-trash"></i> {{ __('managemember.delete') }}</button>
                                <form method="post" action="{{ route('promotion_ads.destroy') }}" id="formdel{{ $item->id }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
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
    @if (session('status'))
    Swal.fire({
        position: 'top-end',
        type: 'success',
        title: 'Your work has been saved',
        showConfirmButton: false,
        timer: 1500
    });
    @endif

    function confirmDelete(form) {
        Swal.fire({
            title: 'Confirm',
            text: 'Delete this banner?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ml-2 mt-2',
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $(form).submit();
            }
        });
    }

    function showImage(image) {
        Swal.fire({
            imageUrl: image,
            imageHeight: 500,
            imageAlt: 'Banner image'
        });
    }
</script>
@endsection
