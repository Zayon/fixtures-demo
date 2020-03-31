
DOCKER_EXEC_BASE = docker run --rm -it -v "$$PWD":/app --user $$(id -u):$$(id -g) -w /app
DOCKER_EXEC_COMPOSER = $(DOCKER_EXEC_BASE) -v /tmp:/tmp composer
DOCKER_EXEC_PHP = $(DOCKER_EXEC_BASE) php

##
## Project
## -------
##

run: ## Runs the server on http://localhost:8081
run: vendor
	$(DOCKER_EXEC_BASE) -p 8081:8081 --name fixture-demo php bin/console -vvv server:run 0.0.0.0:8081
.PHONY: run

stop: ## Stops the server
	docker kill fixture-demo
.PHONY: stop

clean: ## Clean
	rm -rf var vendor
.PHONY: clean

##
## QA
## --
##

behat: ## Run behat tests
behat: vendor
	$(DOCKER_EXEC_BASE) php vendor/bin/behat
.PHONY: behat

##
## Installation
## ------------
##

install: ## Install the project
install: vendor
	$(DOCKER_EXEC_PHP) bin/console doctrine:schema:create
.PHONY: install

composer.lock: composer.json
	$(DOCKER_EXEC_COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(DOCKER_EXEC_COMPOSER) install

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
