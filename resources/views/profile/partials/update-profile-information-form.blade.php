<section class="mb-5">
    <header class="mb-4">
        <h2 class="h5 text-dark">
            Thông tin hồ sơ
        </h2>
        <p class="text-muted small">
            Cập nhật thông tin hồ sơ và địa chỉ email của bạn.
        </p>
    </header>

    {{-- Form gửi lại email xác minh --}}
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="d-none">
        @csrf
    </form>

    {{-- Form cập nhật hồ sơ + avatar --}}
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- Avatar --}}
        <div class="mb-4">
            <label for="avatar" class="form-label d-block">Ảnh đại diện</label>

            <div class="mb-2">
                <div style="width: 80px; height: 80px; overflow: hidden; border-radius: 50%;">
                    <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('images/avatar.png') }}"
                        alt="Avatar"
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>

            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
            @error('avatar')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Họ tên --}}
        <div class="mb-3 ">
            <label for="name" class="form-label">Họ và tên</label>
            <input id="name" type="text"
                class="form-control rounded-lg border border-gray-300 focus:border-gray-400 p-3 @error('name') is-invalid @enderror"
                name="name" value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email"
                class="form-control rounded-lg border border-gray-300 focus:border-gray-400 p-3 @error('email') is-invalid @enderror"
                name="email" value="{{ old('email', $user->email) }}"
                required autocomplete="username">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            {{-- Nếu chưa xác minh email --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning mt-3 small">
                Địa chỉ email của bạn chưa được xác minh.
                <button type="submit" form="send-verification" class="btn btn-link p-0 align-baseline">
                    Nhấn vào đây để gửi lại email xác minh.
                </button>
            </div>

            @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success small mt-2">
                Một liên kết xác minh mới đã được gửi tới email của bạn.
            </div>
            @endif
            @endif
        </div>

        {{-- Nút lưu --}}
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-info">
                Lưu thay đổi
            </button>

            @if (session('status') === 'profile-updated')
            <span class="text-success small">Đã lưu thay đổi vừa rồi.</span>
            @endif
        </div>
    </form>
</section>