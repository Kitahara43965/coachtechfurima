<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Traits\MailTrait;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BaseController;

trait ProfileTrait
{
    use MailTrait;

    protected $profileUndefinedKind = 0;
    protected $profileOkKind = 1;

    protected $profileUsername = '山ちゃん';
    protected $profilePostcode = '123-4567';
    protected $profileAddress = '東京都千代田区';
    protected $profileBuilding = '富士見ハイツ';
    protected $profileImage = 'user1.png';

    protected $profileUserImageDirectory = BaseController::USER_IMAGE_DIRECTORY;


    public function profile($profileKind){
        
        // ユーザー登録
        $response = $this->mail($this->mailUndefinedKind);

        $file = UploadedFile::fake()->create($this->profileImage, 100, 'image/png');

        $formData = [
            'username' => $this->profileUsername,
            'postcode' => $this->profilePostcode,
            'address' => $this->profileAddress,
            'building' => $this->profileBuilding,
            'image' => $file,
        ];

        $response = $this->post(route('mypage.profile.update'), $formData);
        $user = Auth::user();

        $profileUserImagePath = $this->profileUserImageDirectory.'/'.$user->image;

        if($profileKind !== $this->profileUndefinedKind){
            //画像ファイルが存在するか確認
            Storage::disk('public')->assertExists($profileUserImagePath);
        }//$profileKind

        //バリデーションエラーが出ないかを確認
        if($profileKind === $this->profileOkKind){
            $response->assertSessionDoesntHaveErrors();
            $this->assertDatabaseHas('users', [
                'username' => $user->username,
            ]);
        }//$profileKind

        return($response);
    }

}