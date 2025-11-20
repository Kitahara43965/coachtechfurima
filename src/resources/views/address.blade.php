@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
    <div class="address-board">
        <div class="gravity-center-child">
            <h1>住所の変更</h1>
        </div>
        <form 
            method="POST" 
            action="{{ route('purchase.address.update.item_id',['item_id'=>$item_id]) }}" 
            enctype="multipart/form-data" 
            novalidate
        >
            @csrf

            <div class="form__group">
                @php
                    if($isFilledWithProfile){
                        $profilePostcode = $authenticatedUser->postcode;
                    }else{
                        $profilePostcode = null;
                    }
                    $embeddedDeliveryPostcode = $selectedPurchaseTypedPivot?->pivot?->delivery_postcode;
                    $oldDeliveryPostcode = old('delivery_postcode');
                    $candidateDeliveryPostcode = $embeddedDeliveryPostcode ? $embeddedDeliveryPostcode : $profilePostcode;
                    $newDeliveryPostcode = $oldDeliveryPostcode ? $oldDeliveryPostcode : $candidateDeliveryPostcode;
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">郵便番号</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="delivery_postcode" value="{{ $newDeliveryPostcode }}" />
                    </div>
                    <div class="form__error">
                        @error('delivery_postcode')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                @php
                    if($isFilledWithProfile){
                        $profileAddress = $authenticatedUser->address;
                    }else{
                        $profileAddress = null;
                    }
                    $embeddedDeliveryAddress = $selectedPurchaseTypedPivot?->pivot?->delivery_address;
                    $oldDeliveryAddress = old('delivery_address');
                    $candidateDeliveryAddress = $embeddedDeliveryAddress ? $embeddedDeliveryAddress : $profileAddress;
                    $newDeliveryAddress = $oldDeliveryAddress ? $oldDeliveryAddress : $candidateDeliveryAddress;
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">住所</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="delivery_address" value="{{ $newDeliveryAddress }}" />
                    </div>
                    <div class="form__error">
                        @error('delivery_address')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__group">
                @php
                    if($isFilledWithProfile){
                        $profileBuilding = $authenticatedUser->building;
                    }else{
                        $profileBuilding = null;
                    }
                    $embeddedDeliveryBuilding = $selectedPurchaseTypedPivot?->pivot?->delivery_building;
                    $oldDeliveryBuilding = old('delivery_building');
                    $candidateDeliveryBuilding = $embeddedDeliveryBuilding ? $embeddedDeliveryBuilding : $profileBuilding;
                    $newDeliveryBuilding = $oldDeliveryBuilding ? $oldDeliveryBuilding : $candidateDeliveryBuilding;
                @endphp
                <div class="form__group-title">
                    <span class="form__label--item">建物名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="delivery_building" value="{{ $newDeliveryBuilding }}"/>
                    </div>
                    <div class="form__error">
                        @error('delivery_building')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form__button">
                @if($isOwner == true)
                    <div class="disabled__button">出品者は更新できません</div>
                @elseif($isOwner == false)
                    @if($isPurchased == true)
                        <div class="disabled__button">購入済みのため更新不可</div>
                    @elseif($isPurchased == false)
                        <button class="form__button-submit" type="submit">更新する</button>
                    @endif
                @endif
            </div>
        </form>

    </div>
@endsection
