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

## —— Apps 📱 ——————————————————————————————————————————————————————————————————
apps-init: ## First install / resetting (Docker build, up, etc)
	@$(MAKE) -C apps/monolith app-init
	@$(MAKE) -C apps/qa app-init

apps-qa: ## Runs full QA pipeline (composer-dump, cs-check, static-analysis, rector-process, test)
	@$(MAKE) -C apps/monolith app-qa
	@$(MAKE) -C apps/qa app-qa
