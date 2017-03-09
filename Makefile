test: vendor
	vendor/bin/phpunit

coverage: vendor
	phpdbg -qrr vendor/bin/phpunit -c phpunit.coverage.xml

lint: vendor $(shell find src)
	vendor/bin/php-cs-fixer fix

prepare: lint coverage
	composer validate --no-check-publish
	travis lint

ci: vendor lint
	vendor/bin/phpunit

.PHONY: FORCE test coverage lint prepare ci

vendor: composer.lock
	composer install

composer.lock: composer.json
	composer update

%.php: FORCE
	@php -l "$@" > /dev/null
