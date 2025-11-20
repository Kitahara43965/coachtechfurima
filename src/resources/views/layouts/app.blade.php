<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>coachtechフリマ</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom-select.css') }}">
  @yield('css')
</head>

@php

  if(isset($isItemList)){
    if($isItemList){
      $newRouteSearch = url()->current();
    }else{
      $newRouteSearch = route('index');
    }
  }else{
    $newRouteSearch = route('index');
  }

  if(isset($isMultipleFunctionHeader)){
    $newIsMultipleFunctionHeader = $isMultipleFunctionHeader;
  }else{
    $newIsMultipleFunctionHeader = false;
  }

@endphp

<body class="body">

  <header class="header">
    <div class="header-left-block">
      <a href="{{route('index')}}" class="header-index">
        <img src="{{ asset('storage/svg/logo.svg') }}" alt="テストSVG" class="header-svg-logo">
      </a>
    </div>


    <div class="header-middle-block">
        @if($newIsMultipleFunctionHeader)
          <form action="{{ $newRouteSearch }}" method="GET" class="header-form">
              @foreach(request()->query() as $key => $value)
                  @if($key !== 'keyword')
                      <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                  @endif
              @endforeach

              <div class="header-form__input--text">
                  <input
                      type="text"
                      name="keyword"
                      value="{{ request('keyword', session('search_keyword', '')) }}"
                      placeholder="なにをお探しですか?" />
              </div>

              <button type="submit" style="display:none;"></button>
          </form>
        @endif
    </div>
    <div class="header-right-block">
        <ul class="header-right-block-child">
          @if($newIsMultipleFunctionHeader)
            <li class="header-log">
              @if (Auth::check())
                <form action="{{route('logout')}}" method="post" class="header-form">
                  @csrf
                  <button class="header-log-button">ログアウト</button>
                </form>
              @else
                <button
                  type="button"
                  class="header-log-button"
                  onclick="window.location='{{ route('login') }}'">
                  ログイン
                </button>
              @endif
            </li>
            <li class = "header-mypage">
                <a href="{{route('mypage')}}" class = "header-mypage-href">マイページ</a>
            </li>
            
            <li class="header-sell">
              <button
                  type="button"
                  class="header-sell-button"
                  onclick="window.location='{{ route('sell') }}'">
                  出品
              </button>
            </li>
          @endif
        </ul>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>