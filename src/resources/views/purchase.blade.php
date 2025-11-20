@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection


@section('content')
        
    <form
        class="purchase-board"
        method="POST" 
        action="{{ route('purchase.store.item_id',['item_id'=>$item_id]) }}" 
        enctype="multipart/form-data" 
        novalidate
    >
        @csrf
        <div class="purchase-left-block">

            <div class="purchase-item-container">
            
                @php
                    $candidateNewImageName = $selectedItem->image;
                    if($candidateNewImageName){
                        $newImageName = $candidateNewImageName;
                        if($selectedItem->is_default){
                            $newPreviewUrl = asset('storage/'.$coachtechImageDirectory.'/'.$newImageName);
                        }else{
                            $newPreviewUrl = asset('storage/'.$itemImageDirectory.'/'.$newImageName);
                        }
                    }else{//$candidateNewImageName
                        $newPreviewUrl = null;
                        $newImageName = null;
                    }//$candidateNewImageName
                @endphp
                <div class="purchase-item-image-container">
                    <img id="preview"
                        src="{{$newPreviewUrl}}"
                        class="purchase-item-image">
                </div>
                <div class="purchase-item-note">
                    <h1 class="purchase-item-name">
                        {{$selectedItem->name}}
                    </h1>
                    <div class="purchase-price-container">
                        <div class="purchase-price-sign">¥</div>
                        <div class="purchase-price-value">{{number_format($selectedItem->price) }}</div>
                    </div>
                </div>
            </div>

            <div class="purchase-section-borderline"></div>

            <div class="form__group">
                <div class="form__group-title">
                    <h2 class="form__label--item">支払い方法</h2>
                </div>
                @php
                    $idName = 'purchase_method_id';
                    $oldPurchaseMethodId = old($idName);
                    $newPurchaseMethodId = $oldPurchaseMethodId ? $oldPurchaseMethodId : $selectedPurchaseMethodId;
                    $isDisabledOnCustomSelect = false;
                    if($isPurchased){
                        $isDisabledOnCustomSelect = true;
                    }
                    if($isOwner){
                        $isDisabledOnCustomSelect = true;
                    }

                    $placeholder = '選択してください';
                    $wrapperName = 'purchase-custom-select-wrapper';
                    $isFetch = true;
                @endphp

                
               <div class="{{$wrapperName}}">
                    <div class="custom-select-selected-option" id="custom-select-id-name">
                        <span class="custom-select-placeholder">{{ $placeholder }}</span>
                        <span class="custom-select-selected-text" style="display:none;"></span>
                    </div>

                    <ul class="custom-select-list">
                        @foreach($purchaseMethods as $purchaseMethod)
                            <li 
                                class="custom-select-item {{ (int)$newPurchaseMethodId === (int)$purchaseMethod->id ? 'selected' : '' }}" 
                                data-id="{{ $purchaseMethod->id }}"
                                data-name="{{ $purchaseMethod->name }}"
                                data-color="{{ $purchaseMethod->color ?? 'black' }}">
                                <span class="custom-select-check-icon"></span>
                                <span class="custom-select-item-text">{{ $purchaseMethod->name }}</span>
                                <div class="custom-select-blue-bar"></div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <input type="hidden" name="{{ $idName }}" id="hidden-{{ $idName }}" value="{{ old($idName, $newPurchaseMethodId) }}">

                <div class="form__error">
                    @error($idName)
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="purchase-section-borderline"></div>

            <div class="form__group">
                @php
                    
                    if($selectedPurchaseTypedPivot){
                        $newIsFilledWithAddress = $selectedPurchaseTypedPivot?->pivot?->is_filled_with_delivery_address;
                        $newDeliveryPostcode = $selectedPurchaseTypedPivot?->pivot?->delivery_postcode;
                        $newDeliveryAddress = $selectedPurchaseTypedPivot?->pivot?->delivery_address;
                        $newDeliveryBuilding = $selectedPurchaseTypedPivot?->pivot?->delivery_building;
                    }else{
                        if($isFilledWithProfile){
                            $newIsFilledWithAddress = true;
                            $newDeliveryPostcode = $authenticatedUser->postcode;
                            $newDeliveryAddress = $authenticatedUser->address;
                            $newDeliveryBuilding = $authenticatedUser->building;
                        }else{
                            $newIsFilledWithAddress = false;
                            $newDeliveryPostcode = null;
                            $newDeliveryAddress = null;
                            $newDeliveryBuilding = null;
                        }
                    }
                @endphp

                

                <div class="purchase-delivery-address">
                    <h2>配送先</h2>
                    <a href="{{ route('purchase.address.item_id',['item_id' => $item_id]) }}" class="link-no-decoration">
                        変更する
                    </a>
                </div>

                    <div class="purchase-address">
                        <div class="purchase-address-detail">{{$newDeliveryPostcode}}</div>
                        <div class="purchase-address-detail">{{$newDeliveryAddress}}</div>
                        <div class="purchase-address-detail">{{$newDeliveryBuilding}}</div>
                    </div>

                <input type="hidden" name="is_filled_with_delivery_address" value="{{ $newIsFilledWithAddress }}">
                <input type="hidden" name="delivery_postcode" value="{{ $newDeliveryPostcode }}">
                <input type="hidden" name="delivery_address" value="{{ $newDeliveryAddress }}">
                <input type="hidden" name="delivery_building" value="{{ $newDeliveryBuilding }}">

                <script>
                    window.customSelectConfig = {
                        id: "{{ old($idName, $newPurchaseMethodId ?? '') }}",
                        wrapperName: "{{$wrapperName}}",
                        isDisabled: {{ $isDisabledOnCustomSelect ? 'true' : 'false' }},
                        isFetch:{{$isFetch ? 'true' : 'false' }},
                        updateUrl: '{{ route("purchase.update-method", $item_id) }}',
                        csrfToken: '{{ csrf_token() }}',
                        placeholder: '{{ $placeholder }}',
                        idName: "{{ $idName }}",

                        isFilledWithDeliveryAddress: {{ $newIsFilledWithAddress ? 'true' : 'false' }},
                        deliveryPostcode: @json($newDeliveryPostcode),
                        deliveryAddress: @json($newDeliveryAddress),
                        deliveryBuilding: @json($newDeliveryBuilding),
                    };
                </script>
                <script>
                    console.log("CSRFトークン:", window.customSelectConfig.csrfToken);
                </script>
                <script src="{{ asset('js/custom-select.js') }}" defer></script>

                <div class="form__error">
                    @error('is_filled_with_address')
                        {{ $message }}
                    @enderror
                </div>

            </div>

            <div class="purchase-section-borderline"></div>
        </div>

        <div class="purchase-right-block">


            <table class="purchase-table">
                <tr class="purchase-table-first-row">
                    <td><div class="purchase-table-normal-text">商品代金</div></td>
                    <td>
                        <div class="purchase-table-price-container">
                            <div class="purchase-table-normal-text">¥</div>
                            <div class="purchase-table-price-value">{{number_format($selectedItem->price) }}</div>
                        </div>
                    </td>
                </tr>
                <tr class="purchase-table-second-row">
                    <td><div class="purchase-table-normal-text">支払い方法</div></td>
                    <td>
                       <div id="custom-select-other-display-id" class="purchase-table-normal-text"></div>
                    </td>
                </tr>
            </table>

            <div class="purchase-form__button">
                @if($isOwner == true)
                    <div class="disabled__button">出品者は購入できません</div>
                @elseif($isOwner == false)
                    @if($isPurchased == true)
                        <div class="disabled__button">購入済み</div>
                    @elseif($isPurchased == false)
                        <button class="form__button-submit" type="submit">購入する</button>
                    @endif
                @endif
            </div>

        </div>
    </form>


@endsection
