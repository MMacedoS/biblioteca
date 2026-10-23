DC      = docker compose
PHP     = $(DC) exec php
CONSOLE = $(PHP) bin/console

export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

.PHONY: build up down sh logs composer console

build:
	$(DC) build

up:
	$(DC) up -d

down:
	$(DC) down

sh:
	$(PHP) bash

logs:
	$(DC) logs -f

composer:
	$(PHP) composer $(c)

console:
	$(CONSOLE) $(c)

migrate:
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

test:
	$(PHP) bin/phpunit

cc:
	$(CONSOLE) cache:clear

.PHONY: build up down sh logs composer console migrate test cc

test-db:
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --env=test

cs:
	$(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix:
	$(PHP) vendor/bin/php-cs-fixer fix

seed:
	docker compose exec -T -e MYSQL_PWD=app database mysql -uapp biblioteca < database/scripts/03_dados_demonstracao.sql

setup: build up
	$(PHP) composer install
	$(CONSOLE) doctrine:migrations:migrate --no-interaction
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --env=test
