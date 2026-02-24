.PHONY: help build up down restart logs shell composer artisan migrate seed test clean

.DEFAULT_GOAL := help

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
NC := \033[0m # No Color

help: ## Show this help message
	@echo "$(BLUE)SelfTalk Server - Available Commands:$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'
	@echo ""

build: ## Build Docker containers
	@echo "$(YELLOW)Building containers...$(NC)"
	docker compose build --no-cache

up: ## Start all containers
	@echo "$(YELLOW)Starting containers...$(NC)"
	docker compose up -d
	@echo "$(GREEN)✓ Containers started successfully$(NC)"

down: ## Stop all containers
	@echo "$(YELLOW)Stopping containers...$(NC)"
	docker compose down
	@echo "$(GREEN)✓ Containers stopped$(NC)"

restart: down up ## Restart all containers

logs: ## Show logs from all containers
	docker compose logs -f

logs-app: ## Show logs from app container
	docker compose logs -f app

logs-nginx: ## Show logs from nginx container
	docker compose logs -f nginx

logs-reverb: ## Show logs from reverb container
	docker compose logs -f reverb

shell: ## Access app container shell
	docker compose exec app bash

shell-db: ## Access database shell
	docker compose exec db psql -U selftalk -d selftalk

composer: ## Run composer install
	docker compose exec app composer install

composer-update: ## Run composer update
	docker compose exec app composer update

artisan: ## Run artisan command (usage: make artisan cmd="migrate")
	docker compose exec app php artisan $(cmd)

migrate: ## Run database migrations
	@echo "$(YELLOW)Running migrations...$(NC)"
	docker compose exec app php artisan migrate
	@echo "$(GREEN)✓ Migrations completed$(NC)"

migrate-fresh: ## Drop all tables and re-run migrations
	@echo "$(YELLOW)Running fresh migrations...$(NC)"
	docker compose exec app php artisan migrate:fresh
	@echo "$(GREEN)✓ Fresh migrations completed$(NC)"

migrate-rollback: ## Rollback the last database migration
	docker compose exec app php artisan migrate:rollback

seed: ## Run database seeders
	@echo "$(YELLOW)Running seeders...$(NC)"
	docker compose exec app php artisan db:seed
	@echo "$(GREEN)✓ Seeding completed$(NC)"

migrate-seed: migrate seed ## Run migrations and seeders

fresh-seed: migrate-fresh seed ## Fresh migrations with seeders

test: ## Run tests
	docker compose exec app php artisan test

test-coverage: ## Run tests with coverage
	docker compose exec app php artisan test --coverage

cache-clear: ## Clear all cache
	@echo "$(YELLOW)Clearing cache...$(NC)"
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear
	@echo "$(GREEN)✓ Cache cleared$(NC)"

optimize: ## Optimize the application
	@echo "$(YELLOW)Optimizing application...$(NC)"
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache
	@echo "$(GREEN)✓ Application optimized$(NC)"

key-generate: ## Generate application key
	docker compose exec app php artisan key:generate

storage-link: ## Create storage symbolic link
	docker compose exec app php artisan storage:link

install: build up composer key-generate migrate-seed storage-link ## Complete installation (first time setup)
	@echo "$(GREEN)✓ Installation completed!$(NC)"
	@echo "$(BLUE)Access the application at: http://localhost:8000$(NC)"

clean: down ## Clean up containers, volumes and images
	@echo "$(YELLOW)Cleaning up...$(NC)"
	docker compose down -v
	docker system prune -f
	@echo "$(GREEN)✓ Cleanup completed$(NC)"

ps: ## Show running containers
	docker compose ps

stats: ## Show container resource usage
	docker stats

reverb-restart: ## Restart Reverb WebSocket server
	docker compose restart reverb

queue-restart: ## Restart Queue worker
	docker compose restart queue

npm-install: ## Install npm dependencies
	docker compose exec app npm install

npm-dev: ## Run npm dev
	docker compose exec app npm run dev

npm-build: ## Build frontend assets
	docker compose exec app npm run build

db-backup: ## Backup database
	@echo "$(YELLOW)Creating database backup...$(NC)"
	docker compose exec db pg_dump -U selftalk selftalk > backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✓ Database backed up$(NC)"

db-restore: ## Restore database (usage: make db-restore file=backup.sql)
	@echo "$(YELLOW)Restoring database...$(NC)"
	docker compose exec -T db psql -U selftalk selftalk < $(file)
	@echo "$(GREEN)✓ Database restored$(NC)"