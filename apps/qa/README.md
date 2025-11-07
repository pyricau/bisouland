# Quality Assurance

This application makes sure BisouLand follows the highest standards.
Or it will die trying.

Use GNU Make to run the project's mundane commands:

```console
# 📱 App related rules
## First install / complete reset (docker build, up)
## ⚠️ Make sure monolith is running first
make app-init

## Run full QA pipeline (dump, cs-check, static-analysis, test)
make app-qa

# 🐳 Docker related rules
## Build the Docker images
make docker-build

## Start the services (eg database, message queue, etc)
make docker-up

## Check the services logs
make docker-compose arg='logs --tail=0 --follow'

## Stop the services
make docker-down

## Open a bash shell in the container
make docker-bash

# 🐘 PHP related rules
## Install dependencies
make composer-install

## To just run php-cs-fixer check
make cs-check

## To just run php-cs-fixer fix
make cs-fix

## To just run phpstan
make static-analysis

## To just run phpunit
make test

### To display technical specifications:
make test arg='--testdox'

### To just run Smoke tests:
make test arg='./tests/Smoke'

# Discover everything you can do
make
```
