
DOCKER_EXEC_BASE = docker run --rm -it -v "$$PWD":/app --user 1000:100 -w /app
DOCKER_EXEC_COMPOSER = $(DOCKER_EXEC_BASE) composer
DOCKER_EXEC_PHP = $(DOCKER_EXEC_BASE) php
DOCKER_EXEC_CONSOLE = $(DOCKER_EXEC_BASE) php bin/console

##
## Project
## -------
##

console: ## Run bin/console
	$(DOCKER_EXEC_CONSOLE)

run: ## Runs the server
	$(DOCKER_EXEC_BASE) -p 8081:8081 --name fixture-demo php bin/console -vvv server:run 0.0.0.0:8081

stop: ## Stops the server
	docker kill fixture-demo

##
## Utils
## -----
##

composer.lock: composer.json
	$(DOCKER_EXEC_COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(DOCKER_EXEC_COMPOSER) install

composer: ## Display base composer command
composer:
	@echo docker run --rm -it -v \"\$$PWD\":/app --user 1000:100 -w /app composer

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
