SHELL = /bin/sh

UID := $(shell id -u)
GID := $(shell id -g)

export UID
export GID

.PHONY: pipeline
pipeline: | get-ready unit phpstan cs-fixer

.PHONY: get-ready
get-ready: | pull-images build reload composer-install

.PHONY: pull-images
pull-images:
	docker compose pull

.PHONY: build
build:
	docker compose build

.PHONY: up
up:
	docker compose up --build -d

.PHONY: down
down:
	docker compose down --remove-orphans

.PHONY: reload
reload: | down up

.PHONY: bash
bash:
	docker compose exec php bash

.PHONY: logs
logs:
	docker compose logs -f

.PHONY: interactive
interactive:
	docker compose exec php php -a

.PHONY: composer
composer:
	docker compose exec php composer $(args)

.PHONY: composer-install
composer-install:
	docker compose exec php composer install

.PHONY: npm-install
npm-install:
	docker compose run --rm node npm install

.PHONY: npm-update
npm-update:
	docker compose run --rm node npm update

vendor/autoload.php:
	$(MAKE) get-ready

.PHONY: unit
unit: vendor/autoload.php
	docker compose exec php vendor/bin/phpunit --testsuite=unit

.PHONY: phpunit
phpunit: vendor/autoload.php
	docker compose exec php vendor/bin/phpunit $(args)

.PHONY: behat
behat: vendor/autoload.php
	docker compose exec php vendor/bin/behat $(args)

.PHONY: phpstan
phpstan: vendor/autoload.php
	docker compose exec php vendor/bin/phpstan analyze -v

.PHONY: rector
rector: vendor/autoload.php
	docker compose exec php vendor/bin/rector $(args)

.PHONY: cs-fixer
cs-fixer: vendor/autoload.php
	docker compose exec php vendor/bin/php-cs-fixer fix -v

.PHONY: cs-fixer-dry
cs-fixer-dry: vendor/autoload.php
	docker compose exec php vendor/bin/php-cs-fixer fix -v --dry-run
