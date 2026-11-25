@extends('layouts.admin')

@section('title', 'Pencarian Data')

@section('content')
<div class="page-head">
    <div>
        <h1>Pencarian Global</h1>
        <p>Telusuri data siswa, guru, mata pelajaran, kelas, dan berita secara langsung.</p>
    </div>
</div>

<section class="panel">
    <div class="panel-body">
        <div class="search-box-wrap">
            <i class="fas fa-search search-box-icon"></i>
            <input type="text" id="liveSearchInput" class="search-box-input"
                   value="{{ $query ?? '' }}"
                   placeholder="Ketik nama siswa, NISN, guru, mapel, kelas, atau judul berita..."
                   autocomplete="off" autofocus>
        </div>

        <div id="resultsBox" class="search-results-list">
            <div id="loading" style="display: none; padding: 20px; text-align: center; color: var(--brand);">
                <i class="fas fa-circle-notch fa-spin"></i> Mencari data...
            </div>
            <div id="resultContent"></div>
        </div>
    </div>
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
        timeout = setTimeout(() => fetchData(query), 250);
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
                errBox.style.padding = '20px';
                errBox.style.textAlign = 'center';
                errBox.style.color = 'var(--ink-3)';
                errBox.textContent = 'Terjadi kesalahan saat mencari data.';
                resultContent.replaceChildren(errBox);
            });
    }

    function renderResults(data) {
        resultContent.replaceChildren();

        if (data.length === 0) {
            const empty = document.createElement('div');
            empty.style.padding = '24px';
            empty.style.textAlign = 'center';
            empty.style.color = 'var(--ink-3)';
            empty.textContent = 'Data tidak ditemukan untuk kata kunci tersebut.';
            resultContent.appendChild(empty);
            return;
        }

        const fragment = document.createDocumentFragment();

        data.forEach(item => {
            const link = document.createElement('a');
            link.href = item.url || '#';
            link.className = 'search-result-row';

            const iconBox = document.createElement('div');
            iconBox.className = 'search-result-icon';
            iconBox.style.color = item.color || '#14532d';
            iconBox.style.border = '1px solid ' + (item.color || '#bbf7d0');
            const icon = document.createElement('i');
            icon.className = item.icon || 'fas fa-circle';
            iconBox.appendChild(icon);

            const info = document.createElement('div');
            info.className = 'search-result-info';
            const title = document.createElement('strong');
            title.textContent = item.title || '';
            const desc = document.createElement('small');
            desc.textContent = item.desc || '';
            info.appendChild(title);
            info.appendChild(desc);

            const cat = document.createElement('span');
            cat.className = 'search-result-tag';
            cat.style.color = item.color || '#14532d';
            cat.style.border = '1px solid ' + (item.color || '#bbf7d0');
            cat.textContent = item.category || '';

            link.appendChild(iconBox);
            link.appendChild(info);
            link.appendChild(cat);
            fragment.appendChild(link);
        });

        resultContent.appendChild(fragment);
    }

    if (input.value.trim() !== '') {
        triggerSearch(input.value);
    }
</script>
@endpush
