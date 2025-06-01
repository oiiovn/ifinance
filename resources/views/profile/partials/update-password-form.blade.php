<section class="mb-5">
    <header class="mb-4">
        <h2 class="h5 text-dark">
            {{ __('Cập nhật mật khẩu') }}
        </h2>
        <p class="text-muted small">
            {{ __('Đảm bảo tài khoản của bạn sử dụng mật khẩu đủ mạnh và ngẫu nhiên để tăng bảo mật.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        {{-- Mật khẩu hiện tại --}}
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('Mật khẩu hiện tại') }}</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Mật khẩu mới --}}
        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('Mật khẩu mới') }}</label>
            <input id="update_password_password" name="password" type="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Xác nhận mật khẩu --}}
        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('Xác nhận mật khẩu') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nút lưu và thông báo --}}
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('Lưu thay đổi') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success small">{{ __('Mật khẩu đã được cập nhật.') }}</span>
            @endif
        </div>
    </form>
</section>
