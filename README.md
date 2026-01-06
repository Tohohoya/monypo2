# Monypo2 - 子どものお手伝い管理アプリ

子どもがお手伝いをしたら報告し、大人が承認してポイントを付与する、家庭用お手伝い管理アプリケーションです。

## 機能

### 子ども向け機能
- お手伝い一覧の確認
- お手伝い完了の報告
- 完了履歴の確認（承認待ち/承認済み）
- ポイント残高の確認
- ポイントの使用（おもちゃの購入など）
- ポイント使用履歴の確認

### 大人向け機能
- お手伝いタスクの登録・管理
- 子どもからの完了報告の承認
- 子どものポイント状況の確認

## 技術スタック

- **バックエンド**: PHP Slim Framework 4.x
- **フロントエンド**: Vanilla JavaScript (ES6+)
- **データベース**: SQLite
- **スタイリング**: CSS3 (グラデーション背景、カード型UI)

## プロジェクト構成

```
monypo2/
├── backend/          # PHP Slim API
│   ├── public/
│   │   └── index.php
│   ├── database/
│   │   └── init.sql
│   └── composer.json
└── frontend/         # JavaScript フロントエンド
    ├── index.html
    ├── css/
    │   └── style.css
    └── js/
        └── app.js
```

## セットアップ手順

### 1. バックエンドのセットアップ

```bash
# バックエンドディレクトリに移動
cd backend

# Composerで依存関係をインストール
composer install

# データベースを初期化
sqlite3 database/app.db < database/init.sql
```

### 2. バックエンドサーバーの起動

```bash
# PHPビルトインサーバーで起動
cd backend/public
php -S localhost:8000
```

### 3. フロントエンドの起動

```bash
# フロントエンドディレクトリで簡易サーバーを起動
cd frontend

# Python 3の場合
python3 -m http.server 3000

# または Python 2の場合
python -m SimpleHTTPServer 3000

# またはNode.jsのhttp-serverを使う場合
npx http-server -p 3000
```

### 4. アクセス

ブラウザで http://localhost:3000 にアクセスしてください。

## デフォルトログイン情報

### 大人アカウント
- ユーザー名: `parent1`
- パスワード: `password`

### 子どもアカウント
- ユーザー名: `child1`
- パスワード: `password`

## API エンドポイント

### 認証
- **POST** `/api/auth/login` - ログイン
  - Request: `{ username, password }`
  - Response: `{ success, user, token }`

### タスク管理
- **GET** `/api/tasks` - お手伝い一覧取得
- **POST** `/api/tasks` - お手伝い作成（大人のみ）
  - Request: `{ title, description, points, created_by }`
  - Response: `{ success, id }`

### 完了報告
- **POST** `/api/tasks/{id}/complete` - お手伝い完了報告（子どものみ）
  - Request: `{ child_id }`
  - Response: `{ success, id }`
- **GET** `/api/completions` - 完了報告一覧
- **POST** `/api/completions/{id}/approve` - 完了承認（大人のみ）
  - Request: `{ approved_by }`
  - Response: `{ success }`

### ポイント管理
- **GET** `/api/points/{childId}` - ポイント残高取得
  - Response: `{ earned, used, balance }`
- **POST** `/api/points/use` - ポイント使用
  - Request: `{ child_id, points, purpose }`
  - Response: `{ success, id }`
- **GET** `/api/points/history/{childId}` - ポイント使用履歴

## データベーススキーマ

### users テーブル
- `id`: INTEGER PRIMARY KEY
- `username`: VARCHAR(50) UNIQUE
- `password`: VARCHAR(255)
- `role`: VARCHAR(20) ('adult' or 'child')
- `name`: VARCHAR(100)
- `created_at`: DATETIME

### tasks テーブル
- `id`: INTEGER PRIMARY KEY
- `title`: VARCHAR(100)
- `description`: TEXT
- `points`: INTEGER
- `created_by`: INTEGER (FK to users)
- `created_at`: DATETIME

### task_completions テーブル
- `id`: INTEGER PRIMARY KEY
- `task_id`: INTEGER (FK to tasks)
- `child_id`: INTEGER (FK to users)
- `status`: VARCHAR(20) ('pending', 'approved', 'rejected')
- `completed_at`: DATETIME
- `approved_by`: INTEGER (FK to users)
- `approved_at`: DATETIME

### point_usages テーブル
- `id`: INTEGER PRIMARY KEY
- `child_id`: INTEGER (FK to users)
- `points`: INTEGER
- `purpose`: VARCHAR(200)
- `used_at`: DATETIME

## 使い方

1. **ログイン**: デフォルトアカウントでログイン
2. **大人の場合**:
   - タスクを作成
   - 子どもからの完了報告を承認
   - 子どものポイント状況を確認
3. **子どもの場合**:
   - お手伝い一覧から実施したタスクを選んで完了報告
   - 承認されたらポイント獲得
   - ポイントを使って欲しいものをゲット

## 開発

### フロントエンドのAPI Base URL変更

`frontend/js/app.js` の先頭にある `API_BASE_URL` を環境に応じて変更してください：

```javascript
const API_BASE_URL = 'http://localhost:8000/api';
```

## ライセンス

MIT License

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

