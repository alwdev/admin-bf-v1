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
                <h4 class="mb-0 font-size-18">Chat</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
                        <li class="breadcrumb-item active">Chat</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12 card">
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <h4 class="card-title"></h4>
                <p class="card-subtitle mb-4">
                </p>

                <table id="basic-datatable" class="table nowrap table-striped" data-filter-control="true"
                    data-toggle="table" data-search="true" data-show-export="false" data-click-to-select="false"
                    data-pagination="true" data-url="">
                    <thead class="table-light">
                        <tr>
                            <th data-field="id" data-sortable="true">chat id</th>
                            <th data-field="created_at" data-sortable="true">TIME</th>
                            <th data-field="member" data-sortable="true" data-filter-control="input">member</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($convs as $item)
                            <tr>
                                <td>หมายเลขห้อง #{{ $item->id }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <a href="{{ route('chat.show', $item->id) }}">
                                        @if ($item->members->count() > 0)
                                            {{ $item->members[0]->username }}
                                        @else
                                            No members
                                        @endif
                                    </a>
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
    <script
        src="https://unpkg.com/bootstrap-table@1.21.2/dist/extensions/filter-control/bootstrap-table-filter-control.min.js">
    </script>

@endsection
