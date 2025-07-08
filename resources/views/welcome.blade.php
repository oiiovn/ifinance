<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tra cứu tài khoản lừa đảo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        body {
            background: #f2f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .main-box {
            max-width: 700px;
            margin: 60px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            padding: 30px;
        }
    </style>
</head>
<body>
<div class="main-box">
    <h3 class="text-center text-primary mb-4">🔍 Tra cứu tài khoản lừa đảo</h3>
    <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('scam.report.form') }}" class="btn btn-outline-danger">
        🚨 Tố cáo ngay
    </a>
</div>


    {{-- Form tìm kiếm --}}
    <form method="GET" action="{{ route('scam.search') }}" class="row g-2 mb-4">
        <div class="col-8">
            <input type="text" name="account" class="form-control" placeholder="Nhập tên hoặc số tài khoản nghi ngờ..." required>
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-danger w-100">Kiểm tra ngay</button>
        </div>
    </form>

    {{-- Kết quả --}}
    @isset($result)
        @if($result->isEmpty())
            <div class="alert alert-success small">✅ Không phát hiện dấu hiệu lừa đảo trong hệ thống.</div>
        @else
            <div class="alert alert-danger small">❌ Cảnh báo! Tài khoản có dấu hiệu scam:</div>
            <ul class="small">
                @foreach($result as $r)
                    <li>{{ $r->scammer_name }} - {{ $r->scammer_account }} ({{ $r->bank }})</li>
                @endforeach
            </ul>
        @endif
    @endisset
</div>
</body>
</html>
