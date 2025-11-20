## アプリケーション名<br>

coachtechフリマ

## 環境構築<br>

(イ) ローカルリポジトリの設定<br>
ローカルリポジトリを作成するディレクトリにおいてコマンドライン上で<br>
$ git clone git@github.com:Kitahara43965/coachtechfurima.git<br>
$ mv coachtechfurima (ローカルリポジトリ名){OS:apple}<br>
$ rename coachtechfurima (ローカルリポジトリ名){OS:windows,コマンドプロンプト}<br>
とすればリモートリポジトリのクローンが生成され、所望のローカルリポジトリ名のディレクトリが得られます。<br>
<br>
(ロ) docker の設定<br>
$ cd (ローカルリポジトリ名)<br>
docker が起動していることを確認した上で<br>
$ docker-compose up -d --build<br>
とします。<br>
$ cd docker/php<br>
$ docker-compose exec php bash<br>
で php コンテナ内に入り、<br>
$ composer install<br>
で composer をインストールします。<br>
<br>
(ハ) web アプリの立ち上げ<br>
(ハ-1) php コンテナ上で<br>
$ cp .env.example .env<br>
と入力し、.env ファイルを複製します。<br>
(ハ-2) .env ファイルで<br>
APP_LOCALE=ja {追加}<br>
DB_HOST=mysql<br>
DB_PORT=3306<br>
DB_DATABASE=laravel_db<br>
DB_USERNAME=laravel_user<br>
DB_PASSWORD=laravel_pass<br>
MAIL_FROM_ADDRESS=noreply@example.com<br>
{以下の2項目追加}
STRIPE_KEY=pk_test_51SPiHEQSyg9ASGebyCSeAmwImMCKJhyH4KA67OM2Wqiabbs1H3TD86ExSygNgoT2fHMD1M9jHrF1VobzzeU4NMCu00NTU0mHr5<br>
STRIPE_SECRET=sk_test_51SPiHEQSyg9ASGebSOYPhtVgeE0C68NJooGaV9fwasEKHSDa6WElqZqEk3lSQrIGmG9ziXbEM7J54yqZ6O1Lpd7s00CvLx32st<br>
とします。<br>
(ハ-3) php コンテナ上で<br>
$ php artisan key:generate<br>
$ php artisan migrate:fresh {もしくは $ php artisan migrate}<br>
$ php artisan db:seed<br>
と入力します。<br>
さらに、<br>
rm public/storage {既存のリンクを削除}<br>
php artisan storage:link {再度リンクの作成}<br>
をすることで web アプリを起動させることができます。<br>
<br>
(二) メール認証について<br>
(二-1) url入力欄に<br>
localhost:8025<br>
を入力するとmailhogに接続されます。
(二-2) アプリでログアウトをせずに最新の届いたメールでメール認証をするとウェブアプリ「coachtechフリマ」に戻ります。<br>
(メールに接続しない場合は「認証はこちらから」ボタンを押下すると、認証が完了します)<br>
<br>
(ホ) stripe決済画面について<br>
(ホ-1) インターネットを接続します。<br>
(ホ-2) 「コンビニ払い」を選択<br>
stripe決済画面に情報を入力せず、画面左上にある「←」(カーソルをおいたときは「←戻る」になる)を押します。商品一覧画面に戻ります<br>
stripe決済画面に情報を入力して進んだとき等はurl入力欄に「localhost」と入れれば商品一覧画面に戻ります<br>
(ホ-3) 「カード支払い」を選択<br>
stripe決済画面に移動からは以下の通り入力します。<br>
メールアドレス:xxxxxx@gmail.com<br>
支払い方法:424242424242424242<br>
MM(月)/YY(年):1234<br>
セキュリティーコード:123<br>
氏名:山田太郎<br>
支払うボタンクリック<br>
<br>
もし、入力せずに戻る場合は「コンビニ払い」同様「←」(カーソルをおいたときは「←戻る」になる)を押すともどります。<br>


テストについて<br>
(へ) php コンテナ上で<br>
$ docker-compose exec php bash<br>
$ vendor/bin/phpunit tests/Feature/(テスト用phpファイル名)<br>
と入力。例えば<br>
$ vendor/bin/phpunit tests/Feature/RegisterTest.php<br>
のように入力します<br>
テスト用phpファイル<br>
1. 会員登録機能 ー RegisterTest.php<br>
2. ログイン機能 ー LoginTest.php<br>
3. ログアウト機能 ー LogoutTest.php<br>
4. 商品一覧取得 ー IndexTest.php<br>
5. マイリスト 一 覧取得 ー MylistTest.php<br>
6. 商品検索機能 ー SearchTest.php<br>
7. 商品詳細情報取得 ー EvaluationTest.php<br>
8. いいね機能 ー FavoriteTest.php<br>
9. コメント送信機能 ー CommentTest.php<br>
10. 商品購入機能 ー PurchaseTest.php<br>
11. 支払い方法選択機能 ー PurchaseMethodSelectTest.php<br>
12. 配送先変更機能 ー AddressTest.php<br>
13. ユーザー情報取得 ー ProfileCheckTest.php<br>
14. ユーザー情報変更 ー ProfileChangeTest.php<br>
15. 出品商品情報登録 ー SellTest.php<br>
16. メール認証機能 ー MailTest.php<br>
その他<br>
初期化・シーディング・初期の画像保存テスト—InitialValueTest.php<br>
プロファイルの登録完了テスト ー ProfileTest.php<br>

## 使用技術(実行環境)<br>

php 8.1<br>
Laravel 8.83.8<br>
mysql 8.0.26<br>
nginx 1.21.1<br>
mailhog v1.0.1<br>

## ER 図<br>

<img width="1715" height="1041" alt="Image" src="https://github.com/user-attachments/assets/b9ada149-875b-4c66-a9a3-e8989145f96a" />

## URL

ホーム画面：http://localhost/<br>
ユーザー登録：http://localhost/register/<br>
phpMyAdmin: http://localhost:8080/<br>
mailhog: http://localhost:8025/<br>






