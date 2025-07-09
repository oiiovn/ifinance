<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tra cứu tài khoản lừa đảo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }
    </style>
</head>

<body class="">
    <div class="main-box ">
        <div class="text-center mb-4">
            <a href="https://www.facebook.com/groups/shopee.tittok.facebcok/" target="_blank" class="btn btn-primary">
                👥 Đặt Đơn Shopee - TikTok Shop
            </a>
        </div>
        <div class="text-center mb-3">
            <span class="fw-bold fs-5 text-primary">CHECK SCAM</span>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('scam.report.form') }}" class="btn btn-outline-danger">
                🚨 Tố cáo ngay
            </a>
        </div>
        <div class="text-end text-muted mb-4" style="font-size: 14px;">
            Hệ thống đã ghi nhận <strong>{{ $reportCount }}</strong> lượt tố cáo
        </div>


        {{-- Form tìm kiếm --}}
        <form method="GET" action="{{ route('scam.search') }}" class="row g-2 mb-4">
            <div class="col-8">
                <input type="text" name="account" class="form-control" placeholder="Nhập tên hoặc số tài khoản nghi ngờ..." required>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-danger w-100">Kiểm tra scam</button>
            </div>
        </form>

        {{-- Định nghĩa các hàm che tên và Zalo --}}
        @php
        if (!function_exists('maskName')) {
        function maskName($name) {
        return mb_substr($name, 0, 1) . str_repeat('*', mb_strlen($name) - 1);
        }
        }

        if (!function_exists('maskZalo')) {
        function maskZalo($zalo) {
        return preg_replace('/(\d{3})\d{3}(\d{3})/', '$1***$2', $zalo);
        }
        }
        @endphp

        {{-- Kết quả --}}
        @isset($result)
        @if($result->isEmpty())
        <div class="alert alert-success small">✅ Không phát hiện dấu hiệu lừa đảo trong Đặt Đơn Shopee - TikTok Shop.</div>
        @else
        <div class="alert alert-danger small">❌ Cảnh báo! Tài khoản có dấu hiệu scam trong Đặt Đơn Shopee - TikTok Shop:</div>
        <ul class="small">
            @foreach($result as $r)
            <li class="mb-3">
                <strong>Tên:</strong> {{ $r->scammer_name }}<br>
                <strong>Số tài khoản:</strong> {{ $r->scammer_account }}<br>
                <strong>Ngân hàng:</strong> {{ $r->bank }}<br>
                <strong>Facebook:</strong>
                <a href="{{ $r->scammer_facebook }}" target="_blank">{{ $r->scammer_facebook }}</a><br>
                <strong>Nội dung tố cáo:</strong> {{ $r->content }}<br>
                <strong>Người tố cáo:</strong> {{ maskName($r->reporter) }} (Zalo: {{ maskZalo($r->reporter_zalo) }})<br>
                <strong>Loại xác nhận:</strong> {{ $r->confirm_type }}<br>
                <strong>Trạng thái:</strong> {{ $r->status }}<br>

                
            </li>
            @endforeach
        </ul>
        @endif
        @endisset
    </div>
</body>

</html>