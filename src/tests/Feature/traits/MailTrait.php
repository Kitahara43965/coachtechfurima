<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Traits\RegisterTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use \App\Models\User;

trait MailTrait
{

    use RegisterTrait;

    protected $mailUndefinedKind = 0;
    protected $mailRegistrationKind = 1;
    protected $mailVerificationKind = 2;
    protected $mailActionToProfileKind = 3;

    public function mail($mailKind){
        Notification::fake();

        // 会員登録済み
        $response = $this->register($this->registerUndefinedKind);

        // 登録されたユーザーを取得;
        $user = Auth::user();

        if($mailKind === $this->mailRegistrationKind){
            // 会員登録されたか確認
            $this->assertDatabaseHas('users', [
                'email' => $user->email,
            ]);

            // 認証メールが送信されたか確認
            Notification::assertSentTo($user, VerifyEmail::class);
        }//mailKind

        

        if($mailKind !== $this->mailUndefinedKind){
            $this->assertAuthenticated();
            //メール認証誘導画面に入ったかを確認
            $response->assertRedirect(route('verification.notice'));

            $response = $this->get(route('verification.notice'));
            $response->assertStatus(200);

        }

        // 「認証はこちらから」ボタンを押下したことをシミュレーション
        $response = $this->actingAs($user)->post(route('verification.manual'));

         if($mailKind === $this->mailActionToProfileKind){
            // DB上で email_verified_at がセットされていることを確認(メール認証を完了)
            $this->assertNotNull($user->email_verified_at, 'ユーザーのメールが認証されていません');

            // 認証後、プロフィール画面へリダイレクトされることを確認
            $response->assertRedirect(route('mypage.profile'));
            $response = $this->get(route('mypage.profile'));
            $response->assertStatus(200);
        }//$mailKind

        return($response);
    }

}