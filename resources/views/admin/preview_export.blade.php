<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Export - {{ ucfirst($title) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="workspace" style="max-width: 1100px;">
    <div class="page-head">
        <div>
            <h1>Preview Export: {{ ucfirst($title) }}</h1>
            <p>Total <strong class="mono">{{ count($rows) }}</strong> baris siap diunduh sebagai file CSV.</p>
        </div>
        <div class="page-actions">
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('export.download', ['type' => $type]) }}" class="btn btn-sm">
                <i class="fas fa-file-csv"></i> Download CSV
            </a>
        </div>
    </div>

    <section class="panel" style="margin-bottom: 0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach($headers as $head)
                            <th>{{ $head }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ str_replace("'", "", $cell) }}</td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ max(count($headers), 1) }}" class="empty-row">
                            Belum ada data untuk diekspor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

</body>
</html>
