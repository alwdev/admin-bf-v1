<!-- resources/views/links/create.blade.php -->

@extends('layouts.guest')

@section('content')
   <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">เพิ่ม Link และ # Hashtags</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body"> 
        <h5>เพิ่ม Link และ Hashtags ใหม่</h5>

        <form action="{{ route('links.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="link">Link</label>
                <input type="text" name="link" class="form-control" value="{{ old('link') }}" placeholder="ใส่ลิงก์" required>
                @error('link')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="hashtags">Hashtags (ใส่หลายๆ # แยกด้วยเครื่องหมายคอมมา , )</label>
                <input type="text" name="hashtags[]" class="form-control" value="{{ old('hashtags.0') }}" placeholder="ใส่ # Hashtags">
                @error('hashtags')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success mt-3">บันทึก</button>
            <a href="{{ route('links.index') }}" class="btn btn-secondary mt-3 ml-3">ยกเลิก</a>
        </form>
    </div>
</div>
@endsection
