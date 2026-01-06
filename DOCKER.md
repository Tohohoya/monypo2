# Docker Development Guide

## 概要
このドキュメントでは、Dockerを使用したMonypo2アプリケーションの開発環境のセットアップ方法を説明します。

## 前提条件
- Docker Desktop または Docker Engine がインストールされていること
- Docker Compose v2.0 以降がインストールされていること

## アーキテクチャ

```
┌─────────────────────────────────────────┐
│  Docker Compose Environment             │
├─────────────────────────────────────────┤
│                                         │
│  ┌─────────────────┐  ┌──────────────┐ │
│  │  app            │  │  db          │ │
│  │  (PHP 8.2 CLI)  │  │  (MySQL 8.0) │ │
│  │                 │  │              │ │
│  │  - Laravel      │  │  - データベース│ │
│  │  - Vite HMR     │  │  - 永続化    │ │
│  │  Port: 8000     │  │  Port: 3306  │ │
│  │  Port: 5173     │  │              │ │
│  └─────────────────┘  └──────────────┘ │
│         ↓                    ↓          │
│  ┌─────────────────┐  ┌──────────────┐ │
│  │  Volume Mounts  │  │  Volume      │ │
│  │  ./:/var/www    │  │  db-data     │ │
│  └─────────────────┘  └──────────────┘ │
└─────────────────────────────────────────┘
```

## クイックスタート

### 1. イメージのビルド
```bash
docker compose build
```

### 2. コンテナの起動
```bash
docker compose up -d
```

### 3. アプリケーションへのアクセス
- **フロントエンド**: http://localhost:8000
- **Vite HMR**: http://localhost:5173
- **データベース**: localhost:3306

### 4. ログの確認
```bash
docker compose logs -f
```

### 5. コンテナの停止
```bash
docker compose down
```

## Makefileコマンド

より簡単に操作するために、Makefileを用意しています：

```bash
make help         # ヘルプを表示
make build        # イメージをビルド
make up           # コンテナを起動
make down         # コンテナを停止
make restart      # コンテナを再起動
make logs         # ログを表示
make shell        # アプリケーションコンテナに接続
make migrate      # マイグレーション実行
make fresh        # データベースをリセット
make test         # テストを実行
make clean        # 完全にクリーンアップ
```

## 初回セットアップの流れ

コンテナを起動すると、以下の処理が自動的に実行されます：

1. **依存関係のインストール** (初回のみ)
   - Composer パッケージのインストール
   - NPM パッケージのインストール

2. **データベースの準備**
   - MySQLコンテナの起動待機
   - 接続確認（最大30回リトライ）

3. **環境設定**
   - `.env` ファイルの作成（存在しない場合）
   - データベース接続情報の自動設定
   - アプリケーションキーの生成

4. **データベースのセットアップ**
   - マイグレーションの実行
   - シードの実行（`DB_SEED=true`の場合）

5. **アプリケーションの起動**
   - Laravelサーバーの起動 (port 8000)
   - Vite開発サーバーの起動 (port 5173)

## 開発ワークフロー

### コードの変更
ホストマシンでファイルを編集すると、以下が自動的に反映されます：
- PHPコードの変更: リクエスト時に自動反映
- フロントエンドコード: Vite HMRにより即座に反映

### データベース操作

**マイグレーションの実行:**
```bash
docker compose exec app php artisan migrate
# または
make migrate
```

**マイグレーションのリセット:**
```bash
docker compose exec app php artisan migrate:fresh
# または
make fresh
```

**データのシード:**
```bash
docker compose exec app php artisan db:seed
# または
make seed
```

**Tinkerの起動:**
```bash
docker compose exec app php artisan tinker
# または
make tinker
```

### デバッグ

**アプリケーションコンテナに接続:**
```bash
docker compose exec app bash
# または
make shell
```

**データベースに接続:**
```bash
docker compose exec db mysql -u monypo2_user -psecret monypo2
# または
make shell-db
```

**ログの確認:**
```bash
# すべてのログ
docker compose logs -f

# アプリケーションのみ
docker compose logs -f app

# データベースのみ
docker compose logs -f db
```

## トラブルシューティング

### ポートが既に使用されている

**エラーメッセージ:**
```
Error starting userland proxy: listen tcp4 0.0.0.0:8000: bind: address already in use
```

**解決方法:**
`docker-compose.yml` のポート番号を変更してください：
```yaml
services:
  app:
    ports:
      - "8080:8000"  # 8000 → 8080 に変更
      - "5174:5173"  # 5173 → 5174 に変更
```

### データベース接続エラー

**エラーメッセージ:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**解決方法:**
1. データベースコンテナが起動しているか確認:
   ```bash
   docker compose ps
   ```

2. データベースログを確認:
   ```bash
   docker compose logs db
   ```

3. コンテナを再起動:
   ```bash
   docker compose restart
   ```

### 依存関係のエラー

**症状:**
- Composerパッケージが見つからない
- NPMパッケージが見つからない

**解決方法:**
依存関係を再インストール:
```bash
docker compose exec app composer install
docker compose exec app npm install
# または
make install
```

### キャッシュのクリア

**症状:**
- 設定変更が反映されない
- ルートが見つからない

**解決方法:**
```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear
# または
make cache-clear
```

### 完全リセット

すべてをクリーンアップして最初からやり直す場合:
```bash
make clean
make build
make up
```

## 環境変数

デフォルトの環境変数は `docker-compose.yml` で設定されています：

```yaml
environment:
  - APP_ENV=local
  - APP_DEBUG=true
  - DB_CONNECTION=mysql
  - DB_HOST=db
  - DB_PORT=3306
  - DB_DATABASE=monypo2
  - DB_USERNAME=monypo2_user
  - DB_PASSWORD=secret
```

カスタム環境変数を追加する場合は、`docker-compose.yml` を編集するか、
`docker-compose.override.yml` を作成してください。

## パフォーマンス最適化

### Composer/NPMキャッシュの利用

依存関係はDockerボリュームに保存されるため、コンテナを再起動しても
再インストールは不要です：

```yaml
volumes:
  - ./:/var/www/html
  - /var/www/html/vendor      # Composerキャッシュ
  - /var/www/html/node_modules # NPMキャッシュ
```

### データベースの永続化

データベースはDockerボリュームに保存され、コンテナを削除しても
データは保持されます：

```yaml
volumes:
  db-data:
    driver: local
```

データを完全に削除する場合:
```bash
docker compose down -v
# または
make clean
```

## 本番環境との違い

この Docker 構成は **開発環境専用** です。以下の点に注意してください：

- ソースコードはホストとマウント共有（本番環境ではイメージに含める）
- デバッグモードが有効（`APP_DEBUG=true`）
- 開発用サーバーを使用（本番環境では Nginx + PHP-FPM）
- セキュリティが緩い設定（本番環境では厳格な設定が必要）

## 参考リンク

- [Laravel公式ドキュメント](https://laravel.com/docs)
- [Docker公式ドキュメント](https://docs.docker.com/)
- [Docker Compose公式ドキュメント](https://docs.docker.com/compose/)
