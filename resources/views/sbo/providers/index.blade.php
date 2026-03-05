@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">SBO Providers</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">/</a></li>
                    <li class="breadcrumb-item active">SBO Providers</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3 align-items-end">
                <h4 class="card-title mb-0">รายการ Provider</h4>
                <form class="form-inline" method="GET" action="{{ route('sbo.providers.index') }}">
                    <div class="form-group mr-2">
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="ชื่อ Provider">
                    </div>
                    <div class="form-group mr-2">
                        <input type="text" name="type" value="{{ request('type') }}" class="form-control" placeholder="ประเภท">
                    </div>
                    <div class="form-group mr-2">
                        <select name="active" class="form-control">
                            <option value="all" {{ request('active','all')=='all'?'selected':'' }}>ทุกสถานะ</option>
                            <option value="1" {{ request('active')==='1'?'selected':'' }}>Enable</option>
                            <option value="0" {{ request('active')==='0'?'selected':'' }}>Disable</option>
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="per_page" class="form-control">
                            @foreach([50,100,150,200] as $pp)
                                <option value="{{ $pp }}" {{ (int)request('per_page', $perPage ?? 50)===$pp?'selected':'' }}>{{ $pp }}/หน้า</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-secondary mr-2">ค้นหา</button>
                    <a href="{{ route('sbo.providers.index') }}" class="btn btn-light mr-2">ล้าง</a>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal"><i class="bx bx-plus"></i> เพิ่ม Provider</button>
                </form>
            </div>

            <table class="table m-10 table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>รูป</th>
                        <th>ชื่อ</th>
                        <th>ประเภท</th>
                        <th>Lobby Game ID</th>
                        <th>สถานะ</th>
                        <th style="width:120px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($providers as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->img ?: asset('images/logo.png') }}" id="providerImage{{ $item->id }}" onclick="chooseProviderImage({{ $item->id }})" style="max-height: 80px; cursor: pointer;">
                            </td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->lobby_game_id }}</td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="providerActive{{ $item->id }}" onchange="toggleProvider({{ $item->id }}, this.checked)" {{ $item->active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="providerActive{{ $item->id }}">{{ $item->active ? 'Enable' : 'Disable' }}</label>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="openProviderEdit({{ $item->id }})"><i class="bx bx-edit-alt"></i></button>
                                <form action="{{ route('sbo.providers.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="bx bx-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $providers->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">เพิ่ม SBO Provider</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('sbo.providers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>ชื่อ</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>ประเภท</label>
                        <input type="text" class="form-control" name="type">
                    </div>
                    <div class="form-group">
                        <label>Lobby Game ID</label>
                        <input type="number" class="form-control" name="lobby_game_id">
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control" name="img" accept="image/*">
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="createProviderActive" name="active" checked>
                        <label class="custom-control-label" for="createProviderActive">Active</label>
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

<div class="modal fade" id="editProviderModal" tabindex="-1" aria-labelledby="editProviderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProviderModalLabel">แก้ไข SBO Provider</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editProviderForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>ชื่อ</label>
                        <input type="text" class="form-control" name="name" id="edit_provider_name" required>
                    </div>
                    <div class="form-group">
                        <label>ประเภท</label>
                        <input type="text" class="form-control" name="type" id="edit_provider_type">
                    </div>
                    <div class="form-group">
                        <label>Lobby Game ID</label>
                        <input type="number" class="form-control" name="lobby_game_id" id="edit_lobby_game_id">
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control" name="img" accept="image/*">
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="edit_provider_active" name="active">
                        <label class="custom-control-label" for="edit_provider_active">Active</label>
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

<form action="#" method="POST" id="provider-img-upload" enctype="multipart/form-data">
    @csrf
    <input type="file" id="provider_imgupload" name="imgupload" class="imgupload" style="display:none" />
    <input type="hidden" id="upload_provider_id" name="id" value="" />
    </form>
@endsection

@section('scripts')
<script>
function toggleProvider(id, checked) {
    $.post('{{ route('sbo.providers.toggle') }}', {
        id: id, checked: checked ? 1 : 0, _token: '{{ csrf_token() }}'
    }).done(function(){
        $('#providerActive'+id).next('label').text(checked ? 'Enable' : 'Disable');
    }).fail(function(){
        $('#providerActive'+id).prop('checked', !checked);
    });
}
function chooseProviderImage(id) {
    $('#upload_provider_id').val(id);
    $('#provider_imgupload').click();
}
$('#provider_imgupload').on('change', function () {
    const formData = new FormData($('#provider-img-upload')[0]);
    $.ajax({
        url: '{{ route('sbo.providers.upload') }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            const id = $('#upload_provider_id').val();
            $('#providerImage' + id).attr('src', res.img);
            $('#provider_imgupload').val('');
        }
    });
});
function openProviderEdit(id) {
    const row = $('#providerImage' + id).closest('tr');
    $('#edit_provider_name').val(row.find('td').eq(1).text().trim());
    $('#edit_provider_type').val(row.find('td').eq(2).text().trim());
    $('#edit_lobby_game_id').val(row.find('td').eq(3).text().trim());
    $('#edit_provider_active').prop('checked', row.find('input[type=checkbox]').is(':checked'));
    $('#editProviderForm').attr('action', '/sbo/providers/' + id);
    $('#editProviderModal').modal('show');
}
</script>
@endsection
