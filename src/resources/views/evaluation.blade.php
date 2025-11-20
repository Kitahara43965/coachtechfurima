@extends('layouts.app')


@section('css')
    <link rel="stylesheet" href="{{ asset('css/evaluation.css') }}">
@endsection


@section('content')

    <div class="evaluation-board">

        <div class="evaluation-left-block">
            <div>
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

                <div class="evaluation-custom-file">
                    <div class="evaluation-item-image-container">
                        <img id="preview"
                            src="{{$newPreviewUrl}}"
                            class="evaluation-item-image">
                    </div>
                </div>
            </div>
        </div>


        <div class="evaluation-right-block">
            <h1>{{$selectedItem->name}}</h1>
            <div class="evaluation-brand">{{$selectedItem->brand}}</div>
            <div class="evaluation-price-container">
                <div class="evaluation-price-sign">¥</div>
                <div class="evaluation-price-value">{{number_format($selectedItem->price) }}</div>
                <div class="evaluation-price-tax">(税込)</div>
            </div>

            <table class="item-table">
                <tr>
                    <th>
                        <form action="{{ route('item.item_id.favorite',['item_id' => $item_id]) }}" method="POST" novalidate>
                            @csrf
                            @if(auth()->user() && $selectedItem->isFavoritedBy(auth()->user()))
                                <button type="submit" class="svg-heart">
                                    {!! file_get_contents(storage_path('app/public/svg/filled-heart.svg')) !!}
                                </button>
                            @else
                                <button type="submit" class="svg-heart">
                                    {!! file_get_contents(storage_path('app/public/svg/heart.svg')) !!}
                                </button>
                            @endif
                        </form>
                    </th>
                    <th class="svg-balloon">
                        {!! file_get_contents(storage_path('app/public/svg/balloon.svg')) !!}
                    </th>
                </tr>
                <tr>
                    <td class="comment-count">
                        {{ $selectedItem->favoritesCount() }}
                    </td>
                    <td class="favorite-count">
                        {{ $selectedItemCommentNumber }}
                    </td>
                </tr>
            </table>

            <div>
                @if($authenticatedUserIdCoincidence)
                    <button
                        type="button"
                        class="evaluation-form__button-submit"
                        onclick="window.location='{{ route('item.edit.item_id',['item_id' => $item_id]) }}'">
                        編集
                    </button>
                @else
                    @if($isPurchased)
                        <div
                            class="evaluation-disabled-button">
                            購入済み
                        </div>
                    @else
                        <button
                            type="button"
                            class="evaluation-form__button-submit"
                            onclick="window.location='{{ route('purchase.item_id',['item_id' => $item_id]) }}'">
                            購入手続きへ
                        </button>
                    @endif
                @endif
            </div>
            <h2>
                商品説明
            </h2>
            <div>
                {!! nl2br(e($selectedItem->description)) !!}
            </div>
            <h2>
                商品の情報
            </h2>

            <div class="evaluation-detail-column-grid">

                <div class="evaluation-label">カテゴリー</div>
                <div class="evaluation-value">
                    @foreach ($selectedCategories as $selectedCategory)
                        @if(in_array($selectedCategory->id, $selectedCategoryIds))
                            <span class="evaluation-category-tag">{{ $selectedCategory->name }}</span>
                        @endif
                    @endforeach
                </div>

                <div class="evaluation-label">商品の状態</div>
                <div class="evaluation-value">
                    @if ($selectedCondition)
                        <span>{{ $selectedCondition->name }}</span>
                    @endif
                </div>

            </div>


            <h2 class="gray-text-color">
                コメント({{$selectedItemCommentNumber}})
            </h2>
            <div>

                @foreach($selectedItem->comments as $comment)
                    <div class="evaluation-commentator-container">

                        <div class="evaluation-commentator-image">
                            @php
                                $candidateNewImageName = $comment->user->image;
                                if($candidateNewImageName){
                                    $newImageName = $candidateNewImageName;
                                    $newPreviewUrl = asset('storage/'.$userImageDirectory.'/'.$newImageName);
                                }else{//$candidateNewImageName
                                    $newImageName = null;
                                    $newPreviewUrl = null;
                                }//$candidateNewImageName
                            @endphp

                            <div class="evaluation-user-image-container">
                                <img id="preview"
                                    src="{{$newPreviewUrl ?? $defaultProfilePreviewUrl}}"
                                    class="evaluation-user-image">
                            </div>
                        </div>
                        <div class="evaluation-commentator-user">
                            {{ $comment->user->username }}
                        </div>
                    </div>

                    <div class="old-comments">
                        <div>{!! nl2br(e($comment->description)) !!}</div>
                    </div>
                @endforeach
            <div>

            <div>
                <h3>
                    商品へのコメント
                </h3>
                <div>
                    <form action="{{ route('item.item_id.comments', ['item_id' => $item_id]) }}" method="POST" novalidate>
                        @csrf
                        <div>
                            <textarea
                                name="description"
                                rows="3"
                                class="new-comment"
                                required>{{ old('description') }}</textarea>
                        </div>
                        <div>

                            <button
                                type="submit"
                                class="evaluation-form__button-submit">
                                コメントを投稿
                            </button>
                        </div>
                        <div class="form__error">
                            @error('description')
                                {{ $message }}
                            @enderror
                        </div>
                        <div class="evaluation-lower-comment-button-blank"></div>
                    </form>
                </div>
            </div>
        </div>
        


    </div>

@endsection