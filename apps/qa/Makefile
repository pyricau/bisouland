# Parameters (optional)
# * `arg`: arbitrary arguments to pass to rules (default: none)
# * `env`: used to set `APP_ENV` (default: `test`)
arg ?=
env ?= test

# Docker containers
QA_SERVICE = app

# Executables
COMPOSER = docker compose exec $(QA_SERVICE) composer
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

test: ## Runs the tests with PHPUnit (arg, eg `arg='./tests/Smoke'`)
	@$(PHPUNIT) $(arg)

qa: ## Equivalent to cs-check
	@$(MAKE) cs-check
	@$(MAKE) static-analysis
	@$(MAKE) test

cs-fix: ## Fixes CS with PHP-CS-Fixer (arg, eg `arg='../monolith/web'`)
	@$(PHP_CS_FIXER) fix --verbose $(arg)
