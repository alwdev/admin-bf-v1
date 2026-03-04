@extends('layouts.guest')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">SBO Games</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">/</a></li>
                        <li class="breadcrumb-item active">SBO Games</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h4 class="card-title mb-0">รายการเกม</h4>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                        <i class="bx bx-plus"></i> เพิ่มเกม
                    </button>
                </div>

                <table id="basic-datatable" class="table m-10 table-bordered" data-filter-control="true" data-toggle="table"
                    data-search="true" data-show-export="false" data-click-to-select="false" data-pagination="true"
                    data-url="">
                    <thead class="table-light">
                        <tr>
                            <th>รูป</th>
                            <th data-field="provider_name" data-filter-control="select" data-sortable="true">ค่าย</th>
                            <th data-field="provider_type" data-filter-control="select" data-sortable="true">ประเภท</th>
                            <th data-field="game_name" data-filter-control="input" data-sortable="true">ชื่อเกม</th>
                            <th data-field="game_code" data-filter-control="input" data-sortable="true">โค้ด</th>
                            <th data-field="active" data-filter-control="select" data-sortable="true">สถานะ</th>
                            <th style="width:120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($games as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->img ?: asset('images/logo.png') }}"
                                        id="gameImage{{ $item->id }}" onclick="chooseImage({{ $item->id }})"
                                        style="max-height: 80px; cursor: pointer;">
                                </td>
                                <td>{{ $item->provider_name }}</td>
                                <td>{{ $item->provider_type }}</td>
                                <td>{{ $item->game_name }}</td>
                                <td>{{ $item->game_code }}</td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input"
                                            id="activeSwitch{{ $item->id }}"
                                            onchange="toggleActive({{ $item->id }}, this.checked)"
                                            {{ $item->active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="activeSwitch{{ $item->id }}">
                                            {{ $item->active ? 'Enable' : 'Disable' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="openEdit({{ $item->id }})">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <form action="{{ route('sbo.games.destroy', $item->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('ยืนยันการลบ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="bx bx-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">เพิ่มเกม SBO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('sbo.games.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Provider</label>
                            <input type="text" class="form-control" name="provider_name" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" class="form-control" name="provider_type">
                        </div>
                        <div class="form-group">
                            <label>Game Name</label>
                            <input type="text" class="form-control" name="game_name" required>
                        </div>
                        <div class="form-group">
                            <label>Game Code</label>
                            <input type="text" class="form-control" name="game_code">
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" class="form-control" name="img" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Payload (option)</label>
                            <textarea class="form-control" name="payload" rows="3"></textarea>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="createActive" name="active"
                                checked>
                            <label class="custom-control-label" for="createActive">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">แก้ไขเกม SBO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Provider</label>
                            <input type="text" class="form-control" name="provider_name" id="edit_provider_name"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" class="form-control" name="provider_type" id="edit_provider_type">
                        </div>
                        <div class="form-group">
                            <label>Game Name</label>
                            <input type="text" class="form-control" name="game_name" id="edit_game_name" required>
                        </div>
                        <div class="form-group">
                            <label>Game Code</label>
                            <input type="text" class="form-control" name="game_code" id="edit_game_code">
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" class="form-control" name="img" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Payload (option)</label>
                            <textarea class="form-control" name="payload" id="edit_payload" rows="3"></textarea>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_active" name="active">
                            <label class="custom-control-label" for="edit_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="#" method="POST" id="img-upload" enctype="multipart/form-data">
        @csrf
        <input type="file" id="imgupload" name="imgupload" class="imgupload" style="display:none" />
        <input type="hidden" id="upload_game_id" name="id" value="" />
    </form>
@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}">
    <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.js"></script>
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
    <script>
        function toggleActive(id, checked) {
            $.post('{{ route('sbo.games.toggle') }}', {
                id: id,
                checked: checked ? 1 : 0,
                _token: '{{ csrf_token() }}'
            });
        }

        function chooseImage(id) {
            $('#upload_game_id').val(id);
            $('#imgupload').click();
        }
        $('#imgupload').on('change', function() {
            const formData = new FormData($('#img-upload')[0]);
            $.ajax({
                url: '{{ route('sbo.games.upload') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    const id = $('#upload_game_id').val();
                    $('#gameImage' + id).attr('src', res.img);
                }
            });
        });

        function openEdit(id) {
            const row = $('#gameImage' + id).closest('tr');
            $('#edit_provider_name').val(row.find('td').eq(1).text().trim());
            $('#edit_provider_type').val(row.find('td').eq(2).text().trim());
            $('#edit_game_name').val(row.find('td').eq(3).text().trim());
            $('#edit_game_code').val(row.find('td').eq(4).text().trim());
            $('#edit_active').prop('checked', row.find('input[type=checkbox]').is(':checked'));
            $('#editForm').attr('action', '/sbo/games/' + id);
            $('#editModal').modal('show');
        }
    </script>
@endsection
