# Parameters (optional)
# * `arg`: arbitrary arguments to pass to rules (default: none)
# * `env`: used to set `APP_ENV` (default: `test`)
arg ?=
env ?= test

# Docker containers
QA_SERVICE = app

# Executables
COMPOSER = docker compose exec $(QA_SERVICE) composer
CONSOLE = docker compose exec -e APP_ENV=$(env) $(QA_SERVICE) php bin/console
PHP_CS_FIXER = docker compose exec $(QA_SERVICE) php vendor/bin/php-cs-fixer
PHPSTAN = docker compose exec $(QA_SERVICE) php vendor/bin/phpstan --memory-limit=256M
PHPUNIT = docker compose exec $(QA_SERVICE) php vendor/bin/phpunit
RECTOR = docker compose exec $(QA_SERVICE) php vendor/bin/rector
SWISS_KNIFE = docker compose exec $(QA_SERVICE) php vendor/bin/swiss-knife

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
	@docker compose exec $(QA_SERVICE) bash

## —— PHP 🐘 ———————————————————————————————————————————————————————————————————
composer: ## Runs Composer (arg, eg `arg='outdated'`)
	@$(COMPOSER) $(arg)

composer-install: ## Install dependencies (arg, eg `arg='--no-dev'`)
	@$(COMPOSER) install --optimize-autoloader $(arg)

composer-update: ## Updates dependencies (arg, eg `arg='--no-dev'`)
	@$(COMPOSER) update --optimize-autoloader $(arg)

composer-dump: ## Dumps autoloader (arg, eg `arg='--classmap-authoritative'`)
	@$(COMPOSER) dump-autoload --optimize --strict-psr --strict-ambiguous $(arg)

console: ## Runs Symfony Console (arg, env, eg `arg='cache:clear' env='prod'`)
	@$(CONSOLE) $(arg)

cs-check: ## Checks CS with PHP-CS-Fixer (arg, eg `arg='../monolith/web'`)
	@$(PHP_CS_FIXER) check --verbose $(arg)

cs-fix: ## Fixes CS with Swiss Knife and PHP-CS-Fixer
	@$(SWISS_KNIFE) namespace-to-psr-4 src --namespace-root 'Bl\\Qa\\'
	@$(SWISS_KNIFE) namespace-to-psr-4 tests --namespace-root 'Bl\\Qa\\Tests\\'
	@$(PHP_CS_FIXER) fix --verbose $(arg)

phpstan: ## Runs phpstan (arg, eg `arg='clear-result-cache'`)
	@$(PHPSTAN) $(arg)

phpstan-analyze: ## Static Analysis with phpstan (arg, eg `arg='../monolith/web'`)
	@$(PHPSTAN) analyze $(arg)

swiss-knife: ## Automated refactorings with Swiss Knife (arg, eg `arg='namespace-to-psr-4 src --namespace-root \'App\\\''`)
	@$(SWISS_KNIFE) $(arg)

phpunit: ## Runs the tests with PHPUnit (arg, eg `arg='./tests/Smoke'`)
	@$(PHPUNIT) $(arg)

rector-fix: ## Automated refactorings with Rector (arg, eg `arg='--clear-cache'`)
	@$(RECTOR) $(arg)

rector-check: ## Refactoring checks with Rector
	@$(RECTOR) process --dry-run

## —— App 📱 ———————————————————————————————————————————————————————————————————
app-init: ## First install / resetting (Docker build, up, etc)
	@echo ''
	@echo 'ℹ️ This will fail if monolith is not running'
	@echo ''
	@echo '  // Stopping QA docker services...'
	@$(MAKE) docker-down
	@echo ''
	@echo '  // Starting QA docker services...'
	@$(MAKE) docker-init
	@echo ''
	@echo '  // Installing Composer dependencies...'
	@$(MAKE) composer-install
	@echo ''
	@echo '  [OK] QA initialized'

app-qa: ## Runs full QA pipeline (composer-dump, cs-check, phpstan-analyze, rector-check, phpunit)
	@echo ''
	@echo '  // Running composer dump...'
	@$(MAKE) composer-dump
	@echo ''
	@echo '  // Running PHP CS Fixer...'
	@$(MAKE) cs-check
	@echo ''
	@echo '  // Running PHPStan...'
	@$(MAKE) phpstan
	@echo ''
	@echo '  // Running Rector...'
	@$(MAKE) rector-check
	@echo ''
	@docker compose exec $(QA_SERVICE) sh bin/sfcc-if-stale.sh test
	@echo ''
	@echo '  // Running PHPUnit...'
	@$(MAKE) phpunit
	@echo ''
	@echo '  [OK] QA done'
