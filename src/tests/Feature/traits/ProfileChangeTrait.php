<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\Traits\ProfileTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ProfileChangeTrait
{
    use ProfileTrait;

    protected $profileChangeUndefinedKind = 0;
    protected $profileChangeOkKind = 1;

    protected $profileChangeUsername = 'やまさん';
    protected $profileChangePostcode = '765-4321';
    protected $profileChangeAddress = '大阪府大阪市天王寺区';
    protected $profileChangeBuilding = '通天ハイツ';
    protected $profileChangeImage = 'user2.png';

    public function profileChange($profileChangeKind){

        // ユーザー・プロフィール登録
        $response = $this->profile($this->profileUndefinedKind);

        if($profileChangeKind === $this->profileChangeOkKind){
            //プロフィール画面に入る
            $response = $this->get(route('mypage.profile'));

            //プロフィール画面が有効か確認
            $response->assertStatus(200);

            //プロフィールを更新
            $formData = [
                'username' => $this->profileChangeUsername,
                'postcode' => $this->profileChangePostcode,
                'address' => $this->profileChangeAddress,
                'building' => $this->profileChangeBuilding,
                'image' => UploadedFile::fake()->create($this->profileChangeImage, 100, 'image/png'),
            ];

            $response = $this->post(route('mypage.profile.update'), $formData);

            $userBefore = Auth::user();
            $userBeforeImagePath = $this->profileUserImageDirectory.'/'.$userBefore->image;

            //バリデーションエラーなく保存できたかを確認
            $response->assertSessionDoesntHaveErrors();
            $this->assertDatabaseHas('users', [
                'username' => $userBefore->username,
            ]);
        }else{
            $userBefore = Auth::user();
            $userBeforeImagePath = $this->profileUserImageDirectory.'/'.$userBefore->image;
        }//$profileChangeKind

        //プロフィール画面に入る
        $response = $this->get(route('mypage.profile'));
        //改めてuser情報を取得します。
        $userAfter = Auth::user();
        $userAfterImagePath = $this->profileUserImageDirectory.'/'.$userAfter->image;


        if($profileChangeKind !== $this->profileChangeUndefinedKind){
            //プロフィール画面が有効か確認
            $response->assertStatus(200);

            //ユーザー名を確認
            $response->assertSee($userAfter->username);
            //住所を確認
            $response->assertSee($userAfter->address);
            //郵便番号を確認
            $response->assertSee($userAfter->postcode);
            //建物名を確認
            $response->assertSee($userAfter->building);

            //プロフィール画像がフォルダーに存在するか確認
            Storage::disk('public')->assertExists($userAfterImagePath);

            //画像を確認
            $userAfterPreviewUrl = asset('storage/' . $userAfterImagePath);
            $response->assertSee($userAfterPreviewUrl, false);

        }

        return($response);
    }

}