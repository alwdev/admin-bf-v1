@extends('layouts.guest')

@section('styles')
    @include('amb._list-styles')
@endsection

@section('content')
    @php
        $ambGamesListQuery = request()->only('product_id', 'category_id', 's', 'per_page', 'active');
    @endphp

    <div class="amb-list-page">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">AMB Games</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">AMB Games</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="amb-list-toolbar">
                <form class="amb-filter-panel" method="GET" action="{{ route('amb.games.index') }}">
                    <div class="amb-filter-grid">
                        <div>
                            <label class="amb-filter-label" for="f-game-product">Provider</label>
                            <select name="product_id" id="f-game-product" class="form-control">
                                <option value="">ทุก provider</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" @selected((string) request('product_id') === (string) $p->id)>
                                        {{ $p->product_code }} — {{ $p->product_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="amb-filter-label" for="f-game-category">หมวดหมู่ (เกม)</label>
                            <select name="category_id" id="f-game-category" class="form-control">
                                <option value="">ทุกหมวด</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected((string) request('category_id') === (string) $c->id)>
                                        {{ $c->code }} — {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="amb-filter-label" for="f-game-active">สถานะ</label>
                            <select name="active" id="f-game-active" class="form-control">
                                <option value="" @selected(request('active') === null || request('active') === '')>ทั้งหมด</option>
                                <option value="1" @selected((string) request('active') === '1')>เปิดใช้เท่านั้น</option>
                                <option value="0" @selected((string) request('active') === '0')>ปิดใช้เท่านั้น</option>
                            </select>
                        </div>
                        <div class="amb-filter-field--search">
                            <label class="amb-filter-label" for="f-game-s">คำค้น</label>
                            <input type="search" name="s" id="f-game-s" value="{{ request('s') }}" class="form-control"
                                placeholder="game code / ชื่อ / provider code…" autocomplete="off">
                        </div>
                        <div>
                            <label class="amb-filter-label" for="f-game-per">แสดงต่อหน้า</label>
                            <select name="per_page" id="f-game-per" class="form-control">
                                @foreach ([25, 50, 100, 200] as $n)
                                    <option value="{{ $n }}" @selected((int) $perPage === $n)>{{ $n }} รายการ</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="amb-filter-actions">
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                        <a href="{{ route('amb.games.index') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
                    </div>
                </form>
                <button type="button" class="btn btn-gold waves-effect ml-auto" data-toggle="modal"
                    data-target="#createModal">เพิ่มเกม</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:72px">รูป</th>
                            <th>Provider</th>
                            <th>หมวด</th>
                            <th>Game code</th>
                            <th>ชื่อเกม</th>
                            <th>Type</th>
                            <th>Rank</th>
                            <th>เปิดใช้</th>
                            <th style="width: 150px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($games as $item)
                            <tr>
                                <td>
                                    @if ($item->img_url)
                                        <img src="{{ $item->img_url }}" alt="" class="img-thumb-game"
                                            data-id="{{ $item->id }}"
                                            style="max-height:40px;max-width:64px;cursor:pointer;object-fit:cover;"
                                            onerror="this.classList.add('d-none');document.getElementById('amb-game-fb-{{ $item->id }}').classList.remove('d-none');">
                                        <span id="amb-game-fb-{{ $item->id }}"
                                            class="text-muted small img-thumb-game d-none" data-id="{{ $item->id }}"
                                            style="cursor:pointer;">อัปโหลด</span>
                                    @else
                                        <span class="text-muted small img-thumb-game" data-id="{{ $item->id }}"
                                            style="cursor:pointer;">อัปโหลด</span>
                                    @endif
                                </td>
                                <td>{{ $item->product?->product_code }}</td>
                                <td>{{ $item->product?->category?->code ?? $item->category?->code }}</td>
                                <td>{{ $item->game_code }}</td>
                                <td>{{ $item->game_name }}</td>
                                <td>{{ $item->game_type }}</td>
                                <td>{{ $item->rank }}</td>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input amb-game-toggle"
                                            data-id="{{ $item->id }}" id="ag{{ $item->id }}"
                                            {{ $item->active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ag{{ $item->id }}"></label>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-game"
                                        data-id="{{ $item->id }}"
                                        data-amb_product_id="{{ $item->amb_product_id }}"
                                        data-game_code="{{ e($item->game_code) }}"
                                        data-game_name="{{ e($item->game_name) }}"
                                        data-game_type="{{ e($item->game_type) }}"
                                        data-provider_code="{{ e($item->provider_code) }}"
                                        data-rank="{{ $item->rank }}"
                                        data-active="{{ $item->active ? 1 : 0 }}"
                                        data-locale-b64="{{ $item->locale ? base64_encode(json_encode($item->locale, JSON_UNESCAPED_UNICODE)) : '' }}">แก้ไข</button>
                                    @php
                                        $gq = http_build_query(
                                            array_filter(
                                                request()->only(['product_id', 'category_id', 's', 'per_page', 'active']),
                                                fn ($v) => $v !== null && $v !== '',
                                            ),
                                        );
                                    @endphp
                                    <form action="{{ route('amb.games.destroy', $item->id) }}{{ $gq !== '' ? '?' . $gq : '' }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('ยืนยันลบเกมนี้?')">
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
            <div class="pagination-wrap">{{ $games->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>

    <input type="file" id="gameImgUpload" accept="image/*" class="d-none">

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มเกม</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('amb.games.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Provider</label>
                            <select name="amb_product_id" class="form-control" required>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->product_code }} — {{ $p->product_name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">หมวดของเกมจะตาม Provider ที่เลือก</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Game code</label>
                                    <input name="game_code" class="form-control" required maxlength="128">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ชื่อเกม</label>
                                    <input name="game_name" class="form-control" required maxlength="255">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Game type</label>
                                    <input name="game_type" class="form-control" maxlength="64">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Provider code</label>
                                    <input name="provider_code" class="form-control" maxlength="64">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Rank</label>
                                    <input type="number" name="rank" class="form-control" value="0" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Locale (JSON ว่างได้)</label>
                            <textarea name="locale_json" class="form-control font-monospace" rows="3"
                                placeholder='{"th":"..."}'></textarea>
                        </div>
                        <div class="form-group">
                            <label>รูปเกม (ถ้ามี)</label>
                            <input type="file" name="img" class="form-control-file" accept="image/*">
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                    id="gcreateActive" checked>
                                <label class="custom-control-label" for="gcreateActive">เปิดใช้งาน</label>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขเกม</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="editGameForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Provider</label>
                            <select name="amb_product_id" id="g_amb_product_id" class="form-control" required>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->product_code }} — {{ $p->product_name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">หมวดจะอัปเดตตาม Provider</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Game code</label>
                                    <input name="game_code" id="g_game_code" class="form-control" required
                                        maxlength="128">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ชื่อเกม</label>
                                    <input name="game_name" id="g_game_name" class="form-control" required
                                        maxlength="255">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Game type</label>
                                    <input name="game_type" id="g_game_type" class="form-control" maxlength="64">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Provider code</label>
                                    <input name="provider_code" id="g_provider_code" class="form-control"
                                        maxlength="64">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Rank</label>
                                    <input type="number" name="rank" id="g_rank" class="form-control" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Locale (JSON)</label>
                            <textarea name="locale_json" id="g_locale_json" class="form-control font-monospace"
                                rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>เปลี่ยนรูป (ถ้าต้องการ)</label>
                            <input type="file" name="img" class="form-control-file" accept="image/*">
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                    id="geditActive">
                                <label class="custom-control-label" for="geditActive">เปิดใช้งาน</label>
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

        var uploadGameId = null;
        $('.img-thumb-game').on('click', function(e) {
            e.preventDefault();
            uploadGameId = $(this).attr('data-id') || $(this).data('id');
            $('#gameImgUpload').trigger('click');
        });

        $('#gameImgUpload').on('change', function() {
            var input = this;
            if (!uploadGameId || !input.files || !input.files.length) {
                $(input).val('');
                uploadGameId = null;
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
                uploadGameId = null;
                return;
            }
            var fd = new FormData();
            fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
            fd.append('id', String(uploadGameId));
            fd.append('imgupload', file);
            $.ajax({
                url: "{{ route('amb.games.upload') }}",
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
                    uploadGameId = null;
                }
            });
        });

        $('.amb-game-toggle').on('change', function() {
            var id = $(this).data('id');
            var active = $(this).is(':checked');
            $.post("{{ route('amb.games.toggle') }}", {
                id: id,
                active: active ? 1 : 0
            });
        });

        var qSuffix = @json($ambGamesListQuery);

        $('.btn-edit-game').on('click', function() {
            var $btn = $(this);
            var id = $btn.data('id');
            $('#g_amb_product_id').val(String($btn.data('amb_product_id')));
            $('#g_game_code').val($btn.data('game_code'));
            $('#g_game_name').val($btn.data('game_name'));
            $('#g_game_type').val($btn.data('game_type'));
            $('#g_provider_code').val($btn.data('provider_code'));
            $('#g_rank').val($btn.data('rank'));
            var b64 = $btn.attr('data-locale-b64');
            if (b64) {
                try {
                    $('#g_locale_json').val(JSON.stringify(JSON.parse(atob(b64)), null, 2));
                } catch (e) {
                    $('#g_locale_json').val('');
                }
            } else {
                $('#g_locale_json').val('');
            }
            $('#geditActive').prop('checked', String($btn.data('active')) === '1');
            var qs = $.param(qSuffix);
            $('#editGameForm').attr('action', '/amb/games/' + id + (qs ? ('?' + qs) : ''));
            $('#editModal').modal('show');
        });
    </script>
@endsection
