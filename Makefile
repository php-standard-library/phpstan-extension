.PHONY: check
check: lint cs tests phpstan

.PHONY: tests
tests:
	php vendor/bin/phpunit

.PHONY: lint
lint:
	php vendor/bin/parallel-lint --colors --show-deprecated \
		src tests

.PHONY: cs
cs:
	composer install --working-dir build-cs && php build-cs/vendor/bin/phpcs

.PHONY: cs-fix
cs-fix:
	vendor/bin/phpcbf

.PHONY: phpstan
phpstan:
	php vendor/bin/phpstan analyse -l 9 -c phpstan.neon src tests
