.PHONY: check
check: lint cs tests phpstan

vendor: $(wildcard composer.lock) composer.json
	composer update

build-cs/vendor:
	composer install --working-dir build-cs

.PHONY: tests
tests: vendor
	php vendor/bin/phpunit

.PHONY: lint
lint: vendor
	php vendor/bin/parallel-lint --colors --show-deprecated \
		src tests

.PHONY: cs
cs: build-cs/vendor
	php build-cs/vendor/bin/phpcs

.PHONY: cs-fix
cs-fix: build-cs/vendor
	build-cs/vendor/bin/phpcbf

.PHONY: phpstan
phpstan: vendor
	php vendor/bin/phpstan analyse -l 9 -c phpstan.neon src tests
