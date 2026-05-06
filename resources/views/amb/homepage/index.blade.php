@extends('layouts.guest')

@section('styles')
    @include('amb._list-styles')
@endsection

@section('content')
    <div class="amb-list-page">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">AMB หน้าแรก (Homepage items)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">AMB หน้าแรก</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-muted small mb-3">
            จัดเกมจาก <strong>AMB Games</strong> ลงช่อง 1–10 ต่อ section แบบเดียวกับ
            <code>sbo_homepage_items</code> (category: hot / slot / livecasino)
        </p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3" style="max-width: 320px">
            <label class="amb-filter-label" for="hp-section-category">Section หน้าแรก</label>
            <select id="hp-section-category" class="form-control" title="เลือก section">
                @foreach ($sections as $key => $label)
                    <option value="{{ $key }}" @selected($category === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:72px">ลำดับ</th>
                                <th style="width:80px">รูป</th>
                                <th>Provider</th>
                                <th>Game code</th>
                                <th>ชื่อเกม</th>
                                <th style="width: 180px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $usedIds = $items->pluck('amb_game_id')->all();
                            @endphp
                            @for ($pos = 1; $pos <= 10; $pos++)
                                @php
                                    $row = $items->get($pos);
                                @endphp
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $pos }}</td>
                                    <td>
                                        @if ($row && $row->game)
                                            @if ($row->game->img_url)
                                                <img src="{{ $row->game->img_url }}" alt=""
                                                    style="max-height:40px;max-width:64px;object-fit:cover;border-radius:6px;">
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $row?->game?->product?->product_code ?? '—' }}</td>
                                    <td>{{ $row?->game?->game_code ?? '—' }}</td>
                                    <td>{{ $row?->game?->game_name ?? '—' }}</td>
                                    <td class="text-nowrap">
                                        @if ($row)
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-hp"
                                                data-id="{{ $row->id }}"
                                                data-game-id="{{ $row->amb_game_id }}">เปลี่ยนเกม</button>
                                            <form action="{{ route('amb.homepage.destroy', $row->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('เอาเกมนี้ออกจากหน้าแรก?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-gold btn-add-hp"
                                                data-position="{{ $pos }}">กำหนดเกม</button>
                                        @endif
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">กำหนดเกม — ตำแหน่ง <span id="addModalPos"></span></h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form action="{{ route('amb.homepage.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="position" id="addPosition" value="">
                        <div class="modal-body">
                            <div class="form-group mb-0">
                                <label>เลือกเกม (AMB)</label>
                                <select name="amb_game_id" class="form-control" required>
                                    <option value="">— เลือกเกม —</option>
                                    @foreach ($games as $g)
                                        @if (!in_array($g->id, $usedIds, true))
                                            <option value="{{ $g->id }}">{{ $g->product?->product_code ?? '?' }} —
                                                {{ $g->game_name }} ({{ $g->game_code }})</option>
                                        @endif
                                    @endforeach
                                </select>
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
                        <h5 class="modal-title">เปลี่ยนเกม</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form id="editHpForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-0">
                                <label>เลือกเกม (AMB)</label>
                                <select name="amb_game_id" id="edit_game_id" class="form-control" required>
                                    @foreach ($games as $g)
                                        <option value="{{ $g->id }}">{{ $g->product?->product_code ?? '?' }} —
                                            {{ $g->game_name }} ({{ $g->game_code }})</option>
                                    @endforeach
                                </select>
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
        $('#hp-section-category').on('change', function() {
            var c = $(this).val();
            window.location.href = '{{ route('amb.homepage.index') }}' + (c ? ('?category=' + encodeURIComponent(c)) : '');
        });
        $('.btn-add-hp').on('click', function() {
            var pos = $(this).data('position');
            $('#addPosition').val(String(pos));
            $('#addModalPos').text(pos);
            $('#addModal').modal('show');
        });
        $('.btn-edit-hp').on('click', function() {
            var id = $(this).data('id');
            var gid = String($(this).data('game-id'));
            $('#edit_game_id').val(gid);
            $('#editHpForm').attr('action', '/amb/homepage-items/' + id);
            $('#editModal').modal('show');
        });
    </script>
@endsection
