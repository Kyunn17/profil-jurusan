<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Export - {{ ucfirst($title) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .preview-wrapper {
            padding: 36px 20px;
        }
        .preview-card {
            background: var(--bg-surface);
            max-width: 1060px;
            margin: 0 auto;
            padding: 32px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            border-top: 4px solid var(--primary);
            box-shadow: var(--shadow-md);
        }
        .preview-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .preview-header h2 {
            margin: 0 0 6px;
            color: var(--text-main);
            font-size: 1.5rem;
            font-weight: 800;
        }
        .preview-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.92rem;
        }
        .preview-table thead {
            background: var(--primary);
            border-bottom: none;
        }
        .preview-table th {
            color: #ffffff;
        }
        .preview-table tbody tr:nth-child(even) {
            background: #f8fcfa;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
        }
    </style>
</head>
<body>

<div class="preview-wrapper">
    <div class="preview-card">
        <div class="preview-header">
            <span class="role-pill" style="margin-bottom: 12px;">
                <i class="fas fa-file-csv"></i> Export CSV
            </span>
            <h2>PREVIEW EXPORT DATA {{ strtoupper($title) }}</h2>
            <p>Pastikan data di bawah ini sudah benar sebelum di-download.</p>
        </div>

        <div class="table-responsive">
            <table class="preview-table">
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
                        <td colspan="{{ max(count($headers), 1) }}" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada data untuk diekspor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="action-buttons">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Batal / Kembali
            </a>

            <a href="{{ route('export.download', ['type' => $type]) }}" class="btn">
                <i class="fas fa-file-csv"></i> Download CSV Sekarang
            </a>
        </div>
    </div>
</div>

</body>
</html>
