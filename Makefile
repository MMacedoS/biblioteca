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