# Parameters (optional)
# * `arg`: arbitrary arguments to pass to rules (default: none)
# * `env`: used to set `APP_ENV` (default: `test`)
arg ?=
env ?= test

# Docker containers
QA_SERVICE = app

# Executables
BENCHMARK = docker compose exec $(QA_SERVICE) php bin/benchmark.php
COMPOSER = docker compose exec $(QA_SERVICE) composer
PERFORMANCE = docker compose exec $(QA_SERVICE) php bin/performance.php
PHP_CS_FIXER = docker compose exec $(QA_SERVICE) php vendor/bin/php-cs-fixer
PHPSTAN = docker compose exec $(QA_SERVICE) php vendor/bin/phpstan
PHPUNIT = docker compose exec $(QA_SERVICE) php vendor/bin/phpunit

# Misc
.DEFAULT_GOAL = help
.PHONY: *

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
## Based on https://github.com/dunglas/symfony-docker
## (arg) denotes the possibility to pass "arg=" parameter to the target
##     this allows to add command and options, example: make composer arg='dump --optimize'
## (env) denotes the possibility to pass "env=" parameter to the target
##     this allows to set APP_ENV environment variable (default: test), example: make console env='prod' arg='cache:warmup'
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@docker compose build --pull

up: ## Starts Docker Compose services, in detached mode (no logs)
	@docker compose up --detach

logs: ## Show live logs
	@docker compose logs --tail=0 --follow

down: ## Stops Docker Compose services
	@docker compose down --remove-orphans

bash: ## Connect to the container via bash so up and down arrows go to previous commands
	@docker compose exec $(QA_SERVICE) bash

## —— Project 🐘 ———————————————————————————————————————————————————————————————
composer: ## Runs Composer (arg, eg `arg='install --optimize-autoloader'`)
	@$(COMPOSER) $(arg)

cs-check: ## Checks CS with PHP-CS-Fixer (arg, eg `arg='../monolith/web'`)
	@$(PHP_CS_FIXER) check --verbose $(arg)

static-analysis: ## Static Analysis with phpstan (arg, eg `arg='../monolith/web'`)
	@$(PHPSTAN) analyze $(arg)

test: ## Runs the tests with PHPUnit (arg, eg `arg='./tests/Monolith/Smoke'`)
	@$(PHPUNIT) $(arg)

qa: ## Equivalent to cs-check
	@$(MAKE) cs-check
	@$(MAKE) static-analysis
	@$(MAKE) test

cs-fix: ## Fixes CS with PHP-CS-Fixer (arg, eg `arg='../monolith/web'`)
	@$(PHP_CS_FIXER) fix --verbose $(arg)

## —— Performance 📊 ———————————————————————————————————————————————————————————
## To measure the performance impact of a change, follow this workflow:
## 1. run `make benchmark` (before your change)
## 2. make your change (feature, refactoring, etc)
## 3. run `make benchmark` (after your change)
## 4. run `make performance-history` to see when runs happened
## 5. run the suggested comparison command from the history output
benchmark: ## Runs performance benchmark (arg, eg `arg='20'` for 20 iterations, default: 10)
	@$(BENCHMARK) $(arg)

performance: ## Runs performance script (arg, eg `arg='metrics:report --hours=24'`)
	@$(PERFORMANCE) $(arg)

benchmark-compare: ## Compares before/after performance (arg, eg `arg='--datetime=now --hours=2 --threshold=10'`, default: now with 1-hour window, 5% threshold)
	@$(PERFORMANCE) metrics:compare $(arg)

performance-history: ## Shows benchmark run history (arg, eg `arg='--days=7'` for last week)
	@$(PERFORMANCE) metrics:history $(arg)

performance-report: ## Shows performance report (arg, eg `arg='--hours=168'` for last week)
	@$(PERFORMANCE) metrics:report $(arg)

performance-cleanup: ## Cleans up old performance metrics (arg, eg `arg='--days=30'` to keep 30 days)
	@$(PERFORMANCE) metrics:prune $(arg)
