@extends('layouts.guest')

@section('styles')
    @include('amb._list-styles')
@endsection

@section('content')
    <div class="amb-list-page">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">AMB Providers</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">AMB Providers</li>
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
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="amb-list-toolbar">
                <form class="amb-filter-panel" method="GET" action="{{ route('amb.products.index') }}">
                    <div class="amb-filter-grid">
                        <div>
                            <label class="amb-filter-label" for="f-prod-category">หมวดหมู่</label>
                            <select name="category_id" id="f-prod-category" class="form-control">
                                <option value="">ทุกหมวด</option>
                                <option value="unassigned" @selected(request('category_id') === 'unassigned')>ยังไม่กำหนดหมวด</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected((string) request('category_id') === (string) $c->id)>
                                        {{ $c->code }} — {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="amb-filter-field--search">
                            <label class="amb-filter-label" for="f-prod-s">คำค้น</label>
                            <input type="search" name="s" id="f-prod-s" value="{{ request('s') }}" class="form-control"
                                placeholder="product code / ชื่อ provider…" autocomplete="off">
                        </div>
                        <div>
                            <label class="amb-filter-label" for="f-prod-per">แสดงต่อหน้า</label>
                            <select name="per_page" id="f-prod-per" class="form-control">
                                @foreach ([25, 50, 100, 200] as $n)
                                    <option value="{{ $n }}" @selected((int) $perPage === $n)>{{ $n }} รายการ</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="amb-filter-actions">
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                        <a href="{{ route('amb.products.index') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
                    </div>
                </form>
                <button type="button" class="btn btn-gold waves-effect ml-auto" data-toggle="modal"
                    data-target="#createModal">เพิ่ม Provider</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:90px">รูป</th>
                            <th>Product code</th>
                            <th>ชื่อ</th>
                            <th>หมวด</th>
                            <th>ลำดับ</th>
                            <th>เปิดใช้</th>
                            <th style="width: 160px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $item)
                            <tr>
                                <td>
                                    @if ($item->img_url)
                                        <img src="{{ $item->img_url }}?v={{ $item->updated_at?->timestamp ?? time() }}" alt="" class="img-thumb-prod"
                                            data-id="{{ $item->id }}"
                                            style="max-height:48px;max-width:72px;cursor:pointer;object-fit:cover;"
                                            onerror="this.classList.add('d-none');document.getElementById('amb-prod-fb-{{ $item->id }}').classList.remove('d-none');">
                                        <span id="amb-prod-fb-{{ $item->id }}"
                                            class="text-muted small img-thumb-prod d-none" data-id="{{ $item->id }}"
                                            style="cursor:pointer;">อัปโหลด</span>
                                    @else
                                        <span class="text-muted small img-thumb-prod" data-id="{{ $item->id }}"
                                            style="cursor:pointer;">อัปโหลด</span>
                                    @endif
                                </td>
                                <td>{{ $item->product_code }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->category?->code }} — {{ $item->category?->name }}</td>
                                <td>{{ $item->order_no }}</td>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input amb-prod-toggle"
                                            data-id="{{ $item->id }}" id="ap{{ $item->id }}"
                                            {{ $item->active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ap{{ $item->id }}"></label>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-prod"
                                        data-id="{{ $item->id }}"
                                        data-product_code="{{ $item->product_code }}"
                                        data-product_name="{{ $item->product_name }}"
                                        data-amb_category_id="{{ $item->amb_category_id ?? '' }}"
                                        data-order_no="{{ $item->order_no }}"
                                        data-active="{{ $item->active ? 1 : 0 }}">แก้ไข</button>
                                    <form action="{{ route('amb.products.destroy', $item->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('ยืนยันลบ provider นี้?')">
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
            <div class="pagination-wrap">{{ $products->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>

    <input type="file" id="prodImgUpload" accept="image/*" class="d-none">

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่ม Provider</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('amb.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Product code</label>
                            <input name="product_code" class="form-control" required maxlength="64">
                        </div>
                        <div class="form-group">
                            <label>ชื่อ Provider</label>
                            <input name="product_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>หมวดหมู่</label>
                            <select name="amb_category_id" class="form-control" required>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>รูป (ถ้ามี)</label>
                            <input type="file" name="img" class="form-control-file" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>ลำดับ</label>
                            <input type="number" name="order_no" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                    id="pcreateActive" checked>
                                <label class="custom-control-label" for="pcreateActive">เปิดใช้งาน</label>
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
                    <h5 class="modal-title">แก้ไข Provider</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="editProdForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Product code</label>
                            <input name="product_code" id="pcode" class="form-control" required maxlength="64">
                        </div>
                        <div class="form-group">
                            <label>ชื่อ Provider</label>
                            <input name="product_name" id="pname" class="form-control" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>หมวดหมู่</label>
                            <select name="amb_category_id" id="p_amb_category_id" class="form-control" required>
                                <option value="" disabled>— เลือกหมวด —</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>เปลี่ยนรูป (ถ้าต้องการ)</label>
                            <input type="file" name="img" class="form-control-file" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>ลำดับ</label>
                            <input type="number" name="order_no" id="porder_no" class="form-control" min="0">
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                    id="peditActive">
                                <label class="custom-control-label" for="peditActive">เปิดใช้งาน</label>
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

        var uploadProdId = null;
        $('.img-thumb-prod').on('click', function(e) {
            e.preventDefault();
            uploadProdId = $(this).attr('data-id') || $(this).data('id');
            $('#prodImgUpload').trigger('click');
        });

        $('#prodImgUpload').on('change', function() {
            var input = this;
            if (!uploadProdId || !input.files || !input.files.length) {
                $(input).val('');
                uploadProdId = null;
                return;
            }
            var file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'ไฟล์ใหญ่เกินไป', text: 'ใช้รูปไม่เกิน 5MB' });
                } else {
                    alert('ไฟล์ต้องไม่เกิน 5MB');
                }
                $(input).val('');
                uploadProdId = null;
                return;
            }
            var fd = new FormData();
            fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
            fd.append('id', String(uploadProdId));
            fd.append('imgupload', file);
            $.ajax({
                url: "{{ route('amb.products.upload') }}",
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    var msg = 'อัปโหลดไม่สำเร็จ';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n') || msg;
                        }
                    } else if (xhr.status === 419) {
                        msg = 'หมดเวลาเซสชัน — รีเฟรชหน้าแล้วลองใหม่';
                    } else if (xhr.status === 413) {
                        msg = 'ไฟล์ใหญ่เกินกำหนดของเซิร์ฟเวอร์';
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'อัปโหลดรูป', text: msg });
                    } else {
                        alert(msg);
                    }
                },
                complete: function() {
                    $(input).val('');
                    uploadProdId = null;
                }
            });
        });

        $('.amb-prod-toggle').on('change', function() {
            var id = $(this).data('id');
            var active = $(this).is(':checked');
            $.post("{{ route('amb.products.toggle') }}", {
                id: id,
                active: active ? 1 : 0
            });
        });

        $('.btn-edit-prod').on('click', function() {
            var id = $(this).data('id');
            $('#pcode').val($(this).data('product_code'));
            $('#pname').val($(this).data('product_name'));
            var catId = $(this).data('amb_category_id');
            if (catId !== undefined && catId !== null && String(catId) !== '') {
                $('#p_amb_category_id').val(String(catId));
            } else {
                $('#p_amb_category_id').val('');
            }
            $('#porder_no').val($(this).data('order_no'));
            $('#peditActive').prop('checked', String($(this).data('active')) === '1');
            $('#editProdForm').attr('action', '/amb/products/' + id);
            $('#editModal').modal('show');
        });
    </script>
@endsection
