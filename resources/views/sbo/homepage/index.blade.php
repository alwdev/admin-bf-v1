@extends('layouts.guest')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">Front Page Settings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">/</a></li>
                        <li class="breadcrumb-item active">Front Page Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ตั้งค่าเกมหน้าแรก</h4>
                    <p class="card-title-desc">เลือกเกมสำหรับ Hot Games, Slot และ Live Casino (อย่างละไม่เกิน 10 เกม)</p>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('sbo.homepage.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Hot Games</h5>
                                <input type="text" class="form-control mb-2" placeholder="ค้นหา..." oninput="filterList('hot', this.value)">
                                <div class="border p-2" style="max-height: 480px; overflow-y: auto;">
                                    @php
                                        $hotIds = $hotSelected->pluck('game_list_id')->toArray();
                                    @endphp
                                    <ul id="list-hot" class="list-unstyled">
                                        @foreach ($allActiveGames as $g)
                                            <li data-key="{{ strtolower($g->game_name . ' ' . $g->provider_name) }}">
                                                <label>
                                                    <input type="checkbox" name="hot[]" value="{{ $g->id }}" {{ in_array($g->id, $hotIds) ? 'checked' : '' }}>
                                                    {{ $g->game_name }} <small class="text-muted">({{ $g->provider_name }} / {{ $g->provider_type }})</small>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <small class="text-muted">เลือกได้สูงสุด 10 เกม</small>
                            </div>
                            <div class="col-md-4">
                                <h5>Slot</h5>
                                <input type="text" class="form-control mb-2" placeholder="ค้นหา..." oninput="filterList('slot', this.value)">
                                <div class="border p-2" style="max-height: 480px; overflow-y: auto;">
                                    @php
                                        $slotIds = $slotSelected->pluck('game_list_id')->toArray();
                                    @endphp
                                    <ul id="list-slot" class="list-unstyled">
                                        @foreach ($slotGames as $g)
                                            <li data-key="{{ strtolower($g->game_name . ' ' . $g->provider_name) }}">
                                                <label>
                                                    <input type="checkbox" name="slot[]" value="{{ $g->id }}" {{ in_array($g->id, $slotIds) ? 'checked' : '' }}>
                                                    {{ $g->game_name }} <small class="text-muted">({{ $g->provider_name }})</small>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <small class="text-muted">เลือกได้สูงสุด 10 เกม</small>
                            </div>
                            <div class="col-md-4">
                                <h5>Live Casino</h5>
                                <input type="text" class="form-control mb-2" placeholder="ค้นหา..." oninput="filterList('livecasino', this.value)">
                                <div class="border p-2" style="max-height: 480px; overflow-y: auto;">
                                    @php
                                        $liveIds = $liveSelected->pluck('game_list_id')->toArray();
                                    @endphp
                                    <ul id="list-livecasino" class="list-unstyled">
                                        @foreach ($liveGames as $g)
                                            <li data-key="{{ strtolower($g->game_name . ' ' . $g->provider_name) }}">
                                                <label>
                                                    <input type="checkbox" name="livecasino[]" value="{{ $g->id }}" {{ in_array($g->id, $liveIds) ? 'checked' : '' }}>
                                                    {{ $g->game_name }} <small class="text-muted">({{ $g->provider_name }})</small>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <small class="text-muted">เลือกได้สูงสุด 10 เกม</small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary" id="btn-save-homepage"><i class="bx bx-save"></i> บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function filterList(section, keyword) {
    const ul = document.getElementById('list-' + section);
    const q = (keyword || '').toLowerCase();
    Array.from(ul.children).forEach(li => {
        const key = li.getAttribute('data-key');
        li.style.display = (!q || key.includes(q)) ? '' : 'none';
    });
}

// limit selections to 10 per section
['hot','slot','livecasino'].forEach(section => {
    const ul = document.getElementById('list-' + section);
    ul.addEventListener('change', function() {
        const checked = ul.querySelectorAll('input[type=checkbox]:checked');
        if (checked.length > 10) {
            // uncheck the last toggled one
            const last = Array.from(checked).pop();
            last.checked = false;
            alert('เลือกได้ไม่เกิน 10 เกมต่อหมวด');
        }
    });
});
</script>
@endsection

