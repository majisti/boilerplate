ENV?=local

ifeq ($(ENV), local)
	SYMFONY_ENV=dev
else ifeq ($(ENV), test)
	SYMFONY_ENV=test
else ifeq ($(ENV), demo)
	SYMFONY_ENV=prod
else
	SYMFONY_ENV=$(ENV)
endif

DIRECTORY_NAME := $(shell pwd | xargs basename | tr -cd 'A-Za-z0-9_-')
DOCKER_HOST_IP := $(shell docker-machine ip 2> /dev/null)
THIS_FILE := $(lastword $(MAKEFILE_LIST))

DC?=docker-compose \
	-p $(ENV)_$(DIRECTORY_NAME) \
    -f docker/docker-compose.yml \
    -f docker/docker-compose.$(ENV).yml \
    -f docker/docker-compose.override.yml

PHP=$(DC) run --rm php
NODE=$(DC) run --rm node
RUBY=$(DC) run --rm ruby
COMPOSER?=$(PHP) php -n -d extension=zip.so -d memory_limit=-1 composer.phar

ci: all lint test
all: configure build composer-install vendors-install backend-assets node-install frontend-assets restart
clean: stop
restart: stop start
restart-test: test-stop test-start
test: test-prepare test-integration test-acceptance

init-nginx-proxy:
	docker stop nginx-proxy || true && docker rm nginx-proxy || true
	docker run --name=nginx-proxy -d -p $(DOCKER_HOST_IP):80:80 -v /var/run/docker.sock:/tmp/docker.sock:ro jwilder/nginx-proxy

#cleans the containers for all environments
clean-all:
	$(eval $@_NAME := $(shell echo $(DIRECTORY_NAME) | tr -d '_-'))
	docker stop $(shell docker ps -a -q --filter="name=$($@_NAME)")
	docker rm -vf $(shell docker ps -a -q --filter="name=$($@_NAME)")

#removes ALL containers, not just does under this project
clean-docker:
	docker kill $(docker ps -a -q)
	docker rm $(docker ps -a -q)

configure:
	cp -n docker/docker-compose.override.yml.dist docker/docker-compose.override.yml

ps:
	$(DC) ps

logs:
	$(DC) logs

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

console:
	$(PHP) php bin/console

node-install:
	$(NODE) yarn install --no-bin-links

frontend-assets:
	$(NODE) node_modules/gulp/bin/gulp.js

frontend-assets-watch:
	$(NODE) node_modules/gulp/bin/gulp.js watch

backend-assets:
	$(PHP) bin/console assets:install web --symlink

composer-install:
	$(PHP) bash -c 'if [ -f composer.phar ]; then echo "updating composer..." && php composer.phar self-update; else echo "installing composer..." && curl -s http://getcomposer.org/installer | php; fi'

composer-compile:
	$(PHP) php yaml-to-json.phar convert composer.yml composer.json

vendors-install:
	$(COMPOSER) install --no-interaction --prefer-dist

vendors-update:
	$(MAKE) -f $(THIS_FILE) composer-compile
	$(COMPOSER) update

test-prepare:
	$(PHP) bin/codecept build

test-acceptance:
	$(PHP) bin/behat -vvv

test-acceptance-firefox:
	$(PHP) bin/behat -vvv -p firefox

test-acceptance-chrome:
	$(PHP) bin/behat -vvv -p chrome

test-integration:
	$(PHP) bin/codecept -v run

test-component:
	$(PHP) bin/codecept -v run Component

test-unit:
	$(PHP) bin/codecept -v run Unit

lint:
	$(PHP) php -n vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --no-interaction --dry-run --diff -vvv

lint-fix:
	$(PHP) php -n vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --no-interaction
