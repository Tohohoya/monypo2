.PHONY: help build up down restart logs clean shell migrate seed fresh test

help: ## ヘルプを表示
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Dockerイメージをビルド
	docker compose build --no-cache

up: ## コンテナを起動
	docker compose up -d
	@echo "🚀 アプリケーションが起動しました！"
	@echo "📱 アプリケーション: http://localhost:8000"
	@echo "🗄️  データベース: localhost:3306"

down: ## コンテナを停止
	docker compose down

restart: down up ## コンテナを再起動

logs: ## ログを表示
	docker compose logs -f

logs-app: ## アプリケーションのログを表示
	docker compose logs -f app

logs-db: ## データベースのログを表示
	docker compose logs -f db

shell: ## アプリケーションコンテナにシェル接続
	docker compose exec app bash

shell-db: ## データベースコンテナにシェル接続
	docker compose exec db mysql -u monypo2_user -psecret monypo2

migrate: ## マイグレーションを実行
	docker compose exec app php artisan migrate

migrate-fresh: ## マイグレーションをリセットして再実行
	docker compose exec app php artisan migrate:fresh

seed: ## データベースをシード
	docker compose exec app php artisan db:seed

fresh: ## データベースをリセットしてシード
	docker compose exec app php artisan migrate:fresh --seed
	@echo "✅ データベースをリセットしました"

test: ## テストを実行
	docker compose exec app php artisan test

tinker: ## Tinkerを起動
	docker compose exec app php artisan tinker

cache-clear: ## キャッシュをクリア
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

clean: ## コンテナとボリュームを削除
	docker compose down -v
	docker system prune -f
	@echo "✅ すべてのコンテナとボリュームを削除しました"

install: ## 依存関係をインストール
	docker compose exec app composer install
	docker compose exec app npm install

update: ## 依存関係を更新
	docker compose exec app composer update
	docker compose exec app npm update

ps: ## 実行中のコンテナを表示
	docker compose ps
