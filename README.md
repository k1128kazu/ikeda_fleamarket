# ikeda_fleamarket

Laravel 8 を用いて構築したフリマアプリケーションです。\
本 README.md は **本システムの手引書** として、

- システム概要（何をするシステムか）
- 設計の考え方（データ構造・主要処理）
- 環境構築手順（Docker）
- 起動・動作確認・テスト実行

までを **この README.md だけで完結** できるようにまとめています。

---

## 1. システム概要

本システムは、ユーザーが商品を出品・購入できるフリマアプリケーションです。

### 主な機能

- 会員登録 / ログイン（Laravel Fortify）
- 商品出品
- 商品一覧・検索
- 商品購入（Stripe 決済：カード / コンビニ）
- マイページ（出品商品 / 購入商品）
- いいね・コメント機能

決済完了後にのみ購入が確定し、商品は SOLD 状態になります。

---

## 2. 技術構成

- Framework : Laravel 8
- PHP : 8.1
- 認証 : Laravel Fortify
- 決済 : Stripe Checkout（テストモード）
- DB : MySQL
- Web : nginx / php-fpm
- 環境 : Docker / docker compose

---

## 3. ディレクトリ構成（概要）

    ikeda_fleamarket/
    ├─ docker compose.yml
    ├─ Dockerfile
    ├─ README.md
    ├─ ER図.SVG
    ├─ src/
    │  ├─ app/
    │  ├─ database/
    │  ├─ resources/
    │  ├─ routes/
    │  ├─ tests/
    │  └─ ...

---

## 4. ER図

データ構造を示す ER 図は README.md と同一ディレクトリに配置しています。

[ER図はこちら](./ERD.svg)

---

## 5. データ設計（migration の考え方）

本システムは以下のテーブルを中核として構成されています。

- **users**\
  ユーザー情報を管理します。出品者・購入者の双方を兼ねます。

- **items**\
  出品された商品を管理します。出品ユーザーに紐づき、購入確定後は SOLD
  状態になります。

- **purchases**\
  購入履歴を管理します。Stripe 決済成功後にのみ作成されます。

- **categories / category_item**\
  商品カテゴリを管理するマスタおよび中間テーブルです（多対多）。

- **comments / likes**\
  商品に対するユーザーアクションを管理します。

詳細なカラム定義は migration ファイルおよび ER 図を参照してください。

---

## 6. 購入処理の設計方針（重要）

購入確定処理は **Stripe 決済成功後のみ** 行われます。

- `store()`
  - Stripe Checkout Session の作成のみ
- `complete()`
  - 決済成功後に購入確定
  - purchases レコード作成
  - items を SOLD 状態に更新

購入確定ロジックは `complete()` に集約しています。

---

## 7. 環境構築手順

### 7.1 リポジトリの取得

    git clone <repository-url>
    cd ikeda_fleamarket

---

### 7.2 Docker コンテナ起動

    docker compose up -d --build

nginx コンテナが起動しない場合は、以下を実行してください。

    docker compose down
    docker compose up -d --build

---

### 7.3 Laravel パッケージのインストール

PHP コンテナ内で Laravel の依存パッケージをインストールします。

```bash
docker compose exec php composer install
```

※ 初回起動時は数分かかる場合があります。

---

### 7.4 .env ファイル作成

```bash
docker compose exec php cp .env.example .env
```

以下のエラーが出る場合があります。

    The stream or file "/var/www/storage/logs/laravel.log" could not be opened

その場合は、次を実行してください。

```bash
docker compose exec php chmod -R 777 storage
```

---

### 7.5 DB 設定（Docker MySQL）

`.env` に以下を設定してください。

```ini
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

---

### 7.6 アプリケーションキー生成

    docker compose exec php php artisan key:generate

---

### 7.7 マイグレーション・シーディング

```bash
docker compose exec php php artisan migrate --seed
```

※ テストデータ（migration / factory / seeder）で作成されたユーザーは
email_verified_at が未設定のため、ログイン後にメール認証が必要です。

初回表示では認証メールが送信されない場合があるため、
「認証メールを再送」ボタンを押してください。

---

### 7.8 ストレージリンク作成（画像表示に必須）

ユーザー画像・商品画像は `storage/app/public` 配下に保存されます。\
Web
から参照できるように、以下のコマンドでシンボリックリンクを作成してください。

```bash
docker compose exec php php artisan storage:link
```

これにより、

- `storage/app/public/user`
- `storage/app/public/items`

に格納された画像を、ブラウザから表示できるようになります。

---

## 8. アプリケーション起動確認

ブラウザで以下にアクセスしてください。

    http://localhost

### storage 権限エラーが出た場合

以下のエラーが表示された場合：

```
The stream or file "/var/www/storage/logs/laravel.log" could not be opened
Permission denied
```

以下を実行してください。

```
docker compose exec php chmod -R 777 storage
docker compose exec php chmod -R 777 bootstrap/cache
```

---

## 9. メール確認（MailHog）

### 追加設定：メール（MailHog）

`.env` に以下を設定してください。

```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@coachtech.local
MAIL_FROM_NAME="COACHTECH"
```

設定後は以下を実行してください。

```
docker compose restart php
```

メール送信確認用に MailHog を使用しています。

    http://localhost:8025

---

## 10. テスト環境構築およびテスト実施手順

本システムでは、本番環境とは別に **テスト専用データベース** を使用して  
Feature Test を実行します。

---

### ① テスト用データベースの準備

MySQL コンテナに入り、テスト用データベースを作成してください。

```bash
docker compose exec mysql bash
mysql -u root -p

CREATE DATABASE laravel_db_testing
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

exit
exit
```

※ `laravel_db_testing` が既に存在する場合、この作業は不要です。

---

### ② テスト用の環境設定ファイル作成・初期化

PHP コンテナに入り、`.env.testing` を作成して初期化します。

```bash
docker compose exec php bash
cp .env .env.testing
php artisan key:generate --env=testing
php artisan migrate --env=testing

# 【重要】設定キャッシュのクリア（.env.testing を確実に反映）
php artisan config:clear
php artisan cache:clear
```

※ データベースが存在していても `php artisan migrate --env=testing` の実行は必須です  
（テーブル未作成の状態ではテストは失敗します）。

---

### ③ .env.testing の設定内容確認

`.env.testing` に以下が正しく設定されていることを確認してください。

```ini
APP_ENV=testing

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db_testing
DB_USERNAME=root
DB_PASSWORD=root

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@coachtech.local
MAIL_FROM_NAME="COACHTECH"

STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

※ テスト環境では MySQL の root ユーザーを使用しています。

---

### ④ テストの実行

テストは PHP コンテナ内で実行してください。

```bash
docker compose exec php php artisan test
```

すべてのテストは `tests/Feature` 配下の Feature Test で構成されています。

---

## Stripe 決済について

本システムは Stripe Checkout を利用しています。

`.env` に以下を設定してください。

STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxx

Stripe のテストモードで発行した API キーを使用してください。

### コンビニ払い（Konbini）について

コード上では `payment_method_types: ['card', 'konbini']` を指定しています。

ただし、コンビニ払いの表示は Stripe アカウントの状態
（事業確認・本人確認・入金設定など）に依存します。

そのため、Stripe 側の設定状況によっては
Checkout 画面にクレジットカードのみ表示される場合があります。

評価時は以下を確認できれば正常動作とします：

- Stripe Checkout 画面へ遷移すること
- 決済後に購入完了画面へ戻ること
- 購入履歴が作成されること
- 商品が SOLD になること

## 11. 補足事項

- バリデーションは FormRequest を使用しています
- 動作確認後に修正を行った場合は、必ずテストを再実行してください

## 12. URL
- トップページ    http://localhost/  
- ログインページ  http://localhost/login
- 会員登録ページ  http://localhost/register
---

以上の手順に従うことで、本システムを構築・起動・動作確認できます。

```

```
