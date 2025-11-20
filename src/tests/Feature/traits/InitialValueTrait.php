<?php

namespace Tests\Feature\Traits;
use Illuminate\Support\Facades\Auth;
use \App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\BaseController;

trait InitialValueTrait
{
    use RefreshDatabase;

    protected $initialValueUndefinedKind = 0;
    protected $initialValueOkKind = 1;

    protected $itemImageDirectory = BaseController::ITEM_IMAGE_DIRECTORY;
    protected $coachtechImageDirectory = BaseController::COACHTECH_IMAGE_DIRECTORY;

    public function initialValue($initialValueKind)
    {
       $this->seed(); // DatabaseSeederを実行してデータ投入
       Storage::fake('public');

        if($initialValueKind === $this->initialValueOkKind){
            $category = \App\Models\Category::first();
            $this->assertNotNull($category);
            $item = \App\Models\Item::first();
            $this->assertNotNull($item);
            $condition = \App\Models\Condition::first();
            $this->assertNotNull($condition);
            $purchaseMethod = \App\Models\PurchaseMethod::first();
            $this->assertNotNull($purchaseMethod);

            $itemedCategory = $item->categories->first(); // 実際に結びついているカテゴリを取得
            $this->assertTrue($item->categories->contains($itemedCategory));
            $this->assertDatabaseHas('item_category', [
                'item_id' => $item->id,
                'category_id' => $itemedCategory->id,
            ]);

        }//$initialValueKind

        $response = $this->get(route('index'));

        if($initialValueKind !== $this->initialValueUndefinedKind){
            $response->assertStatus(200);
        }//$initialValueKind

        $items = Item::all();
        foreach ($items as $item) {
            $file = UploadedFile::fake()->create('dummy.png', 100, 'image/png');
            // 指定ファイル名で保存
            Storage::disk('public')->putFileAs($this->coachtechImageDirectory, $file, $item->image);

            // ファイルの存在確認
            $itemImagePath = $this->coachtechImageDirectory.'/'.$item->image;
            if($initialValueKind !== $this->initialValueUndefinedKind){
                Storage::disk('public')->assertExists($itemImagePath);
            }//$initialValueKind
        }

        return($response);
    }
}