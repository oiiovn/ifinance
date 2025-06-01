<section class="mb-5">
    <header class="mb-4">
        <h2 class="h5 text-danger">
            Xoá tài khoản
        </h2>
        <p class="text-muted small">
            Sau khi tài khoản bị xoá, tất cả dữ liệu liên quan sẽ bị xoá vĩnh viễn. Hãy chắc chắn rằng bạn đã sao lưu những thông tin quan trọng trước khi thực hiện thao tác này.
        </p>
    </header>

    {{-- Nút mở modal --}}
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        Xoá tài khoản
    </button>

    {{-- Modal xác nhận --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="confirmDeleteModalLabel">Xác nhận xoá tài khoản</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-3">
                            Bạn có chắc chắn muốn xoá tài khoản? Tất cả dữ liệu và thông tin liên quan sẽ bị xoá vĩnh viễn và không thể khôi phục.
                        </p>

                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Mật khẩu</label>
                            <input id="delete_password" name="password" type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Nhập mật khẩu để xác nhận">
                            @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        <button type="submit" class="btn btn-danger">Xoá tài khoản</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>