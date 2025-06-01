@extends('layouts.app') {{-- Hoặc layouts.guest nếu bạn có layout riêng cho guest --}}

@section('content')
<div class="container mt-5" style="max-width: 600px;">
    <div class="alert alert-secondary small mb-4">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small mb-4">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        {{-- Gửi lại email xác thực --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        {{-- Đăng xuất --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-danger text-decoration-none">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</div>
@endsection
