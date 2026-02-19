# Quality Assurance

This application makes sure BisouLand follows the highest standards.
Or it will die trying.

It covers all `apps/` and all `packages/`.
See [How to run QA](../../docs/how-to/003-how-to-run-qa.md) for details.

Use GNU Make to run the project's mundane commands:

```console
# 📱 App related rules
## First install / complete reset (docker build, up)
## ⚠️ Make sure monolith is running first
make app-init

## Run full QA pipeline (cs-check, phpstan-analyze, rector-check, phpunit)
make app-qa

# 🐳 Docker related rules
## Build the Docker images and start the services
make docker-init

## Check the services logs
make docker-compose arg='logs --tail=0 --follow'

## Stop the services
make docker-down

## Open a bash shell in the container
make docker-bash

# 🐘 PHP related rules
## To just run composer
make composer

### To dump the autoloader (also checks for PSR-4 alignment)
make composer-dump

### To install dependencies
make composer-install

### To update dependencies
make composer-update

### To install new package
make composer arg='require gnugat/redaktilo'

## To just run php-cs-fixer check
make cs-check

## To just run php-cs-fixer fix (also runs Swiss Knife for PSR-4 alignment)
make cs-fix

## To just run phpstan
make phpstan-analyze

## To just run rector check
make rector-check

## To just run rector fix
make rector-fix

## To just run phpunit
make phpunit

### To display technical specifications:
make phpunit arg='--testdox'

### To run a specific test suite:
make phpunit arg='--testsuite packages-spec'
make phpunit arg='--testsuite qalin-spec'
make phpunit arg='--testsuite qalin-integration'
make phpunit arg='--testsuite monolith-smoke'

# Discover everything you can do
make
```
