@extends('layouts.admin')

@section('title', 'Live Search')

@section('raw_content')
<section class="search-hero">
    <span class="role-pill" style="margin-bottom: 14px;">
        <i class="fas fa-search"></i> Global Search
    </span>

    <h1 style="color: var(--text-main); margin: 0 0 10px; font-size: 2.1rem; font-weight: 800; text-align: center;">
        Pencarian Data Akademik
    </h1>
    <p style="color: var(--text-muted); margin: 0 0 28px; text-align: center;">
        Cari cepat data siswa, guru, mata pelajaran, kelas, dan berita sekolah.
    </p>

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

    <p style="color: var(--text-muted); margin-top: 24px; font-size: 0.9rem;">
        <i class="fas fa-info-circle" style="color: var(--primary);"></i> Hasil akan muncul otomatis saat Anda mengetik.
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
            icon.className = 'fas fa-search';
            icon.style.fontSize = '1.8rem';
            icon.style.marginBottom = '10px';
            icon.style.color = 'var(--primary)';
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
            iconBox.style.color = item.color || '#15803d';
            iconBox.style.border = '1px solid ' + (item.color || '#15803d');
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
            cat.style.color = item.color || '#15803d';
            cat.style.border = '1px solid ' + (item.color || '#15803d');
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
