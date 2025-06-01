@extends('layouts.app')

@section('content')

<div class="container p-3 bg-white shadow-sm rounded">


    <h2 class="m-4 text-info">Nhập liệu tài chính</h2>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    @endif
    @if($wallets->isEmpty())
    <div class="alert alert-warning">
        Bạn chưa có ví nào. <a href="{{ route('wallets.index') }}" class="btn btn-sm btn-primary">Thêm ví ngay</a>
    </div>
    @else
    {{-- Form ghi thu chi --}}
    <form action="{{ route('transactions.store') }}" method="POST" class="mb-4">
        <ul class="list-group col-3">
            @foreach($wallets as $wallet)
            <li class="list-group-item">
                <div class="d-flex align-items-center">
                    <img src="{{ asset($wallet->logo_path) }}" alt="Logo" width="80" height="20" class="me-2">
                    <span>: {{ number_format($wallet->balance) }} đ</span>
                </div>
            </li>
            @endforeach
        </ul>
        @csrf
        <input type="hidden" name="wallet_id" value="{{ $wallets->first()->id }}">
        <div class="row">
            <div class="col-md-2 pe-2	">
                <label for="type"></label>
                <select name="type" id="type" class="form-select" onchange="toggleCategory()" required>
                    <option value="chi">Chi</option>
                    <option value="thu">Thu</option>
                    <option value="nhaphang">Nhập hàng</option>
                    <option value="muontien">Mượn tiền</option>
                </select>
            </div>

            <div class="col-md-4 pe-2">

                <label for="category"></label>
                <div class="d-flex align-items-center">
                    <select name="supplier_name" id="category_nhaphang" class="form-select me-2 d-none" onchange="checkSpecialCategory(this)">
                        <option value="" disabled selected>-- Nhà cung cấp --</option>
                        @foreach($nhacungcaps as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    <select name="category_id" id="category_thu" class="form-select me-2 d-none" onchange="checkSpecialCategory(this)">
                        <option value="" disabled selected>-- Danh mục thu --</option>
                        @foreach($categories->where('type', 'thu') as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select name="category_id" id="category_chi" class="form-select me-2 d-none" onchange="checkSpecialCategory(this)">
                        <option value="" disabled selected>-- Danh mục chi --</option>
                        @foreach($categories->where('type', 'chi') as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-light btn-sm text-success" id="add-thu-btn" data-bs-toggle="modal" data-bs-target="#addCategoryModal" data-type="thu">+</button>
                    <button type="button" class="btn btn-light btn-sm text-success" id="add-chi-btn" data-bs-toggle="modal" data-bs-target="#addCategoryModal" data-type="chi">+</button>
                    <button type="button" class="btn btn-light btn-sm text-success" id="add-nhaphang-btn" data-bs-toggle="modal" data-bs-target="#addSupplierModal" data-type="nhaphang">+</button>


                </div>



            </div>
            <div id="nhacungcap-group" class="col-md-3 d-none pt-0 align-self-start">
                <label for="supplier_name" class="form-label"></label>
                <div class="d-flex align-items-start">
                    <button type="button" id="add-supplier-btn" class="btn btn-light btn-sm text-success d-none" data-bs-toggle="modal" data-bs-target="#addSupplierModal">+</button>
                </div>
            </div>

            <div class="d-flex align-items-center pe-2">
                <div id="employee-group" class="col-md-3 d-none">
                    <label for="employee"></label>
                    <div class="d-flex align-items-center">
                        <select name="employee_name" id="employee" class="form-select me-2">
                            <option value="" disabled selected>-- Chọn nhân viên --</option>
                            @foreach($employees as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-light btn-sm text-success" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">+</button>
                    </div>
                </div>
            </div>
            <div id="shop-group" class="col-md-3 d-none pt-0 align-self-start pe-2">
                <label for="shop" class="form-label"></label>
                <div class="d-flex align-items-start">
                    <select name="shop_name" id="shop" class="form-select me-2">
                        <option value="">-- Chọn shop --</option>
                        @foreach($contacts as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-light btn-sm text-success align-self-center" data-bs-toggle="modal" data-bs-target="#addShopModal">+</button>
                </div>
            </div>
            <div id="dautu-group" class="col-md-3 d-none pt-0 align-self-start">
                <label for="dautu" class="form-label"></label>
                <div class="d-flex align-items-start pe-2">
                    <select name="dautu_name" id="dautu" class="form-select me-2">
                        <option value="">-- Chọn đầu tư --</option>
                        @foreach($dautus as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-light btn-sm text-success align-self-center" data-bs-toggle="modal" data-bs-target="#addDautuModal">+</button>
                </div>
            </div>
            <div id="chovay-group" class="col-md-3 d-none pt-0 align-self-start">
                <label for="chovay_name" class="form-label"></label> <!-- Thêm label để đồng bộ -->
                <div class="d-flex align-items-start pe-2">
                    <select name="related_name" id="chovay_name" class="form-select me-2">
                        <option value="">-- Chọn người vay --</option>
                        @foreach ($chovays as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-light btn-sm text-success align-self-center"
                        id="add-chovay-btn"
                        data-bs-toggle="modal" data-bs-target="#addChovayModal" data-type="chovay">+</button>
                </div>
            </div>
            <div id="muontien-group" class="col-md-3 d-none pt-0 align-self-start">
                <label for="muontien_name" class="form-label">Người cho mượn</label>
                <div class="d-flex align-items-start pe-2">
                    <select name="muontien_name" class="form-select me-2">
                        <option value="">-- Chọn người cho mượn --</option>
                        @foreach($muontiens as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-light btn-sm text-success align-self-center"
                        data-bs-toggle="modal" data-bs-target="#addMuontienModal">+</button>
                </div>
            </div>

            <div id="nhaphang-fields" class="row d-none mt-3">
                <div class="col-md-3">
                    <label>Giá trị đơn hàng</label>
                    <input type="text" name="amount_total" class="form-control border rounded" placeholder="VD: 5.000.000">
                </div>
                <div class="col-md-3">
                    <label class="form-label ms-3">Thanh toán trước</label>
                    <input type="text" name="amount_paid" class="form-control border rounded ms-3" placeholder="VD: 3.000.000">
                </div>
            </div>

            <div class="row align-items-center">
                <div id="amount-default" class="col-md-2 py-4">
                    <input type="text" name="amount" class="form-control border rounded" placeholder="VD: 100.000">
                </div>
                <div class="col-md-2 py-4 ps-md-2">
                    <input type="date" name="transaction_date" class="form-control border rounded" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2 py-4 ps-md-2">
                    <input type="text" name="note" class="form-control border rounded" placeholder="Ghi chú (tuỳ chọn)">
                </div>
            </div>

        </div>
        <div class="m-3 col-md-1 d-flex align-items-center">
            <button type="submit" class="btn btn-success w-100">Ghi</button>
        </div>

    </form>
    @endif


    {{-- Modal thêm danh mục --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.addCategory') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="type" id="modal_type">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm danh mục <span id="type_text"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="border rounded" type="text" name="name" class="form-control" placeholder="Tên danh mục" required>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="addDautu" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <form action="{{ route('transactions.addCategory') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="type" id="modal_type">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm danh mục <span id="type_text"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="border rounded" type="text" name="name" class="form-control" placeholder="Tên danh mục" required>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.addEmployee') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="border rounded" type="text" name="employee_name" class="form-control" placeholder="Tên nhân viên" required>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="addShopModal" tabindex="-1" aria-labelledby="addShopLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.addShop') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm shop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="border rounded" type="text" name="shop_name" class="form-control" placeholder="Tên shop" required>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="addDautuModal" tabindex="-1" aria-labelledby="addDautuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.addDautu') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm khoản đầu tư</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="border rounded" type="text" name="dautu_name" class="form-control" placeholder="Tên đầu tư" required>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.addSupplier') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhà cung cấp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control border rounded" type="text" name="supplier_name" placeholder="Tên nhà cung cấp" required>
                    <button type="submit" class="btn btn-primary mt-2">Lưu</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="addChovayModal" tabindex="-1" aria-labelledby="addChovayLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.addChovay') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm người vay</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control border rounded" type="text" name="chovay_name" placeholder="Tên người vay" required>
                    <button type="submit" class="btn btn-primary mt-2">Lưu</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal thêm người cho mượn -->
    <div class="modal fade" id="addMuontienModal" tabindex="-1" aria-labelledby="addMuontienLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('transactions.addMuontien') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm người cho mượn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control border rounded" type="text" name="muontien_name" placeholder="Tên người cho mượn" required>
                    <button type="submit" class="btn btn-primary mt-2">Lưu</button>
                </div>
            </form>
        </div>
    </div>




    <hr>
    <h4 class="mb-3 mt-3">Lịch sử ghi giao dịch</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Loại</th>
                <th>Bank</th>
                <th>Danh mục</th>
                <th>Số tiền</th>
                <th>Chi tiết</th>
                <th>Ghi chú</th>
                <th>Ngày</th>
                <th>Ngày giao dịch</th> <!-- ✅ Thêm cột mới -->
            </tr>
        </thead>

        <tbody>
            @forelse($transactions as $tran)
            <tr>
                <td>
                    <span class="badge bg-{{ 
        $tran->type == 'thu' ? 'success' : 
        ($tran->type == 'chi' ? 'danger' : 
        ($tran->type == 'nhaphang' ? 'info' : 
        ($tran->type == 'congno' ? 'warning' : 'secondary')))
    }}">
                        {{ strtoupper($tran->type) }}
                    </span>
                </td>
                <td>{{ $tran->wallet->name ?? '---' }}</td> <!-- 👈 Hiển thị tên ví -->
                <td>{{ $tran->category->name ?? '    hàng' }}</td>
                <td>{{ number_format($tran->amount) }} đ</td>
                <td>{{ $tran->related_name }}</td>
                <td>
                    @if($tran->type === 'congno' && Str::startsWith($tran->note, 'Công nợ:'))
                    <small class="text-muted">
                        ({{ $tran->noteStripped ?? str_replace('Công nợ: ', '', $tran->note) }} = nợ lại {{ number_format(abs($tran->amount)) }} đ)
                    </small>
                    @else
                    {{ $tran->note }}
                    @endif
                </td>
                <td>{{ $tran->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($tran->transaction_date)->format('d/m/Y') }}</td> <!-- ✅ Thêm dòng này -->
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Chưa có giao dịch</td>
            </tr>
            @endforelse
        </tbody>

    </table>
</div>

<script>
    function toggleCategory() {
        const type = document.getElementById('type').value;

        const thuSelect = document.getElementById('category_thu');
        const chiSelect = document.getElementById('category_chi');
        const nhaphangSelect = document.getElementById('category_nhaphang');
        const nhacungcapGroup = document.getElementById('nhacungcap-group');
        const addSupplierBtn = document.getElementById('add-supplier-btn');
        const addChovayBtn = document.getElementById('add-chovay-btn');
        const chovayGroup = document.getElementById('chovay-group');
        const addThuBtn = document.getElementById('add-thu-btn');
        const addChiBtn = document.getElementById('add-chi-btn');
        const addNhaphangBtn = document.getElementById('add-nhaphang-btn');
        const nhaphangFields = document.getElementById('nhaphang-fields');
        const amountDefault = document.getElementById('amount-default');
        const muontienGroup = document.getElementById('muontien-group'); // 👈 thêm dòng này

        // Ẩn tất cả trước
        thuSelect.classList.add('d-none');
        chiSelect.classList.add('d-none');
        nhaphangSelect.classList.add('d-none');
        addThuBtn.classList.add('d-none');
        addChiBtn.classList.add('d-none');
        addNhaphangBtn.classList.add('d-none');
        addSupplierBtn.classList.add('d-none');
        amountDefault.classList.add('d-none');
        chovayGroup.classList.add('d-none');
        addChovayBtn.classList.add('d-none');
        nhaphangFields.classList.add('d-none');
        muontienGroup.classList.add('d-none'); // 👈 ẩn mặc định

        // Hiển thị theo loại được chọn
        if (type === 'thu') {
            thuSelect.classList.remove('d-none');
            addThuBtn.classList.remove('d-none');
        } else if (type === 'chi') {
            chiSelect.classList.remove('d-none');
            addChiBtn.classList.remove('d-none');
        } else if (type === 'nhaphang') {
            nhaphangSelect.classList.remove('d-none');
            addNhaphangBtn.classList.remove('d-none');
        }
        if (type === 'thu' || type === 'chi') {
            if (amountDefault) amountDefault.classList.remove('d-none');
        }
        if (type === 'nhaphang') {
            nhaphangSelect.classList.remove('d-none');
            addSupplierBtn.classList.remove('d-none');
            nhaphangFields.classList.remove('d-none');
        }
        if (type === 'muontien') {
            amountDefault.classList.remove('d-none');
            muontienGroup.classList.remove('d-none'); // 👈 hiện khi chọn Mượn tiền
        }

        // Gọi checkSpecialCategory ngay sau khi chọn loại mới
        const currentSelect = document.querySelector('#category_thu:not(.d-none), #category_chi:not(.d-none), #category_nhaphang:not(.d-none)');
        if (currentSelect) {
            checkSpecialCategory(currentSelect);
        }
    }

    function checkCovay(selectElement) {
        const selectedText = selectElement.options[selectElement.selectedIndex]?.text || '';
        const chovayGroup = document.getElementById('chovay-group');

        if (selectedText.trim().toLowerCase() === 'cho vay') {
            chovayGroup.classList.remove('d-none');
        } else {
            chovayGroup.classList.add('d-none');
        }
    }

    function checkLương(selectElement) {
        const selectedText = selectElement.options[selectElement.selectedIndex]?.text || '';
        const employeeGroup = document.getElementById('employee-group');

        if (selectedText.trim().toLowerCase() === 'lương') {
            employeeGroup.classList.remove('d-none');
        } else {
            employeeGroup.classList.add('d-none');
        }
    }

    function checkSpecialCategory(selectElement) {
        const selectedText = selectElement.options[selectElement.selectedIndex].text.trim().toLowerCase();

        const employeeGroup = document.getElementById('employee-group');
        const shopGroup = document.getElementById('shop-group');
        const dautuGroup = document.getElementById('dautu-group');
        const chovayGroup = document.getElementById('chovay-group');
        const addChovayBtn = document.getElementById('add-chovay-btn'); // 👈 lấy nút cho vay



        // Ẩn hết
        employeeGroup.classList.add('d-none');
        shopGroup.classList.add('d-none');
        dautuGroup.classList.add('d-none');
        chovayGroup.classList.add('d-none');
        addChovayBtn.classList.add('d-none'); // 👈 ẩn luôn nút


        // Hiện tùy theo tên danh mục
        if (selectedText === 'lương') {
            employeeGroup.classList.remove('d-none');
        } else if (selectedText === 'rút tiền') {
            shopGroup.classList.remove('d-none');
        } else if (selectedText === 'đầu tư' || selectedText === 'khoản đầu tư') {
            dautuGroup.classList.remove('d-none');
        } else if (selectedText === 'nhập hàng') {
            nhacungcapGroup.classList.remove('d-none'); // 💡 thêm dòng này
        } else if (selectedText === 'cho vay') {
            chovayGroup.classList.remove('d-none');
            addChovayBtn.classList.remove('d-none'); // 👈 hiện lại nút nếu đúng danh mục

        }
    }



    // Modal thêm danh mục: tự động set kiểu
    const modal = document.getElementById('addCategoryModal');
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const type = button.getAttribute('data-type');
        document.getElementById('modal_type').value = type;
        document.getElementById('type_text').innerText = type === 'thu' ? 'Thu' : 'Chi';
    });

    // Gọi lại khi trang tải
    window.addEventListener('DOMContentLoaded', () => {
        toggleCategory();

        // Lấy select đang hiển thị và kiểm tra nếu là "Lương"
        setTimeout(() => {
            const activeSelect = document.getElementById('category_chi').classList.contains('d-none') ?
                document.getElementById('category_chi') :
                document.getElementById('category_thu');
            checkLương(activeSelect);
        }, 100);
    });
    window.addEventListener('DOMContentLoaded', () => {
        toggleCategory();

        // gọi lại logic ẩn/hiện nhân viên/shop khi load trang
        setTimeout(() => {
            const activeSelect = document.getElementById('category_thu').classList.contains('d-none') ?
                document.getElementById('category_chi') :
                document.getElementById('category_thu');
            document.getElementById('category_nhaphang');

            checkSpecialCategory(activeSelect); // ← sửa lại để kiểm tra đúng tất cả case đặc biệt
        }, 100);
    });

    // Định dạng số tiền khi nhập
    function formatCurrencyLive(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = Number(value).toLocaleString('vi-VN');
        } else {
            input.value = '';
        }
    }

    // Gắn sự kiện cho các input tiền tệ
    document.addEventListener('DOMContentLoaded', function() {
        const moneyInputs = document.querySelectorAll('input[name="amount"], input[name="amount_total"], input[name="amount_paid"]');
        moneyInputs.forEach(function(input) {
            input.addEventListener('input', function(e) {
                // Lưu vị trí con trỏ
                const oldLength = input.value.length;
                const oldCursor = input.selectionStart;

                formatCurrencyLive(input);

                // Tính toán vị trí con trỏ mới
                const newLength = input.value.length;
                input.setSelectionRange(oldCursor + (newLength - oldLength), oldCursor + (newLength - oldLength));
            });
        });
    });
</script>

@endsection