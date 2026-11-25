<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Export - {{ ucfirst($title) }}</title>
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f1f5f9; padding: 20px; font-family: sans-serif; }
        .preview-card {
            background: white; max-width: 1000px; margin: 0 auto;
            padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; font-size: 14px; }
        th { background-color: #1e293b; color: white; }
        tr:nth-child(even) { background-color: #f8fafc; }
        
        .action-buttons {
            display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;
            padding-top: 20px; border-top: 1px solid #e2e8f0;
        }
        .btn-confirm {
            background-color: #10b981; color: white; padding: 12px 25px; 
            text-decoration: none; border-radius: 5px; font-weight: bold;
            display: flex; align-items: center; gap: 8px;
        }
        .btn-cancel {
            background-color: #ef4444; color: white; padding: 12px 25px; 
            text-decoration: none; border-radius: 5px; font-weight: bold;
             display: flex; align-items: center; gap: 8px;
        }
    </style>
</head>
<body>

<div class="preview-card">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #1e293b;">PREVIEW EXPORT DATA {{ strtoupper($title) }}</h2>
        <p style="color: #64748b;">Pastikan data di bawah ini sudah benar sebelum di-download.</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $head)
                    <th>{{ $head }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ str_replace("'", "", $cell) }}</td> @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="action-buttons">
        <a href="{{ url()->previous() }}" class="btn-cancel">
            <i class="fas fa-times-circle"></i> Batal / Kembali
        </a>
        
        <a href="{{ route('export.download', ['type' => $type]) }}" class="btn-confirm">
            <i class="fas fa-file-csv"></i> Download CSV Sekarang
        </a>
    </div>
</div>

</body>
</html>