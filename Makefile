##################
# Variables
##################

DOCKER_COMPOSE = docker compose
DC_PHP = ${DOCKER_COMPOSE} exec -u app app
DC_PHP_NO_DEBUG = ${DC_PHP} php -d xdebug.mode=debug

help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[32m%-14s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

##################
# Docker compose
##################
build: ## Docker build
	${DOCKER_COMPOSE} build

up: ## Docker up
	${DOCKER_COMPOSE} up -d

logs: ## Docker logs
	${DOCKER_COMPOSE} logs -f

down: ## Docker stop
	${DOCKER_COMPOSE} down

destroy: ## Docker stop and remove
	${DOCKER_COMPOSE} down -v --rmi=all --remove-orphans


##################
# App
##################
shell: ## php shell
	${DC_PHP} bash

cache: ## php clear cache
	${DC_PHP} php bin/console cache:clear

test: ## Run all tests
	${DC_PHP_NO_DEBUG} bin/phpunit tests

test-cov: ## Run tests with HTML coverage
	${DC_PHP} php -d xdebug.mode=coverage ./vendor/bin/phpunit tests --coverage-html var/coverage/ --coverage-cache var/cache/coverage/

test-cache: ## php clear cache
	${DC_PHP} rm -fr var/cache/test*
	${DC_PHP} php bin/console cache:clear --env=test

test-add: ## Добавить тест
	${DC_PHP} php bin/console make:test


##################
# Code analysis
##################
phpstan: ## Run phpstan
	${DC_PHP} env APP_DEBUG=false XDEBUG_MODE=off composer run phpstan

cs-fix: ## Run php-cs-fixer
	${DC_PHP_NO_DEBUG} vendor/bin/php-cs-fixer fix

