@extends('layouts.guest')

@section('styles')
    @include('amb._list-styles')
@endsection

@section('content')
    <div class="amb-list-page">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">AMB หมวดหมู่</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">AMB หมวดหมู่</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="amb-list-toolbar">
                <form class="form-inline" method="GET" action="{{ route('amb.categories.index') }}">
                    <input type="text" name="s" value="{{ request('s') }}" class="form-control mr-2"
                        placeholder="ค้นหา code / ชื่อ">
                    <select name="per_page" class="form-control mr-2">
                        @foreach ([25, 50, 100, 200] as $n)
                            <option value="{{ $n }}" @selected((int) $perPage === $n)>{{ $n }} / หน้า</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                    <a href="{{ route('amb.categories.index') }}" class="btn btn-light">ล้าง</a>
                </form>
                <button type="button" class="btn btn-gold waves-effect ml-auto" data-toggle="modal"
                    data-target="#createModal">เพิ่มหมวดหมู่</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Name (TH)</th>
                            <th>ลำดับ</th>
                            <th>เปิดใช้</th>
                            <th style="width: 140px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $item)
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->name_th }}</td>
                                <td>{{ $item->order_no }}</td>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input amb-cat-toggle"
                                            data-id="{{ $item->id }}" id="ac{{ $item->id }}"
                                            {{ $item->active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ac{{ $item->id }}"></label>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-cat"
                                        data-id="{{ $item->id }}" data-code="{{ $item->code }}"
                                        data-name="{{ $item->name }}" data-name_th="{{ $item->name_th }}"
                                        data-order_no="{{ $item->order_no }}"
                                        data-active="{{ $item->active ? 1 : 0 }}">แก้ไข</button>
                                    <form action="{{ route('amb.categories.destroy', $item->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('ยืนยันลบหมวดหมู่นี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $categories->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มหมวดหมู่</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('amb.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Code</label>
                            <input name="code" class="form-control" required maxlength="64">
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" class="form-control" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>Name (TH)</label>
                            <input name="name_th" class="form-control" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>ลำดับ</label>
                            <input type="number" name="order_no" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                    id="ccreateActive" checked>
                                <label class="custom-control-label" for="ccreateActive">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขหมวดหมู่</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="editCatForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Code</label>
                            <input name="code" id="ecode" class="form-control" required maxlength="64">
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" id="ename" class="form-control" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>Name (TH)</label>
                            <input name="name_th" id="ename_th" class="form-control" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>ลำดับ</label>
                            <input type="number" name="order_no" id="eorder_no" class="form-control" min="0">
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                    id="eeditActive">
                                <label class="custom-control-label" for="eeditActive">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.amb-cat-toggle').on('change', function() {
            var id = $(this).data('id');
            var active = $(this).is(':checked');
            $.post("{{ route('amb.categories.toggle') }}", {
                id: id,
                active: active ? 1 : 0
            });
        });

        $('.btn-edit-cat').on('click', function() {
            var id = $(this).data('id');
            $('#ecode').val($(this).data('code'));
            $('#ename').val($(this).data('name'));
            $('#ename_th').val($(this).data('name_th'));
            $('#eorder_no').val($(this).data('order_no'));
            $('#eeditActive').prop('checked', String($(this).data('active')) === '1');
            $('#editCatForm').attr('action', '/amb/categories/' + id);
            $('#editModal').modal('show');
        });
    </script>
@endsection
