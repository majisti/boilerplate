DC=docker-compose
RUN=$(DC) run --rm php

DC_TEST=bin/test_env.sh
RUN_TEST=$(DC_TEST) run --rm php

all: configure build start gems-install vendors-install
clean: clean-project clean-tests

configure:
	cp -n docker-compose.override.yml.dist docker-compose.override.yml

build:
	$(DC) pull
	$(DC) build

gems-install:
	$(DC) run --rm ruby bundle install

vendors-install:
	$(RUN) composer install --no-interaction --prefer-dist

vendors-update:
	$(RUN) 'php yaml-to-json.phar convert composer.yml composer.json && composer update'

start:
	$(DC) up -d

clean-project:
	$(DC) kill
	$(DC) rm -vf

clean-tests:
	$(DC_TEST) kill
	$(DC_TEST) rm -vf

test:
	$(RUN_TEST) 'bin/codecept build && bin/codecept -v run && bin/behat -vvv'

codecept:
	$(RUN_TEST) bin/codecept -v run

behat:
	$(RUN_TEST) bin/behat -vvv

cs:
	$(RUN_TEST) bin/php-cs-fixer fix --no-interaction --dry-run --diff -vvv
