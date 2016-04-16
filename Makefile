FIG=docker-compose
RUN=$(FIG) run --rm tools

all: build start install

build:
	$(FIG) pull && $(FIG) build

install:
	$(RUN) composer install --no-interaction --prefer-dist

start:
	$(FIG) up -d

clean:
	$(FIG) kill
	$(FIG) rm -vf

cs:
	$(RUN_TEST) bin/php-cs-fixer fix --no-interaction --dry-run --diff -vvv
