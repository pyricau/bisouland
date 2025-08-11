# Quality Assurance

This application makes sure SkySwoon follows the highest standards.
Or it will die trying.

Use GNU Make to run the project's mundane commands:

```console
# 🐳 Docker related rules
## Build the Docker image
make build

## Start the services (eg database, message queue, etc)
make up

## Check the services logs
make logs

## Stop the services
make down

## Open interactive shell in container
make bash

# 🐘 Project related rules
## Install dependencies
make composer arg='install --optimize-autoloader'

## Run php-cs-fixer (check), phpstan and phpunit
make qa

## To just run php-cs-fixer check
make cs-check

## To just run phpstan
make static-analysis

## To just run phpunit
make test

### To display technical specifications:
make test arg='--testdox'

### To just run Smoke tests:
make test arg='./tests/Smoke'

# Run php-cs-fixer fix
make cs-fix

# Discover everything you can do
make
```
