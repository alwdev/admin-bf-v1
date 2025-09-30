@extends('layouts.guest')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/responsive.bootstrap4.css') }}">
    <link href="https://unpkg.com/bootstrap-table@1.21.2/dist/bootstrap-table.min.css" rel="stylesheet">

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
                <h4 class="mb-0 font-size-18">{{ __('Menu Setting') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">{{ __('Menu Setting') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row card mt-3">
        <div class="col-12">
            <div class="card-body">

                <!-- Add Menu Button -->
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                    + Add Menu
                </button>

                <table id="basic-datatable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $menu)
                            <tr>
                                <td>{{ $menu->icon }}</td>
                                <td>{{ $menu->title }}</td>
                                <td>{{ $menu->url }}</td>
                                <td>
                                    <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <!-- Edit button -->
                                    <button class="btn btn-warning editBtn" data-id="{{ $menu->id }}"
                                        data-title="{{ $menu->title }}" data-url="{{ $menu->url }}"
                                        data-icon="{{ $menu->icon }}" data-sort="{{ $menu->sort_order }}"
                                        data-active="{{ $menu->is_active }}">
                                        Edit
                                    </button>

                                    <!-- Toggle button -->
                                    <form action="{{ route('menus.toggle', $menu->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-sm {{ $menu->is_active ? 'btn-danger' : 'btn-success' }}">
                                            {{ $menu->is_active ? 'Disable' : 'Enable' }}
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

    <!-- Add Menu Modal -->
    <div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('menus.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL</label>
                            <input type="text" class="form-control" name="url">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            <input type="text" class="form-control" name="icon">
                            <small class="text-muted">Example: fa fa-home</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="0">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editMenuForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL</label>
                            <input type="text" class="form-control" id="editUrl" name="url">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon</label>
                            <input type="text" class="form-control" id="editIcon" name="icon">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="editSortOrder" name="sort_order">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="editIsActive" name="is_active">
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.js') }}"></script>

    <script>
        // Edit Modal JS
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('editBtn')) {
                const button = e.target;
                const id = button.dataset.id;
                const title = button.dataset.title;
                const url = button.dataset.url;
                const icon = button.dataset.icon;
                const sort = button.dataset.sort;
                const active = button.dataset.active === '1' || button.dataset.active === true;

                const editForm = document.getElementById('editMenuForm');
                editForm.action = `/menus/${id}`;

                document.getElementById('editTitle').value = title;
                document.getElementById('editUrl').value = url;
                document.getElementById('editIcon').value = icon;
                document.getElementById('editSortOrder').value = sort;
                document.getElementById('editIsActive').checked = active;

                const modal = new bootstrap.Modal(document.getElementById('editMenuModal'));
                modal.show();
            }
        });
    </script>
@endsection
