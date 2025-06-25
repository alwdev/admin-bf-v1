@extends('layouts.guest')

@section('content')
   <!-- start page title -->
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">{{__('managemember.manage')}} Links & # Hashtags</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="card">
    <div class="card-body">
        <h5>Links & # Hashtags</h5>

        <a href="{{ route('links.create') }}" class="btn btn-primary mb-3">{{__('main.Add')}} Link & # Hashtag</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Link</th>
                    <th>Hashtags</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($links as $link)
                    <tr>
                        <td>{{ $link->id }}</td>
                        <td><a href="{{ $link->link }}" target="_blank">{{ $link->link }}</a></td>
                        <td>
                            {{ $link->hashtag }}
                        </td>
                        <td>
                            <a href="{{ route('links.edit', $link->id) }}" class="btn btn-warning btn-sm">{{__('main.edit')}}</a>
                            <form action="{{ route('links.destroy', $link->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{__('managemember.delete')}}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
