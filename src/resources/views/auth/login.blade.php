@extends('layouts.app')
   
@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection


@section('content')

<div class="login-board">
    <div class="gravity-center-child">
        <h1>ログイン</h1>
    </div>
    <form method="POST" action="{{ route('login.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="form__group">
          <div class="form__group-title">
            <span class="form__label--item">メールアドレス</span>
          </div>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="email" name="email" value="{{ old('email') }}" />
            </div>
            <div class="form__error">
              @error('email')
              {{ $message }}
              @enderror
            </div>
          </div>
        </div>
        <div class="form__group">
          <div class="form__group-title">
            <span class="form__label--item">パスワード</span>
          </div>
          <div class="form__group-content">
            <div class="form__input--text">
              <input type="password" name="password" />
            </div>
            <div class="form__error">
              @error('password')
              {{ $message }}
              @enderror
            </div>
          </div>
        </div>
        <div class="login-upper-form-button-blank"></div>
        <div class="form__button">
          <button class="form__button-submit" type="submit">ログインする</button>
        </div>
    </form>

    <!-- 新規登録へのリンク -->
    <div class="register__link">
        <a class="link-no-decoration" href="{{ route('register') }}">
            会員登録はこちら
        </a>
    </div>

    <div class="login-form-bottom-blank"></div>
</div>

@endsection
