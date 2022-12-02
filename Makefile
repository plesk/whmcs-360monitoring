.PHONY: help
help: ## Shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: init
init: composer-install-app composer-install-dev-ops ## Install composer dependencies

.PHONY: update
update: composer-update-app composer-update-dev-ops ## Update composer dependencies

.PHONY: composer-install-app
composer-install-app:
	composer install

.PHONY: composer-update-app
composer-update-app:
	composer update

.PHONY: composer-install-dev-ops
composer-install-dev-ops:
	composer install -d ./dev-ops/ci

.PHONY: composer-update-dev-ops
composer-update-dev-ops:
	composer update -d ./dev-ops/ci

.PHONY: phpstan
phpstan:
	php -d memory_limit=-1 dev-ops/ci/vendor/bin/phpstan analyse -c dev-ops/ci/config/phpstan.neon
