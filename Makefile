# https://clarkgrubb.com/makefile-style-guide#phony-targets

DOCKER_BUILD_VARS := COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1
DOCKER_BUILD := ${DOCKER_BUILD_VARS} docker build

COMPOSE := $(DOCKER_BUILD_VARS) docker compose

.env:
	cp .env.dist .env

build:
	${COMPOSE} pull --ignore-pull-failures --include-deps
	${COMPOSE} build

setup: .env build
	${COMPOSE} run --rm php composer install

start:
	${COMPOSE} up -d

stop:
	${COMPOSE} down

destroy: stop
	${COMPOSE} rm --force --stop -v

test-phpunit:
	${COMPOSE} run --rm php php bin/phpunit

bash:
	${COMPOSE} run php bash

phpstan:
	${COMPOSE} run php vendor/bin/phpstan analyse src tests

phpfixer:
	${COMPOSE} run php vendor/bin/php-cs-fixer fix --config=/app/.php-cs-fixer.php

