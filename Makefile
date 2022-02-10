.PHONY: pipeline
pipeline: | get-ready unit phpstan cs-fixer

.PHONY: get-ready
get-ready: | pull-images build reload composer-install

.PHONY: pull-images
pull-images:
	docker-compose pull

.PHONY: build
build:
	docker-compose build

.PHONY: up
up:
	docker-compose up --build -d

.PHONY: down
down:
	docker-compose down --remove-orphans

.PHONY: reload
reload: | down up

.PHONY: bash
bash:
	docker-compose exec php bash

.PHONY: interactive
interactive:
	docker-compose exec php php -a

.PHONY: composer
composer:
	docker-compose exec php composer $(args)

.PHONY: composer-install
composer-install:
	docker-compose exec php composer install

.PHONY: unit
unit:
	docker-compose exec php vendor/bin/phpunit --testsuite=unit

.PHONY: phpunit
phpunit:
	docker-compose exec php vendor/bin/phpunit $(args)

.PHONY: phpstan
phpstan:
	docker-compose exec php vendor/bin/phpstan analyze -v src
.PHONY: cs-fixer
cs-fixer:
	docker-compose exec php vendor/bin/php-cs-fixer fix -v

.PHONY: cs-fixer-dry
cs-fixer-dry:
	docker-compose exec php vendor/bin/php-cs-fixer fix -v --dry-run
