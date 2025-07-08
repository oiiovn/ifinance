<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Báo cáo Scam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f7f7f7;
        }

        .scam-box {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 10px #eee;
            padding: 32px 28px 24px 28px;
        }

        .scam-lock  {
            font-size: 48px;
            color:rgb(0, 0, 0);
            text-align: center;
        }
        .alert-success {
            font-size: 20px;
            color: #155724;
            border-color: #c3e6cb;
            padding: 10px;
            border-radius: 8px;
        }

        .scam-title {
            text-align: center;
            color: #0099ff;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 28px;
            margin-top: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .section-label {
            color: #0099ff;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 32px;
            margin-bottom: 12px;
            font-size: 18px;
        }

        .form-label {
            font-weight: 500;
        }

        .form-control,
        .form-select {
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e3e6ef;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0099ff;
            box-shadow: 0 0 0 2px #0099ff22;
        }

        .scam-upload-box {
            background: #f8f9fa;
            border: 1.5px dashed #bbb;
            border-radius: 8px;
            padding: 18px 14px;
            text-align: center;
            margin-bottom: 10px;
            color: #333;
            font-size: 15px;
        }

        .scam-upload-box input[type="file"] {
            display: none;
        }

        .scam-upload-label {
            cursor: pointer;
            color: #0099ff;
            font-weight: 500;
            display: inline-block;
        }

        .scam-note {
            color: #ff9800;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .scam-warning {
            color: #e53935;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .scam-radio {
            margin-right: 8px;
        }

        .btn-scam {
            background: #ff2d2d;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            border-radius: 8px;
            padding: 10px 0;
            margin-top: 18px;
            width: 100%;
            border: none;
        }

        .btn-scam:hover {
            background: #e60000;
            color: #fff;
        }

        .required {
            color: #ff2d2d;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 8px;
            height: 38px;
            padding-top: 3px;
        }
    </style>
</head>

<body>
    <div class="scam-box">
        <div class="scam-lock mb-2">
            <i class="fa fa-lock"></i>
            @if(session('success'))
            <div class="alert alert-success mt-3 small">
                {{ session('success') }}
            </div>
            @endif
        </div>
        <div class="scam-title d-flex justify-content-between align-items-center">
            THÔNG TIN KẺ LỪA ĐẢO
            <a href="{{ url('/') }}" class="btn btn-primary btn-sm ms-2">
                <i class="fa fa-search me-1"></i> Check scam
            </a>
        </div>
        <form method="POST" action="{{ route('scam.report.submit') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tên chủ tài khoản <span class="required">*</span></label>
                    <input type="text" name="scammer_name" class="form-control" required placeholder="Chủ tài khoản nhận tiền">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Số tài khoản <span class="required">*</span></label>
                    <input type="text" name="scammer_account" class="form-control" required placeholder="Số tài khoản nhận tiền">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngân hàng <span class="required">*</span></label>
                    <select name="bank" class="form-select" id="bank-select" required>
                        <option value="">Ngân hàng</option>
                        <option value="Vietcombank">Vietcombank</option>
                        <option value="VietinBank">VietinBank</option>
                        <option value="BIDV">BIDV</option>
                        <option value="Techcombank">Techcombank</option>
                        <option value="MB Bank">MB Bank</option>
                        <option value="TPBank">TPBank</option>
                        <option value="ACB">ACB</option>
                        <option value="Sacombank">Sacombank</option>
                        <option value="VPBank">VPBank</option>
                        <option value="SHB">SHB</option>
                        <option value="HDBank">HDBank</option>
                        <option value="VIB">VIB</option>
                        <option value="OCB">OCB</option>
                        <option value="Eximbank">Eximbank</option>
                        <option value="SeABank">SeABank</option>
                        <option value="MSB">MSB</option>
                        <option value="LienVietPostBank">LienVietPostBank</option>
                        <option value="SCB">SCB</option>
                        <option value="PVcomBank">PVcomBank</option>
                        <option value="ABBANK">ABBANK</option>
                        <option value="Saigonbank">Saigonbank</option>
                        <option value="NamABank">NamABank</option>
                        <option value="BacABank">BacABank</option>
                        <option value="VietBank">VietBank</option>
                        <option value="PG Bank">PG Bank</option>
                        <option value="OceanBank">OceanBank</option>
                        <option value="NCB">NCB</option>
                        <option value="Kienlongbank">Kienlongbank</option>
                        <option value="CBBank">CBBank</option>
                        <option value="BaoVietBank">BaoVietBank</option>
                        <option value="GPBank">GPBank</option>
                        <option value="Other">Khác</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link Facebook (nếu có)</label>
                    <input type="text" name="scammer_facebook" class="form-control" placeholder="Link facebook kẻ lừa đảo">
                </div>
            </div>
            <div class="scam-upload-box mt-3 mb-2">
                <label class="scam-upload-label" for="scam-images">
                    <i class="fa fa-plus"></i> Upload bill chuyển tiền, ảnh chụp đoạn chat, v.v....
                </label>
                <input type="file" id="scam-images" name="images[]" multiple accept="image/*">
                <div id="selected-files" style="font-size: 14px; margin-top: 8px; color: #333;"></div>
            </div>
            <div class="scam-note">
                <i class="fa fa-exclamation-triangle"></i> Mẹo: Tải lên từng ảnh một sẽ nhanh hơn.
            </div>
            <div class="scam-warning">
                <i class="fa fa-exclamation-triangle"></i> Đơn tố cáo chỉ được duyệt khi có bằng chứng rõ ràng và sẽ bị gỡ nếu Zalo liên hệ không tìm kiếm được.
            </div>
            <div class="mb-3 mt-2">
                <label class="form-label">Nội dung tố cáo <span class="required">*</span></label>
                <textarea name="content" class="form-control" rows="3" required placeholder="Mô tả chi tiết sự việc bạn bị lừa đảo"></textarea>
            </div>
            <div class="section-label">Người xác thực</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Họ và tên <span class="required">*</span></label>
                    <input type="text" name="reporter" class="form-control" required placeholder="Nhập họ, tên của bạn">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Zalo liên hệ <span class="required">*</span></label>
                    <input type="text" name="reporter_zalo" class="form-control" required placeholder="Zalo liên hệ của bạn (mở tìm kiếm)">
                </div>
            </div>
            <div class="form-check mt-3">
                <input class="form-check-input scam-radio" type="radio" name="confirm_type" id="confirm1" value="group" required>
                <label class="form-check-label" for="confirm1">
                    Phốt này nay trên group tôi chỉ đăng hộ
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input scam-radio" type="radio" name="confirm_type" id="confirm2" value="victim">
                <label class="form-check-label" for="confirm2">
                    Tôi chính là nạn nhân, tôi đồng ý và sẵn sàng chịu trách nhiệm trước pháp luật về nội dung tố cáo này.
                </label>
            </div>
            <button type="submit" class="btn btn-scam">Gửi Duyệt</button>
        </form>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#bank-select').select2({
                width: '100%',
                placeholder: 'Ngân hàng',
                allowClear: true
            });
            // Custom file input click
            document.querySelector('.scam-upload-label').onclick = function(e) {
                e.preventDefault();
                document.getElementById('scam-images').click();
            };
            // Hiển thị tên file đã chọn
            document.getElementById('scam-images').addEventListener('change', function() {
                const files = Array.from(this.files);
                const label = document.querySelector('.scam-upload-label');
                const fileList = document.getElementById('selected-files');

                if (files.length) {
                    const names = files.map(f => f.name).join(', ');
                    label.innerHTML = '<i class="fa fa-check-circle text-success"></i> Đã chọn ' + files.length + ' file';
                    label.style.color = '#28a745';
                    fileList.innerHTML = '<strong>Danh sách file:</strong><br>' + files.map(f => `- ${f.name}`).join('<br>');
                } else {
                    label.innerHTML = '<i class="fa fa-plus"></i> Upload bill chuyển tiền, ảnh chụp đoạn chat, v.v....';
                    label.style.color = '#0099ff';
                    fileList.innerHTML = '';
                }
            });
        });
    </script>
</body>

</html>