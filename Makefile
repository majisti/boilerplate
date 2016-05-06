DC=docker-compose
RUN=$(DC) run --rm tools

all: configure build start install

configure:
	cp -n docker-compose.override.yml.dist docker-compose.override.yml

build:
	$(DC) pull && $(DC) build

install:
	$(RUN) composer install --no-interaction --prefer-dist

start:
	$(DC) up -d

clean:
	$(DC) kill
	$(DC) rm -vf

test:
	$(RUN) bin/codecept build
	$(RUN) bin/codecept -v run

cs:
	$(RUN_TEST) bin/php-cs-fixer fix --no-interaction --dry-run --diff -vvv
