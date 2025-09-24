@extends('layouts.guest')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{ __('main.member_level_setting') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">{{ __('main.member_level_setting') }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="card-header text-right" style="background: transparent;">

        </div>
        <div class="col-12 card">

            <div class="card-body">
                <button type="button" class="btn btn-primary btn-gold waves-effect waves-light" data-toggle="modal"
                    data-target="#addModal">
                    {{ __('main.Add') }}
                </button>
                <h4 class="card-title"></h4>
                <p class="card-subtitle mb-4">
                </p>

                <table id="basic-datatable" class="table m-10 table-bordered" data-filter-control="true" data-toggle="table"
                    data-search="true" data-show-export="false" data-click-to-select="false" data-pagination="true"
                    data-url="">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->message }}</td>
                                <td>{{ $item->date }}</td>
                                <td>{{ $item->time }}</td>
                                <td>
                                    @if ($item->active == 1)
                                        <button class="btn btn-sm btn-success"
                                            onclick="changeStatus('{{ $item->id }}')">Enable</button>
                                    @else
                                        <button class="btn btn-sm btn-danger"
                                            onclick="changeStatus('{{ $item->id }}')">Enable</button>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editItem(this)"
                                        data-id="{{ $item->id }}" data-date="{{ $item->date }}"
                                        data-time="{{ $item->time }}" data-message="{{ $item->message }}"
                                        data-active="{{ $item->active }}">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="confirm_destroy('{{ $item->id }}')">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div> <!-- end card body-->

        </div><!-- end col-->
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="add_form" action="{{ route('setting.alert_store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">{{ __('main.Add') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add_date">{{ __('main.Date') }}</label>
                            <input type="date" class="form-control" id="add_date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="add_time">{{ __('main.Time') }}</label>
                            <input type="time" class="form-control" id="add_time" name="time" required>
                        </div>
                        <div class="form-group">
                            <label for="add_message">{{ __('main.Message') }}</label>
                            <textarea class="form-control" id="add_message" name="message" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="add_active">{{ __('main.Active') }}</label>
                            <select class="form-control" id="add_active" name="active" required>
                                <option value="1">Enable</option>
                                <option value="0">Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('main.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('main.Add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="edit_form" action="{{ route('setting.alert_update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">{{ __('main.Edit') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_date">{{ __('main.Date') }}</label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_time">{{ __('main.Time') }}</label>
                            <input type="time" class="form-control" id="edit_time" name="time" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_message">{{ __('main.Message') }}</label>
                            <textarea class="form-control" id="edit_message" name="message" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_active">{{ __('main.Active') }}</label>
                            <select class="form-control" id="edit_active" name="active" required>
                                <option value="1">Enable</option>
                                <option value="0">Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('main.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('main.Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <form method="post" id="destroy_form" action="{{ route('setting.alert_del') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" id="destroy_id" name="id">
    </form>
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
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script>
        function confirm_destroy(id) {

            Swal.fire({
                title: "{{ __('setting.Do you want to delete this information?') }}",
                showDenyButton: true,
                showCancelButton: true,
                icon: 'question',
                confirmButtonText: "{{ __('managemember.delete') }}",
                denyButtonText: '{{ __('main.cancel') }}'
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $('#destroy_id').val(id);
                    $('#destroy_form').submit();
                } else {}
            });
            //
        }

        function submit_(form) {
            var content = $('#editor').html();
            $("<input />").attr("type", "hidden").attr("name", "note").attr("value", content).appendTo(form);
            $(form).submit();
        }

        @if (session('success'))

            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 2500
            })
        @endif

        @if (session('error'))

            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2500
            })
        @endif
    </script>
    <script>
        function editItem(button) {
            const id = $(button).data('id');
            const date = $(button).data('date');
            const time = $(button).data('time');
            const message = $(button).data('message');
            const active = $(button).data('active');

            // Populate the form fields in the edit modal
            $('#edit_id').val(id);
            $('#edit_date').val(date);
            $('#edit_time').val(time);
            $('#edit_message').val(message);
            $('#edit_active').val(active);

            // Show the edit modal
            $('#editModal').modal('show');
        }
    </script>
@endsection
