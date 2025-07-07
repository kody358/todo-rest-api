# Todo REST API

## 概要

Laravelを使用したTodo管理RESTAPIです。ユーザー認証機能とTodoのCRUD操作を提供します。

## 主な機能

- **ユーザー認証**: 新規登録・ログイン・ログアウト
- **Todo管理**: 作成・取得・更新・削除・復元
- **フィルタリング**: 完了状態による絞り込み
- **ソート**: 作成日時・更新日時・タイトル順
- **ページネーション**: 1ページ15件表示（最大100件まで設定可能）

## 技術スタック

- **言語**: PHP 8.2
- **フレームワーク**: Laravel 12
- **認証**: Laravel Sanctum（トークンベース）
- **データベース**: SQLite（デフォルト）
- **開発環境**: Docker（Laravel Sail）
- **テスト**: PHPUnit
- **API仕様**: Swagger/OpenAPI

## アーキテクチャ

### 設計パターン
- **MVC + Actionパターン**: ビジネスロジックをActionクラスに分離

### ディレクトリ構成
```
app/
├── Actions/           # ビジネスロジック
├── Http/
│   ├── Controllers/   # APIコントローラー
│   └── Requests/      # リクエストバリデーション
└── Models/           # データモデル
```

## APIエンドポイント

### 認証系
- `POST /api/register` - ユーザー登録
- `POST /api/login` - ログイン
- `POST /api/logout` - ログアウト
- `GET /api/user` - ユーザー情報取得

### Todo系（認証必須）
- `GET /api/todos` - Todo一覧取得
- `POST /api/todos` - Todo作成
- `GET /api/todos/{id}` - Todo詳細取得
- `PUT /api/todos/{id}` - Todo更新
- `DELETE /api/todos/{id}` - Todo削除
- `PATCH /api/todos/{id}/restore` - Todo復元

## 動作の流れ

1. **ユーザー登録/ログイン**
   - 新規ユーザーはメールアドレスとパスワードで登録
   - 認証トークンを発行し、以降のAPI呼び出しで使用

2. **Todo操作**
   - 認証されたユーザーのみ自分のTodoを操作可能
   - 作成・更新・削除・復元の各操作に対応
   - ソフトデリートにより削除されたTodoも復元可能

3. **データ取得**
   - ページネーション付きでTodo一覧を取得
   - 完了状態でのフィルタリング
   - 作成日時・更新日時・タイトル順でのソート

## 開発環境構築

```bash
# GitHubからプロジェクトをクローン
git clone https://github.com/kody358/todo-rest-api.git

# プロジェクトディレクトリに移動
cd todo-rest-api

# Laravel Sailの起動（初回はイメージのビルドも実行）
./vendor/bin/sail up -d

# データベースマイグレーション実行
./vendor/bin/sail artisan migrate

# 初期データの投入（任意）
./vendor/bin/sail artisan db:seed
```

開発サーバーにアクセス: `http://localhost`

## テストユーザー情報

初期データを投入した場合、以下のテストユーザーでログインできます：

- **メールアドレス**: test@example.com
- **パスワード**: password
- **名前**: テストユーザー

## テスト実行

```bash
# 全テスト実行
php artisan test
```

## API仕様書

Swagger UIでAPI仕様を確認：
- 開発環境: `http://localhost/api/documentation`
