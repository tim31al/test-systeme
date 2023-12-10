##################
# Variables
##################

DOCKER_COMPOSE = docker compose
DC_PHP = ${DOCKER_COMPOSE} exec -u app app
DC_PHP_NO_DEBUG = ${DC_PHP} php -d xdebug.mode=debug
DB_VOLUME = data_test_payments

help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[32m%-14s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

##################
# Docker compose
##################
build: mk_volume docker_build ## Docker build

up: ## Docker up
	${DOCKER_COMPOSE} up -d

logs: ## Docker logs
	${DOCKER_COMPOSE} logs -f

down: ## Docker stop
	${DOCKER_COMPOSE} down

destroy: ## Docker stop and remove
	${DOCKER_COMPOSE} down -v --remove-orphans

mk_volume: ## Create db_volume
	docker volume inspect ${DB_VOLUME} > /dev/null 2>&1 || docker volume create ${DB_VOLUME}

rm_volume: ## Remove db_volume
	docker volume rm ${DB_VOLUME}

docker_build:
	${DOCKER_COMPOSE} build


##################
# App
##################
init: install db_init ## Инициализация приложения

db_init: ## Load fixtures
	${DC_PHP_NO_DEBUG} bin/console doctrine:migrations:migrate -q
	${DC_PHP_NO_DEBUG} bin/console app:load-fixtures

install: ## Composer install
	${DC_PHP} composer install

shell: ## php shell
	${DC_PHP} bash

cache: ## php clear cache
	${DC_PHP} php bin/console cache:clear

tests: test-init test-all ## Run test init and all tests

test-all: ## Run all tests
	${DC_PHP_NO_DEBUG} bin/phpunit tests

test-cov: ## Run tests with HTML coverage
	${DC_PHP} php -d xdebug.mode=coverage ./vendor/bin/phpunit tests --coverage-html var/coverage/ --coverage-cache var/cache/coverage/

test-cache: ## php clear cache
	${DC_PHP} rm -fr var/cache/test*
	${DC_PHP} php bin/console cache:clear --env=test

test-add: ## Добавить тест
	${DC_PHP} php bin/console make:test

test-init: test-db-drop test-db-create test-db-schema test-fixtures ## Test init db

test-db-drop: ## Test drop database
	${DC_PHP_NO_DEBUG} bin/console --env=test doctrine:database:drop --if-exists --force

test-db-create: ## Test create database
	${DC_PHP_NO_DEBUG} bin/console --env=test doctrine:database:create

test-db-schema: ## Test schema create
	${DC_PHP_NO_DEBUG} bin/console --env=test doctrine:schema:create -q

test-fixtures: ## Test load fixtures
	${DC_PHP_NO_DEBUG} bin/console --env=test app:load-fixtures -q


##################
# Code analysis
##################
phpstan: ## Run phpstan
	${DC_PHP} env APP_DEBUG=false XDEBUG_MODE=off composer run phpstan

cs-fix: ## Run php-cs-fixer
	${DC_PHP_NO_DEBUG} vendor/bin/php-cs-fixer fix

