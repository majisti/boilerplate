THIS_FILE := $(lastword $(MAKEFILE_LIST))
DC=docker-compose
PHP=$(DC) run --rm php
NODE=$(DC) run --rm node
RUBY=$(DC) run --rm ruby
COMPOSER=$(PHP) php -n -d extension=zip.so /usr/local/bin/composer

DC_TEST=bin/test_env.sh
PHP_TEST=$(DC_TEST) run --rm php

ci: all cs test
all: configure build start vendors-install ruby-install node-install assets
clean: stop
restart: stop build start
restart-test: test-stop test-start
test: test-prepare test-integration test-acceptance

configure:
	cp -n docker-compose.override.yml.dist docker-compose.override.yml

up:
	$(DC) up

start:
	$(DC) up -d

stop:
	$(DC) kill
	$(DC) rm -vf

build:
	$(DC) pull
	$(DC) build

ruby-install:
	$(RUBY) bundle install

node-install:
	$(NODE) npm install
	ln -sf ../node_modules/bower/bin/bower bin/bower
	ln -sf ../node_modules/gulp/bin/gulp.js bin/gulp

assets:
	$(NODE) bin/gulp

composer-compile:
	$(PHP) php yaml-to-json.phar convert composer.yml composer.json

vendors-install:
	$(COMPOSER) install --no-interaction --prefer-dist

vendors-update:
	$(MAKE) -f $(THIS_FILE) composer-compile
	$(COMPOSER) update

test-start:
	$(DC_TEST) up -d

test-stop:
	$(DC_TEST) kill
	$(DC_TEST) rm -vf

test-prepare:
	$(PHP_TEST) bin/codecept build

test-acceptance:
	$(PHP_TEST) bin/behat -vvv

test-integration:
	$(PHP_TEST) bin/codecept -v run

test-component:
	$(PHP_TEST) bin/codecept -v run Component

test-unit:
	$(PHP_TEST) bin/codecept -v run Unit

cs:
	$(PHP) php -n bin/php-cs-fixer fix --no-interaction --dry-run --diff -vvv

cs-fix:
	$(PHP) php -n bin/php-cs-fixer fix --no-interaction
