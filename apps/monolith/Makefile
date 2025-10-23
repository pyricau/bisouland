# Parameters (optional)
# * `arg`: arbitrary arguments to pass to rules (default: none)
# * `env`: used to set `APP_ENV` and docker compose files (default: `test`)
arg ?=
env ?= test

# Docker containers
LEGACY_SERVICE = web

# Docker Compose files based on environment
ifeq ($(env),test)
	COMPOSE_FILES = -f compose.yaml -f compose.test.yaml
else
	COMPOSE_FILES = -f compose.yaml
endif

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
build: ## Builds the Docker images (env)
	@docker compose $(COMPOSE_FILES) build --pull

up: ## Starts Docker Compose services, in detached mode (no logs) (env)
	@docker compose $(COMPOSE_FILES) up --detach

logs: ## Show live logs (env)
	@docker compose $(COMPOSE_FILES) logs --tail=0 --follow

down: ## Stops Docker Compose services (env)
	@docker compose $(COMPOSE_FILES) down --remove-orphans

bash: ## Connect to the container via bash so up and down arrows go to previous commands (env)
	@docker compose $(COMPOSE_FILES) exec $(LEGACY_SERVICE) bash

## —— Project 🐘 ———————————————————————————————————————————————————————————————
db-reset: ## Reset the database by dropping and recreating it with schema.sql (env, arg, eg `env=test arg='--keep-performance-metrics'`)
	@APP_ENV=$(env) ./bin/db-reset.sh $(arg)
