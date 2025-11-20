@extends('layouts.app')

@php
    
    $addBladeActionKind = 1;
    $editBladeActionKind = 2;

    if($displayKind === $addDisplayKind){
        $bladeActionKind = $addBladeActionKind;
    }elseif($displayKind === $editDisplayKind){
        $bladeActionKind = $editBladeActionKind;
    }else{
        $bladeActionKind = $addBladeActionKind;
    }

    if($bladeActionKind === $addBladeActionKind){
        $createOrUpdateRoute = route('sell.store');
        $deleteRoute = null;
    }elseif($bladeActionKind === $editBladeActionKind){
        $createOrUpdateRoute = route('sell.update.item_id',['item_id' => $item_id]);
        $deleteRoute = route('sell.delete.item_id',['item_id' => $item_id]);;
    }

    $isChangeDisabled = false;
    if($bladeActionKind === $addBladeActionKind){
    }else if($bladeActionKind === $editBladeActionKind){
        if(!$isOwner){
            $isChangeDisabled = true;
        }
        if($isPurchased){
            $isChangeDisabled = true;
        }
    }//$bladeActionKind


@endphp

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection


@section('content')
    <div class="sell-board">
        <div class="gravity-center-child">
            @if($bladeActionKind === $addBladeActionKind)
                <h1>商品の出品</h1>
            @elseif($bladeActionKind === $editBladeActionKind)
                <h1>{{$selectedItem->name}}</h1>
            @endif
        </div>

        <form method="POST" action="{{ $createOrUpdateRoute }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">商品画像</span>
                </div>
                @php
                    $routeCountImages = route('sell.image.count');
                    $previewContainerClass = 'sell-item-image-container';
                    $previewClass = 'sell-item-image';
                    $fileUploadButtonClass = 'sell-file-upload-button';
                    $oldImageName = old('image_name');
                    $oldPreviewUrl = old('preview_url');
                    if($isChangeDisabled){
                        $imageChangeDisabled = true;
                    }else{
                        $imageChangeDisabled = false;
                    }


                    if($bladeActionKind === $addBladeActionKind){
                        
                        $embeddedImageName = null;
                        $embeddedPreviewUrl = null;
                    }else if($bladeActionKind === $editBladeActionKind){

                        $embeddedImageName = $selectedItem->image ?? null;
                        if($selectedItem->is_default){
                            $embeddedPreviewUrl = $embeddedImageName ? asset('storage/'.$coachtechImageDirectory.'/'.$embeddedImageName) : null;
                        }else{
                            $embeddedPreviewUrl = $embeddedImageName ? asset('storage/'.$itemImageDirectory.'/'.$embeddedImageName) : null;
                        }
                    }//$bladeActionKind
                    $candidateNewImageName = $oldImageName ?? $embeddedImageName;
                    $candidateNewPreviewUrl = $oldPreviewUrl ?? $embeddedPreviewUrl;
                    $newImageName = null;
                    $newPreviewUrl = null;
                    if($candidateNewPreviewUrl){
                        $newImageName = $candidateNewImageName;
                        $newPreviewUrl = $candidateNewPreviewUrl;
                    }//$candidateNewPreviewUrl

                    if($newPreviewUrl){
                        $previewImageFlag = " large";
                        $fileUploadButtonFlag = " none";
                        $fileUploadButtonNoneFlag = "";
                    }else{
                        $previewImageFlag = "";
                        $fileUploadButtonFlag = "";
                        $fileUploadButtonNoneFlag = " none";
                    }
                @endphp

                <div class="sell-custom-file">
                    <div class="{{ $previewContainerClass.$previewImageFlag}} ">
                        <img id="preview"
                            src="{{ $newPreviewUrl ?? '' }}"
                            class="{{ $previewClass.$previewImageFlag}}">
                        @if(!$imageChangeDisabled)
                            <label class="{{$fileUploadButtonClass.$fileUploadButtonFlag}}">
                                <input type="file" id="imageInput" name="image" accept="image/*">
                                ファイルを選択
                            </label>
                        @endif
                    </div>
                </div>
                
                <input class="sell-item-image-name" type="hidden" id="imageName" value="{{ $newImageName }}" readonly>
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
                    window.imagePrefix = "{{ $itemImagePrefix }}";
                    window.hasPreview = @json(!empty($newPreviewUrl));
                </script>
                <script src="{{ asset('js/preview-filename.js') }}" defer></script>
            </div>

            <h2 class="sell-detail-subtitle">商品の詳細</h2>
            <div class="sell-section-borderline"></div>

            <div class="form__group">
                @php
                    $categoryButtonClass = "category-button";
                    $selectedCategoryId = "selected-category";

                    $oldCategoryIds = collect(explode(',', old('category_id', '')))
                        ->filter()
                        ->map(fn($id) => (int) $id)
                        ->toArray();

                    if($isChangeDisabled){
                        $categoryIdChangeDisabled = true;
                    }else{
                        $categoryIdChangeDisabled = false;
                    }
                    if ($bladeActionKind === $addBladeActionKind) {
                        $embeddedCategoryIds = [];
                    } elseif ($bladeActionKind === $editBladeActionKind) {
                        $embeddedCategoryIds = $selectedCategoryIds ?? [];
                    }

                    $newCategoryIds = !empty($oldCategoryIds) ? $oldCategoryIds : $embeddedCategoryIds;

                    if($categoryIdChangeDisabled){
                        $stringCategoryIdChangeDisabled = " disabled";
                    }else{
                        $stringCategoryIdChangeDisabled = "";
                    }

                @endphp

                <div class="form__group-title">
                    <span class="form__label--item">カテゴリー</span>
                </div>

                <div class="form__group-content">
                    <div id="category-buttons-container">
                        @foreach($categories as $category)
                            <button
                                type="button"
                                class="{{ $categoryButtonClass }} {{ in_array($category->id, $newCategoryIds) ? 'active' : '' }}"
                                data-id="{{ $category->id }}"
                                @if(!empty($stringCategoryIdChangeDisabled)) disabled @endif
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="category_id" id="{{ $selectedCategoryId }}" value="{{ implode(',', $newCategoryIds) }}">

                    <script>
                        window.categorySelectConfig = {
                            buttonSelector: '.{{ $categoryButtonClass }}',
                            hiddenInputSelector: '#{{ $selectedCategoryId }}',
                            activeClass: 'active'
                        };
                    </script>
                </div>

                <div class="form__error">
                    @error('category_id')
                        {{ $message }}
                    @enderror
                </div>

                <script src="{{ asset('js/category-select.js') }}" defer></script>
            </div>


            <div class="form__group">
                @php
                    $idName = 'condition_id';
                    $oldConditionId = old($idName);
                    $newConditionId = $oldConditionId ? $oldConditionId : $selectedConditionId;
                    if($isChangeDisabled){
                        $isDisabledOnCustomSelect = true;
                    }else{
                        $isDisabledOnCustomSelect = false;
                    }

                    $placeholder = '選択してください';
                    $wrapperName = 'sell-custom-select-wrapper';
                    $isFetch = false;
                @endphp

                <div class="form__group-title">
                    <span class="form__label--item">商品の状態</span>
                </div>
                
               <div class="{{$wrapperName}}">
                    <div class="custom-select-selected-option" id="custom-select-id-name">
                        <span class="custom-select-placeholder">{{ $placeholder }}</span>
                        <span class="custom-select-selected-text" style="display:none;"></span>
                    </div>

                    <ul class="custom-select-list">
                        @foreach($conditions as $condition)
                            <li 
                                class="custom-select-item {{ (int)$newConditionId === (int)$condition->id ? 'selected' : '' }}" 
                                data-id="{{ $condition->id }}"
                                data-name="{{ $condition->name }}"
                                data-color="{{ $condition->color ?? 'black' }}">
                                <span class="custom-select-check-icon"></span>
                                <span class="custom-select-item-text">{{ $condition->name }}</span>
                                <div class="custom-select-blue-bar"></div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <input type="hidden" name="{{ $idName }}" id="hidden-{{ $idName }}" value="{{ old($idName, $newConditionId) }}">

                <div class="form__error">
                    @error($idName)
                        {{ $message }}
                    @enderror
                </div>

                 <script>
                    window.customSelectConfig = {
                        id: "{{ old($idName, $newConditionId ?? '') }}", 
                        wrapperName: "{{$wrapperName}}",
                        isDisabled: {{ $isDisabledOnCustomSelect ? 'true' : 'false' }},
                        isFetch:{{$isFetch ? 'true' : 'false' }},
                        updateUrl: '{{ null }}',
                        csrfToken: '{{ csrf_token() }}',
                        placeholder: '{{ $placeholder }}',
                        idName: "{{ $idName }}",
                    };
                </script>
                <script src="{{ asset('js/custom-select.js') }}" defer></script>
            </div>

            <h2 class="sell-description-subtitle">商品名と説明</h2>
            <div class="sell-section-borderline"></div>

            <div class="form__group">
                @php
                    $oldName = old('name');
                    if($isChangeDisabled){
                        $nameChangeDisabled = true;
                    }else{
                        $nameChangeDisabled = false;
                    }
                    if($bladeActionKind === $addBladeActionKind){
                        $embeddedName = null;
                    }else if($bladeActionKind === $editBladeActionKind){
                        $embeddedName = $selectedItem->name ?? null;
                    }//$bladeActionKind
                    $newName = $oldName ? $oldName : $embeddedName;

                    if($nameChangeDisabled){
                        $stringNameReadonly = ' readonly';
                    }else{//$nameChangeDisabled
                        $stringNameReadonly = '';
                    }//$nameChangeDisabled

                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">商品名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ $newName }}" {{$stringNameReadonly}}/>
                    </div>
                    <div class="form__error">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__group">
                @php
                    $oldBrand = old('brand');
                    if($isChangeDisabled){
                        $brandChangeDisabled = true;
                    }else{
                        $brandChangeDisabled = false;
                    }
                    if($bladeActionKind === $addBladeActionKind){
                        $embeddedBrand = null;
                    }else if($bladeActionKind === $editBladeActionKind){
                        $embeddedBrand = $selectedItem->brand ?? null;
                    }//$bladeActionKind
                    $newBrand = $oldBrand ? $oldBrand : $embeddedBrand;

                    if($brandChangeDisabled){
                        $stringBrandReadonly = ' readonly';
                    }else{
                        $stringBrandReadonly = '';
                    }

                    
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">ブランド名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="brand" value="{{ $newBrand }}" {{$stringBrandReadonly}}/>
                    </div>
                    <div class="form__error">
                        @error('brand')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__group">
                @php
                    $oldDescription = old('description');
                    if($isChangeDisabled){
                        $descriptionChangeDisabled = true;
                    }else{
                        $descriptionChangeDisabled = false;
                    }
                    if($bladeActionKind === $addBladeActionKind){
                        $embeddedDescription = null;
                    }else if($bladeActionKind === $editBladeActionKind){
                        $embeddedDescription = $selectedItem->description ?? null;
                    }//$bladeActionKind
                    $newDescription = $oldDescription ? $oldDescription : $embeddedDescription;
                
                    if($descriptionChangeDisabled){
                        $stringDescriptionReadonly = ' readonly';
                    }else{
                        $stringDescriptionReadonly = '';
                    }

                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">商品の説明</span>
                </div>
                <div class="form__group-content">
                    <textarea name="description" 
                     class = "sell-description" 
                    {{$stringDescriptionReadonly}}>{{ $newDescription }}</textarea>
                    <div class="form__error">
                        @error('description')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__group">
                @php
                    $oldPrice = old('price');
                    if($isChangeDisabled){
                        $priceChangeDisabled = true;
                    }else{
                        $priceChangeDisabled = false;
                    }
                    if($bladeActionKind === $addBladeActionKind){
                        $embeddedPrice = null;
                    }else if($bladeActionKind === $editBladeActionKind){
                        $embeddedPrice = $selectedItem->price ?? null;
                    }//$bladeActionKind

                    if(isset($oldPrice)){
                        if($oldPrice){
                            $newPrice = $oldPrice;
                        }else{
                            $newPrice = 0;
                        }
                    }else{
                        $newPrice = $embeddedPrice;
                    }

                    if($priceChangeDisabled){
                        $stringPriceReadonly = ' readonly';
                    }else{
                        $stringPriceReadonly = '';
                    }

                @endphp

                <div class="form__group-title">
                    <span class="form__label--item">販売価格</span>
                </div>
                <div class="form__group-content">
                    <div class="sell_input--text">
                        <input type="text" id="price" name="price" value="{{ $newPrice }}" {{ $stringPriceReadonly }}>
                    </div>
                    <div class="form__error">
                        @error('price')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                @if($bladeActionKind === $addBladeActionKind)
                    <div class="sell-button">
                        <button class="form__button-submit" type="submit" name="sellType" value="createSellType">出品する</button>
                    </div>
                @elseif($bladeActionKind === $editBladeActionKind)
                    <div class="sell-button">
                        <button
                            type="button"
                            class="form__return-button-submit"
                            onclick="window.location='{{ route('item.item_id', ['item_id' => $item_id]) }}'">
                            戻る
                        </button>
                        @if(!$isChangeDisabled)
                            <button class="form__update-button-submit" type="submit" name="sellType" value="updateSellType">更新</button>
                        @endif
                    </div>
                @endif
            </div>
        </form>
        
        @if($bladeActionKind === $editBladeActionKind)
            @if(!$isChangeDisabled)
                <form method="POST" action="{{ $deleteRoute }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('DELETE')
                    <button type="submit" name="sellType" value="deleteSellType" class="sell-delete-button-group">
                        <img src="{{ $trashPreviewUrl }}" alt="削除" class="sell-delete-button">
                    </button>
                </form>
            @endif
        @endif

        
    </div>
@endsection