DC=docker-compose
PHP=$(DC) run --rm php
NODE=$(DC) run --rm node
RUBY=$(DC) run --rm ruby

DC_TEST=bin/test_env.sh
PHP_TEST=$(DC_TEST) run --rm php

all: configure build start vendors-install ruby-install node-install
clean: stop
restart: stop start

configure:
	cp -n docker-compose.override.yml.dist docker-compose.override.yml

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

node-gulp:
	$(NODE) bin/gulp

vendors-install:
	$(PHP) composer install --no-interaction --prefer-dist

vendors-update:
	$(PHP) 'php yaml-to-json.phar convert composer.yml composer.json && composer update'

test:
	$(PHP_TEST) 'bin/codecept build && bin/codecept -v run && bin/behat -vvv'

codecept:
	$(PHP_TEST) bin/codecept -v run

behat:
	$(PHP_TEST) bin/behat -vvv

cs:
	$(PHP_TEST) bin/php-cs-fixer fix --no-interaction --dry-run --diff -vvv
