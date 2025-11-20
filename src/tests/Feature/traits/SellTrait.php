<?php

namespace Tests\Feature\Traits;
use Tests\Feature\Traits\InitialValueTrait;
use Tests\Feature\Traits\ProfileTrait;
use Tests\Feature\Traits\PurchaseTrait;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;

trait SellTrait
{
    use InitialValueTrait,ProfileTrait,PurchaseTrait;

    protected $sellUndefinedKind = 0;
    protected $sellOkKind = 1;
    protected $sellAfterPurchaseNoTestKind = 2;

    protected $sellName = "テスト商品";
    protected $sellBrand = "テストブランド";
    protected $sellDescription = "テスト説明";
    protected $sellPrice = 1234;
    protected $sellImage = 'item1.png';

    protected $sellConditionName = "目立った傷や汚れなし";
    protected $sellCategoryName1 = "家電";
    protected $sellCategoryName2 = "コスメ";

    public function sell($sellKind)
    {
        if($sellKind === $this->sellOkKind){
            //ログイン及びプロフィール登録
            $response = $this->profile($this->profileUndefinedKind);
            $isAssertCheck = true;
        }else if($sellKind === $this->sellAfterPurchaseNoTestKind){
            //ログイン及びプロフィール登録後に商品購入済み
            $response = $this->purchase($this->purchaseUndefinedKind);
            $isAssertCheck = false;
        }else{//$sellKind
            //ログイン及びプロフィール登録
            $response = $this->profile($this->profileUndefinedKind);
            $isAssertCheck = false;
        }//$sellKind

        //商品出品画面に移動
        $response = $this->get(route('sell'));

        if($isAssertCheck){
            //移動できたかを確認
            $response->assertStatus(200);
        }//$sellKind

        // ★ Factory なしで直接 DB に作成
        $selectedCondition = Condition::create([
            'name' => $this->sellConditionName,
        ]);

        //出品する商品はカテゴリーを複数選択する
        $selectedCategories = collect([
            Category::create(['name' => $this->sellCategoryName1]),
            Category::create(['name' => $this->sellCategoryName2]),
        ]);

        $file = UploadedFile::fake()->create('dummy.png', 100, 'image/png');

        //商品の入力
        $formData = [
            'name'        => $this->sellName,
            'brand'       => $this->sellBrand,
            'description' => $this->sellDescription,
            'price'       => $this->sellPrice,
            'image' => $file,
            'condition_id'  => $selectedCondition->id,
            'category_id'  => $selectedCategories->pluck('id')->toArray(),
            'sellType' => 'createSellType',
        ];

        //商品の登録
        $response = $this->post(route('sell.store'), $formData);

        if($isAssertCheck){
            //出品時にバリデーションエラーが出ていないか確認
            $response->assertSessionDoesntHaveErrors();

            $this->assertDatabaseHas('items', [
                'name' => $this->sellName,
                'brand' => $this->sellBrand,
            ]);
        }//$sellKind

        $sellingItem = Item::where('name', $this->sellName)
                        ->where('brand', $this->sellBrand)
                        ->first();


        $sellingItemId = $sellingItem->id;
        $sellingItemImagePath = $this->itemImageDirectory.'/'.$sellingItem->image;

        if($isAssertCheck){
            //登録できているかを確認（出品した商品を保存できていることを確認）
            $this->assertDatabaseHas('items', [
                'id' => $sellingItemId,
            ]);
            $this->assertAuthenticated();
            //画像が保存されたか確認
            Storage::disk('public')->assertExists($sellingItemImagePath);

            //画像が詳細画面で表示されるか確認
            $response = $this->get(route('item.item_id', ['item_id' => $sellingItemId]));
            $response->assertStatus(200);

            $sellingPreviewUrl = asset('storage/' . $sellingItemImagePath);
            $response->assertSee($sellingPreviewUrl, false);

        }//$sellKind

        return $response;
    }
    
}