<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\DB;
use Tests\Feature\Traits\ProfileTrait;
use Tests\Feature\Traits\LoginTrait;
use Illuminate\Support\Facades\Auth;

trait LogoutTrait
{
    protected $logoutUndefinedKind = 0;
    protected $logoutOkKind = 1;

    use ProfileTrait,LoginTrait;

    public function logout($logoutKind)
    {

        //profile入力済みであり認証済み(ログインしている)
        $response = $this->profile($this->profileUndefinedKind);

        //ログインしていることを確認
        if($logoutKind !== $this->logoutUndefinedKind){
            $this->assertAuthenticated();
        }//$logoutKind

        $response = $this->post(route('logout'), []);

        //logoutしていることを確認
        if($logoutKind !== $this->logoutUndefinedKind){
            $this->assertGuest();
        }//$logoutKind

        return($response);
    }

}