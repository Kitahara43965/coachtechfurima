@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-board">
        <div class="gravity-center-child">
            <h1>プロフィール設定</h1>
        </div>
        <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form__group">

                @php
                    $routeCountImages = route('profile.image.count');
                    $previewContainerClass = 'user-image-container';
                    $previewClass = 'user-image';
                    $fileUploadButtonClass = 'profile-file-upload-button';
                    $oldImageName = old('image_name');
                    $oldPreviewUrl = old('preview_url');
                    $embeddedImageName = $authenticatedUser?->image;
                    $embeddedPreviewUrl = $embeddedImageName ? asset('storage/'.$userImageDirectory.'/'.$embeddedImageName) : null;
                    $candidateNewImageName = $oldImageName ?? $embeddedImageName;
                    $candidateNewPreviewUrl = $oldPreviewUrl ?? $embeddedPreviewUrl;
                    if($candidateNewPreviewUrl){
                        $newImageName = $candidateNewImageName;
                        $newPreviewUrl = $candidateNewPreviewUrl;
                    }else{
                        $newImageName = null;
                        $newPreviewUrl = null;
                    }//$candidateNewPreviewUrl
                @endphp

                <div class="profile-custom-file">

                    <div class="{{ $previewContainerClass }}">
                        <img id="preview"
                            src="{{ $newPreviewUrl ?? $defaultProfilePreviewUrl }}"
                            class="{{$previewClass}}">
                    </div>
                    <label class="{{$fileUploadButtonClass}}">
                        <input type="file" id="imageInput" name="image" accept="image/*">
                        画像を選択する
                    </label>
                </div>

                <input class="user-image-name" type="hidden" id="imageName" value="{{ $newImageName }}" readonly>
                <input type="hidden" name="preview_url" value="{{ $newPreviewUrl }}">
                <input type="hidden" name="image_name" value="{{ $newImageName }}">
                
                
                <div class="form__error">
                    @error('image')
                    {{ $message }}
                    @enderror
                </div>
            
                <script>
                    window.profileImageConfig = {
                        previewContainerSelector: ".{{ $previewContainerClass }}",
                        previewSelector: ".{{ $previewClass }}",
                        fileUploadButtonSelector: ".{{ $fileUploadButtonClass}}",
                    };
                    window.route = {
                        countImages: "{{ $routeCountImages }}"
                    };
                    window.imagePrefix = "{{ $userImagePrefix }}";
                    window.hasPreview = @json(!empty($newPreviewUrl) || !empty($defaultProfilePreviewUrl));
                </script>
                <script src="{{ asset('js/preview-filename.js') }}" defer></script>

            </div>
                


            <div class="form__group">
                @php
                    $oldUserName = old('username');
                    $embeddedUserName = $authenticatedUser?->username;
                    $newUserName = $oldUserName ? $oldUserName : $embeddedUserName;
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">ユーザー名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="username" value="{{ $newUserName }}" />
                    </div>
                    <div class="form__error">
                        @error('username')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__group">
                @php
                    $oldPostcode = old('postcode');
                    $embeddedPostcode = $authenticatedUser?->postcode;
                    $newPostcode = $oldPostcode ? $oldPostcode : $embeddedPostcode;
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">郵便番号</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="postcode" value="{{ $newPostcode }}" />
                    </div>
                    <div class="form__error">
                        @error('postcode')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                @php
                    $oldAddress = old('address');
                    $embeddedAddress = $authenticatedUser?->address;
                    $newAddress = $oldAddress ? $oldAddress : $embeddedAddress;
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">住所</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="address" value="{{ $newAddress }}" />
                    </div>
                    <div class="form__error">
                        @error('address')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__group">
                @php
                    $oldBuilding = old('building');
                    $embeddedBuilding = $authenticatedUser->building;
                    $newBuilding = $oldBuilding ? $oldBuilding : $embeddedBuilding;
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">建物名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="building" value="{{ $newBuilding }}"/>
                    </div>
                    <div class="form__error">
                        @error('building')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>

    </div>
@endsection
