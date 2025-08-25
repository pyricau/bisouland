# Parameters (optional)
# * `arg`: arbitrary arguments to pass to rules (default: none)
# * `env`: used to set `APP_ENV` (default: `test`)
arg ?=
env ?= test

# Docker containers
LEGACY_SERVICE = web

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
	@docker compose exec $(LEGACY_SERVICE) bash

## —— Project 🐘 ———————————————————————————————————————————————————————————————
db-reset: ## Reset the database by dropping and recreating it with schema.sql
	@./bin/db-reset.sh
