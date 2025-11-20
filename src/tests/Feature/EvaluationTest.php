<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Feature\Traits\EvaluationTrait;

class EvaluationTest extends TestCase
{
    use EvaluationTrait;

    //商品詳細情報取得---必要な情報が表示されているか確認
    public function testEvaluationCheck()
    {
        $response = $this->evaluation($this->evaluationCheckKind);
    }

    //商品詳細情報取得---複数選択したカテゴリが表示されているか確認
    public function testEvaluationCategoryCheck()
    {
        $response = $this->evaluation($this->evaluationCategoryCheckKind);
    }

}
