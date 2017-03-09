SOURCE = $(shell find src test -type f)

.PHONY: test
test: | vendor
	php -c test/etc/php.ini vendor/bin/peridot

.PHONY: coverage
coverage: artifacts/tests/coverage/index.html

.PHONY: coverage-open
coverage-open: artifacts/tests/coverage/index.html
	open artifacts/tests/coverage/index.html

.PHONY: lint
lint: $(SOURCE) | vendor
	vendor/bin/php-cs-fixer fix

.PHONY: prepare
prepare: lint coverage
	composer validate
	travis lint

.PHONY: ci
ci: lint artifacts/tests/coverage/clover.xml
	php -c test/etc/php.ini -d zend.assertions=-1 vendor/bin/peridot

.PHONY: FORCE
	test coverage coverage-open lint prepare ci

.PHONY: vendor
vendor: composer.lock
	composer install

composer.lock: composer.json
	composer update

artifacts/tests/coverage/index.html: $(SOURCE) | vendor
	phpdbg -c test/etc/php.ini -qrr vendor/bin/peridot --reporter html-code-coverage --code-coverage-path=$(@D)

artifacts/tests/coverage/clover.xml: $(SOURCE) | vendor
	phpdbg -c test/etc/php.ini -qrr vendor/bin/peridot --reporter clover-code-coverage --code-coverage-path=$@

%.php: FORCE
	@php -l $@ > /dev/null
