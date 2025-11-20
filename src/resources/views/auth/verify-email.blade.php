@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

<script>
    const routes = {
        mypage_profile: "{{ route('mypage.profile') }}",
    };
</script>

@section('content')

    <div class="verify-email-board">
        <div class="verify-email-block">
            <div class="verify-email-text">
                <div>登録していただいたメールアドレスに認証メールを送付しました。</div>
                <div>メール認証を完了してください。</div>
            </div>

            <form method="POST" action="{{ route('verification.manual') }}" class="">
                @csrf
                <button type="submit" class="verify-email-button">
                    認証はこちらから
                </button>
            </form>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="verify-email-auth-resend-button">認証メールを再送する</button>
            </form>
        </div>
    </div>

    <div class="register-form-bottom-blank"></div>
    
@endsection