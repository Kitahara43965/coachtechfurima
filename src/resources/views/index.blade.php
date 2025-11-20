@extends('layouts.app')

@php
    $homeBladeActionKind = 1;
    $mypageBladeActionKind = 2;
    if($indexKind == $indexIndexKind){
        $bladeActionKind = $homeBladeActionKind;
        $homeIndexToggleClass = "index-toggle-red-text";
        $mylistIndexToggleClass = "index-toggle-black-text";
        $boughtGoodsIndexToggleClass = "index-toggle-black-text";
        $soldGoodsIndexToggleClass = "index-toggle-black-text";
    }else if($indexKind == $mylistIndexKind){
        $bladeActionKind = $homeBladeActionKind;
        $homeIndexToggleClass = "index-toggle-black-text";
        $mylistIndexToggleClass = "index-toggle-red-text";
        $boughtGoodsIndexToggleClass = "index-toggle-black-text";
        $soldGoodsIndexToggleClass = "index-toggle-black-text";
    }else if($indexKind == $boughtGoodsIndexKind){
        $bladeActionKind = $mypageBladeActionKind;
        $homeIndexToggleClass = "index-toggle-black-text";
        $mylistIndexToggleClass = "index-toggle-black-text";
        $boughtGoodsIndexToggleClass = "index-toggle-red-text";
        $soldGoodsIndexToggleClass = "index-toggle-black-text";
    }else if($indexKind == $soldGoodsIndexKind){
        $bladeActionKind = $mypageBladeActionKind;
        $homeIndexToggleClass = "index-toggle-black-text";
        $mylistIndexToggleClass = "index-toggle-black-text";
        $boughtGoodsIndexToggleClass = "index-toggle-black-text";
        $soldGoodsIndexToggleClass = "index-toggle-red-text";
    }else{//$indexKind
        $bladeActionKind = $homeBladeActionKind;
        $homeIndexToggleClass = "index-toggle-black-text";
        $mylistIndexToggleClass = "index-toggle-black-text";
        $boughtGoodsIndexToggleClass = "index-toggle-black-text";
        $soldGoodsIndexToggleClass = "index-toggle-black-text";
    }//$indexKind

@endphp

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

@php
    $candidateNewImageName = $authenticatedUserImageName;
    if($candidateNewImageName){
        $newImageName = $candidateNewImageName;
        $newPreviewUrl = asset('storage/'.$userImageDirectory.'/'.$newImageName);
    }else{//$candidateNewImageName
        $newImageName = null;
        $newPreviewUrl = null;
    }//$candidateNewImageName
@endphp

    <div class="index-board">
        
        @if($bladeActionKind == $homeBladeActionKind)
            <div class="index-toggle-upper-blank">
            <div class="index-toggle-block">
                <div class="index-toggle-inner-block">
                    <a class="{{$homeIndexToggleClass}}" href="{{ route('index') }}">
                        おすすめ
                    </a>
                    <a class="{{$mylistIndexToggleClass}}" href="{{ route('index', ['tab' => 'mylist']) }}">
                        マイリスト
                    </a>
                </div>
            </div>
        @elseif($bladeActionKind == $mypageBladeActionKind)
                
            <div class="index-user-image-block-upper-blank">
            <div class="index-user-image-block">
                <div class="index-user-image-block-left">
                    <div class="user-image-container">
                        <img id="preview"
                            src="{{$newPreviewUrl ?? $defaultProfilePreviewUrl}}"
                            class="user-image">
                    </div>
                    <div class="index-user-name">
                        {{$authenticatedUser?->username}}
                    </div>
                </div>
                <a class="index-profile-button" href="{{ route('mypage.profile') }}">
                    プロフィールを編集
                </a>
            </div>
            <div class="index-toggle-upper-blank">
            <div class="index-toggle-block">
                <div class="index-toggle-inner-block">
                    <a class="{{$soldGoodsIndexToggleClass}}" href="{{ route('mypage', ['page' => 'sell']) }}">
                        出品した商品
                    </a>
                    <a class="{{$boughtGoodsIndexToggleClass}}" href="{{ route('mypage', ['page' => 'buy']) }}">
                        購入した商品
                    </a>
                </div>
            </div>
        @endif

        <div class="index-section-borderline"></div>


        <div class="index-item-card-container">

            @foreach($items as $item)
                @php
                    $candidateNewImageName = $item->image;
                    if($candidateNewImageName){
                        $newImageName = $candidateNewImageName;
                        if($item->is_default){
                            $newPreviewUrl = asset('storage/'.$coachtechImageDirectory.'/'.$newImageName);
                        }else{
                            $newPreviewUrl = asset('storage/'.$itemImageDirectory.'/'.$newImageName);
                        }
                    }else{//$candidateNewImageName
                        $newPreviewUrl = null;
                        $newImageName = null;
                    }//$candidateNewImageName

                    $isPurchased = $item->isPurchased();

                @endphp
                <a href="{{ route('item.item_id', ['item_id' => $item->id]) }}" class="index-item-card">
                    <div class="index-image-container">
                        <div class="index-item-image-container ">
                            <img src="{{ $newPreviewUrl }}" class="index-item-image">
                        </div>
                        @if($isPurchased)
                            <div class="index-sold-text">Sold</div>
                        @endif
                    </div>
                    <div class="index-item-card-footer">
                        <span class="index-item-name">{{ $item->name }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

@endsection