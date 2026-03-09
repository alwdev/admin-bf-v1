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
                                <input type="text" class="form-control mb-2" placeholder="ค้นหาเกมเพื่อเพิ่ม..." oninput="debounceSearch('hot', this.value, 'all')">
                                <div class="border p-2 bg-light mb-2" style="max-height: 200px; overflow-y: auto;">
                                    <ul id="search-hot" class="list-unstyled mb-0">
                                        <li class="text-muted small p-2">พิมพ์เพื่อค้นหาเกม...</li>
                                    </ul>
                                </div>
                                <hr>
                                <h6>เกมที่เลือกแล้ว</h6>
                                <div class="border p-2" style="max-height: 300px; overflow-y: auto;">
                                    <ul id="selected-hot" class="list-unstyled mb-0">
                                        @foreach ($hotSelected as $item)
                                            @if($item->game)
                                            <li class="p-1 border-bottom d-flex align-items-center">
                                                <input type="checkbox" name="hot[]" value="{{ $item->game_list_id }}" checked class="mr-2">
                                                <span>{{ $item->game->game_name }} <small class="text-muted">({{ $item->game->provider_name }})</small></span>
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <small class="text-muted">เลือกได้สูงสุด 10 เกม</small>
                            </div>
                            <div class="col-md-4">
                                <h5>Slot</h5>
                                <input type="text" class="form-control mb-2" placeholder="ค้นหาเกมเพื่อเพิ่ม..." oninput="debounceSearch('slot', this.value, 'EGAMES')">
                                <div class="border p-2 bg-light mb-2" style="max-height: 200px; overflow-y: auto;">
                                    <ul id="search-slot" class="list-unstyled mb-0">
                                        <li class="text-muted small p-2">พิมพ์เพื่อค้นหาเกม...</li>
                                    </ul>
                                </div>
                                <hr>
                                <h6>เกมที่เลือกแล้ว</h6>
                                <div class="border p-2" style="max-height: 300px; overflow-y: auto;">
                                    <ul id="selected-slot" class="list-unstyled mb-0">
                                        @foreach ($slotSelected as $item)
                                            @if($item->game)
                                            <li class="p-1 border-bottom d-flex align-items-center">
                                                <input type="checkbox" name="slot[]" value="{{ $item->game_list_id }}" checked class="mr-2">
                                                <span>{{ $item->game->game_name }} <small class="text-muted">({{ $item->game->provider_name }})</small></span>
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <small class="text-muted">เลือกได้สูงสุด 10 เกม</small>
                            </div>
                            <div class="col-md-4">
                                <h5>Live Casino</h5>
                                <input type="text" class="form-control mb-2" placeholder="ค้นหาเกมเพื่อเพิ่ม..." oninput="debounceSearch('livecasino', this.value, 'LIVECASINO')">
                                <div class="border p-2 bg-light mb-2" style="max-height: 200px; overflow-y: auto;">
                                    <ul id="search-livecasino" class="list-unstyled mb-0">
                                        <li class="text-muted small p-2">พิมพ์เพื่อค้นหาเกม...</li>
                                    </ul>
                                </div>
                                <hr>
                                <h6>เกมที่เลือกแล้ว</h6>
                                <div class="border p-2" style="max-height: 300px; overflow-y: auto;">
                                    <ul id="selected-livecasino" class="list-unstyled mb-0">
                                        @foreach ($liveSelected as $item)
                                            @if($item->game)
                                            <li class="p-1 border-bottom d-flex align-items-center">
                                                <input type="checkbox" name="livecasino[]" value="{{ $item->game_list_id }}" checked class="mr-2">
                                                <span>{{ $item->game->game_name }} <small class="text-muted">({{ $item->game->provider_name }})</small></span>
                                            </li>
                                            @endif
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
let searchTimers = {};

function debounceSearch(section, keyword, type) {
    if (searchTimers[section]) clearTimeout(searchTimers[section]);
    
    if (!keyword || keyword.length < 2) {
        document.getElementById('search-' + section).innerHTML = '<li class="text-muted small p-2">พิมพ์อย่างน้อย 2 ตัวอักษรเพื่อค้นหา...</li>';
        return;
    }

    searchTimers[section] = setTimeout(() => {
        performSearch(section, keyword, type);
    }, 500);
}

function performSearch(section, keyword, type) {
    const searchList = document.getElementById('search-' + section);
    searchList.innerHTML = '<li class="text-muted small p-2">กำลังค้นหา...</li>';

    fetch(`{{ route('sbo.homepage.search') }}?q=${encodeURIComponent(keyword)}&type=${type}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                searchList.innerHTML = '<li class="text-muted small p-2">ไม่พบเกมที่ตรงกับคำค้นหา</li>';
                return;
            }

            let html = '';
            data.forEach(game => {
                const isSelected = !!document.querySelector(`#selected-${section} input[value="${game.id}"]`);
                if (!isSelected) {
                    html += `
                    <li class="p-1 border-bottom hover-bg" style="cursor:pointer" onclick="addGame('${section}', ${game.id}, '${game.game_name.replace(/'/g, "\\'")}', '${game.provider_name.replace(/'/g, "\\'")}')">
                        <i class="bx bx-plus-circle text-primary"></i> ${game.game_name} <small class="text-muted">(${game.provider_name})</small>
                    </li>`;
                }
            });
            searchList.innerHTML = html || '<li class="text-muted small p-2">เกมทั้งหมดถูกเลือกไปแล้ว</li>';
        })
        .catch(err => {
            console.error(err);
            searchList.innerHTML = '<li class="text-danger small p-2">เกิดข้อผิดพลาดในการค้นหา</li>';
        });
}

function addGame(section, id, name, provider) {
    const selectedList = document.getElementById('selected-' + section);
    const checked = selectedList.querySelectorAll('input[type=checkbox]:checked');
    
    if (checked.length >= 10) {
        alert('เลือกได้ไม่เกิน 10 เกมต่อหมวด');
        return;
    }

    if (document.querySelector(`#selected-${section} input[value="${id}"]`)) {
        return;
    }

    const li = document.createElement('li');
    li.className = 'p-1 border-bottom d-flex align-items-center';
    li.innerHTML = `
        <input type="checkbox" name="${section}[]" value="${id}" checked class="mr-2">
        <span>${name} <small class="text-muted">(${provider})</small></span>
    `;
    selectedList.appendChild(li);
    
    // Refresh search results to hide added game
    const searchInput = document.querySelector(`[oninput*="debounceSearch('${section}'"]`);
    if (searchInput.value) {
        debounceSearch(section, searchInput.value, section === 'hot' ? 'all' : (section === 'slot' ? 'EGAMES' : 'LIVECASINO'));
    }
}

// limit selections to 10 per section for already existing checkboxes
['hot','slot','livecasino'].forEach(section => {
    const ul = document.getElementById('selected-' + section);
    ul.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox' && !e.target.checked) {
            // Remove the li if unchecked
            e.target.closest('li').remove();
        }
    });
});
</script>

<style>
.hover-bg:hover {
    background-color: #f8f9fa;
}
</style>

