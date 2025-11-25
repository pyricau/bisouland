# Parameters (optional)
# * `arg`: arbitrary arguments to pass to rules (default: none)
# * `env`: used to set `APP_ENV` (default: `test`)
arg ?=
env ?= test

# Docker containers
MONOLITH_SERVICE = web

# Executables
COMPOSER = docker compose exec $(MONOLITH_SERVICE) composer

# Misc
.DEFAULT_GOAL = help
.PHONY: *

## ——  💋 The BisouLand Makefile  ——————————————————————————————————————————————
## Based on https://github.com/dunglas/symfony-docker
## (arg) denotes the possibility to pass "arg=" parameter to the target
##     this allows to add command and options, example: make composer arg='dump --optimize'
## (env) denotes the possibility to pass "env=" parameter to the target
##     this allows to set APP_ENV environment variable (default: test), example: make console env='prod' arg='cache:warmup'
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' \
		| sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
docker: ## Runs Docker (arg, eg `arg='compose logs --tail=0 --follow'`)
	@docker $(arg)

docker-compose: ## Runs Docker Compose (arg, eg `arg='logs --tail=0 --follow'`)
	@docker compose $(arg)

docker-init: ## Builds the Docker images and starts the services in detached mode (no logs)
	@docker compose build --pull
	@docker compose up --detach

docker-down: ## Stops the services
	@docker compose down --remove-orphans

docker-bash: ## Opens a (bash) shell in the container
	@docker compose exec $(MONOLITH_SERVICE) bash

## —— PHP 🐘 ———————————————————————————————————————————————————————————————————
composer: ## Runs Composer (arg, eg `arg='outdated'`)
	@$(COMPOSER) $(arg)

composer-install: ## Install dependencies (arg, eg `arg='--no-dev'`)
	@$(COMPOSER) install --optimize-autoloader $(arg)

composer-update: ## Updates dependencies (arg, eg `arg='--no-dev'`)
	@$(COMPOSER) update --optimize-autoloader $(arg)

composer-dump: ## Dumps autoloader (arg, eg `arg='--classmap-authoritative'`)
	@$(COMPOSER) dump-autoload --optimize --strict-psr --strict-ambiguous $(arg)

## —— Database ⛁ ———————————————————————————————————————————————————————————————
db-reset: ## Reset the database by dropping and recreating it with schema.sql
	@./bin/db-reset.sh

## —— App 📱 ———————————————————————————————————————————————————————————————————
app-init: ## First install / resetting (Docker build, up, etc)
	@echo '  // Stopping Monolith docker services...'
	@$(MAKE) docker-down
	@echo ''
	@echo '  // Starting Monolith docker services...'
	@$(MAKE) docker-init
	@echo ''
	@echo '  // Waiting for PostgreSQL to initialize...'
	@sleep 3
	@echo ''
	@$(MAKE) db-reset
	@echo ''
	@echo '  [OK] Monolith initialized'
