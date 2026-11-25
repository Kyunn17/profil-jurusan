@extends('layouts.admin')

@section('title', 'Live Search')
@section('logo_spin', 'fa-spin" style="--fa-animation-duration: 15s;')

@push('styles')
<style>
    .search-container {
        position: relative;
        max-width: 700px;
        width: 100%;
        margin: 0 auto;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-btn-icon {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--primary);
        font-size: 1.2rem;
        cursor: pointer;
    }

    .live-search-results {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: rgba(30, 41, 59, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid var(--primary);
        border-top: none;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
        max-height: 400px;
        overflow-y: auto;
    }

    .search-item {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #334155;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
    }

    .search-item:last-child { border-bottom: none; }

    .search-item:hover {
        background: rgba(56, 189, 248, 0.1);
        padding-left: 25px;
    }

    .item-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 15px;
        background: rgba(255, 255, 255, 0.05);
    }

    .item-info h4 {
        margin: 0;
        color: #f1f5f9;
        font-size: 1rem;
    }

    .item-info small {
        color: #94a3b8;
        font-size: 0.8rem;
    }

    .item-category {
        margin-left: auto;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: bold;
        background: rgba(255, 255, 255, 0.1);
    }

    .loading-spinner {
        text-align: center;
        padding: 20px;
        color: var(--primary);
        display: none;
    }

    .no-result {
        padding: 20px;
        text-align: center;
        color: #94a3b8;
    }
</style>
@endpush

@section('raw_content')
<section class="search-hero" style="min-height: 85vh; display:flex; flex-direction:column; align-items:center; justify-content:flex-start; padding: 100px 20px 40px;">
    <h1 style="color: white; margin-bottom: 20px; text-shadow: 0 0 20px var(--primary);">
        GLOBAL DATABASE ACCESS
    </h1>

    <div class="search-container">
        <div class="search-input-wrapper">
            <input type="text" id="liveSearchInput" class="search-input-lg"
                   value="{{ $query ?? '' }}"
                   placeholder="Ketik nama siswa, guru, mapel..."
                   autocomplete="off" autofocus>

            <button type="button" class="search-btn-icon" aria-label="Cari">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div id="resultsBox" class="live-search-results">
            <div id="loading" class="loading-spinner">
                <i class="fas fa-circle-notch fa-spin fa-2x"></i>
            </div>

            <div id="resultContent"></div>
        </div>
    </div>

    <p style="color: #64748b; margin-top: 30px;">
        <i class="fas fa-info-circle"></i> Hasil akan muncul otomatis saat Anda mengetik.
    </p>
</section>
@endsection

@push('scripts')
<script>
    const input = document.getElementById('liveSearchInput');
    const resultsBox = document.getElementById('resultsBox');
    const resultContent = document.getElementById('resultContent');
    const loading = document.getElementById('loading');
    let timeout = null;

    function triggerSearch(query) {
        if (!query.trim()) {
            resultsBox.style.display = 'none';
            resultContent.replaceChildren();
            return;
        }

        resultsBox.style.display = 'block';
        loading.style.display = 'block';
        resultContent.replaceChildren();

        clearTimeout(timeout);
        timeout = setTimeout(() => fetchData(query), 300);
    }

    input.addEventListener('input', function () {
        triggerSearch(this.value);
    });

    function fetchData(query) {
        fetch("{{ route('search.live') }}?q=" + encodeURIComponent(query))
            .then(async response => {
                const data = await response.json();
                if (!response.ok && !Array.isArray(data)) {
                    throw new Error('Gagal memuat hasil pencarian');
                }
                return data;
            })
            .then(data => {
                loading.style.display = 'none';
                renderResults(Array.isArray(data) ? data : []);
            })
            .catch(() => {
                loading.style.display = 'none';
                const errBox = document.createElement('div');
                errBox.className = 'no-result';
                errBox.textContent = 'Terjadi kesalahan saat mencari data.';
                resultContent.replaceChildren(errBox);
            });
    }

    function renderResults(data) {
        resultContent.replaceChildren();

        if (data.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'no-result';
            const icon = document.createElement('i');
            icon.className = 'fas fa-ghost';
            icon.style.fontSize = '2rem';
            icon.style.marginBottom = '10px';
            empty.appendChild(icon);
            empty.appendChild(document.createElement('br'));
            empty.appendChild(document.createTextNode('Data tidak ditemukan.'));
            resultContent.appendChild(empty);
            return;
        }

        const fragment = document.createDocumentFragment();

        data.forEach(item => {
            const link = document.createElement('a');
            link.href = item.url || '#';
            link.className = 'search-item';

            const iconBox = document.createElement('div');
            iconBox.className = 'item-icon';
            iconBox.style.color = item.color || '#38bdf8';
            iconBox.style.border = '1px solid ' + (item.color || '#38bdf8');
            const icon = document.createElement('i');
            icon.className = item.icon || 'fas fa-circle';
            iconBox.appendChild(icon);

            const info = document.createElement('div');
            info.className = 'item-info';
            const title = document.createElement('h4');
            title.textContent = item.title || '';
            const desc = document.createElement('small');
            desc.textContent = item.desc || '';
            info.appendChild(title);
            info.appendChild(desc);

            const cat = document.createElement('div');
            cat.className = 'item-category';
            cat.style.color = item.color || '#38bdf8';
            cat.style.border = '1px solid ' + (item.color || '#38bdf8');
            cat.textContent = item.category || '';

            link.appendChild(iconBox);
            link.appendChild(info);
            link.appendChild(cat);
            fragment.appendChild(link);
        });

        resultContent.appendChild(fragment);
    }

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
        }
    });

    if (input.value.trim() !== '') {
        triggerSearch(input.value);
    }
</script>
@endpush
