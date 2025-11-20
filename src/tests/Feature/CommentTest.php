<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\CommentTrait;

class CommentTest extends TestCase
{
    
    use CommentTrait;

    //コメントできたかを確認
    public function testCommentOk()
    {
        $response = $this->comment($this->commentOkKind);
    }

    //ログイン前にコメントすると、ログイン画面に遷移するかを確認
    public function testCommentBeforeLogin()
    {
        $response = $this->comment($this->commentBeforeLoginKind);
    }

    //コメントされていない場合、バリデーションエラーが発生することを確認
    public function testMissingDescription()
    {
        $response = $this->comment($this->commentMissingDescriptionKind);
    }

    //コメントが256文字以上の時、バリデーションエラーが発生することを確認
    public function testLongDescription()
    {
        $response = $this->comment($this->commentLongDescriptionKind);
    }
}
