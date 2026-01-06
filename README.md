# Monypo2 - お手伝い管理アプリ

家族のお手伝いを管理し、ポイントでご褒美を交換できるアプリケーションです。

## Docker での起動方法

### 必要なもの
- Docker
- Docker Compose

### クイックスタート

1. **リポジトリをクローン**
```bash
git clone https://github.com/Tohohoya/monypo2.git
cd monypo2
```

2. **Dockerコンテナを起動**
```bash
docker compose up -d
```

3. **アプリケーションにアクセス**
- アプリケーション: http://localhost:8000
- データベース: localhost:3306

初回起動時は自動的にデータベースのマイグレーションが実行されます。

### 便利なコマンド（Makefileを使用）

```bash
make up          # コンテナ起動
make down        # コンテナ停止
make restart     # コンテナ再起動
make logs        # ログ表示
make shell       # アプリケーションコンテナに接続
make migrate     # マイグレーション実行
make fresh       # データベースをリセット
make test        # テスト実行
make clean       # 完全クリーンアップ
```

### Docker なしでの起動方法

```bash
# 依存関係のインストール
composer install
npm install

# 環境設定
cp .env.example .env
php artisan key:generate

# データベースのセットアップ
php artisan migrate
php artisan db:seed

# 開発サーバーの起動
php artisan serve &
npm run dev
```

アプリケーションは http://localhost:8000 でアクセスできます。

### トラブルシューティング

**ポートが既に使用されている場合:**
`docker-compose.yml` のポート番号を変更してください。

**データベースをリセットしたい場合:**
```bash
make fresh
# または
docker compose exec app php artisan migrate:fresh --seed
```

**ログを確認したい場合:**
```bash
make logs
# または個別に
docker compose logs app
docker compose logs db
```

**依存関係の再インストール:**
```bash
make install
# または
docker compose exec app composer install
docker compose exec app npm install
```

## 機能

- 👨‍👩‍👧‍👦 親と子どもの役割管理
- 📝 お手伝いの作成と管理
- ✅ お手伝いの完了承認
- 🎁 ポイント制ご褒美システム
- 💰 ポイント交換申請と承認

---

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
