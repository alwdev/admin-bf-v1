@extends('layouts.guest')

@section('content')
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">{{__('main.edit')}} Link & # Hashtags</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>{{__('main.edit')}} Link & Hashtags</h5>

        <form action="{{ route('links.update', $link->id) }}" method="POST">
            @csrf
            @method('PUT')  <!-- ใช้ @method('PUT') เพื่อบอกว่าฟอร์มนี้เป็นการแก้ไข -->

            <div class="form-group">
                <label for="link">Link</label>
                <input type="text" name="link" class="form-control" value="{{ old('link', $link->link) }}" placeholder="link" required>
                @error('link')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

        <div class="form-group">
    <label for="hashtags">Hashtags</label>
    <input type="text" name="hashtags" class="form-control" value="{{ old('hashtags', $link->hashtag) }}" placeholder=" # Hashtag ({{__('main.Separated by commas')}})" required>
    @error('hashtags')
        <div class="alert alert-danger mt-2">{{ $message }}</div>
    @enderror
</div>

            <button type="submit" class="btn btn-success mt-3">{{__('main.save')}}</button>
            <a href="{{ route('links.index') }}" class="btn btn-secondary mt-3 ml-3">{{__('main.cancel')}}</a>
        </form>
    </div>
</div>
@endsection
