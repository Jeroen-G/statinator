# Default to showing help section
info: intro help

intro:
	@echo ""
	@echo "Statinator"
	@echo ""

# ===========================
# Main commands
# ===========================

# Dependencies
install: intro do-composer-install do-assets-install
update: intro do-composer-update

# Tests
tests: intro do-test-unit do-test-report
test-unit: intro do-test-unit
mutations: intro do-test-infection

# Development
pre-commit: intro do-lint-staged-files do-commit-intro
codestyle: intro do-cs-ecs
codestyle-fix: intro do-cs-ecs-fix

# ===========================
# Overview of commands
# ===========================

help:
	@echo "\n=== Make commands ===\n"
	@echo "Dependencies"
	@echo "    make install                   Make the project ready for development."
	@echo "    make update                    Update backend and frontend dependencies."
	@echo "    make reset                     Reinstall backend and frontend dependencies."
	@echo "\nTests"
	@echo "    make tests                     Run tests."
	@echo "    make test-unit                 Run unit tests."
	@echo "    make mutations                 Run the infection mutation tests."
	@echo "\nDevelopment"
	@echo "    make codestyle                 Check if the codestyle is OK."
	@echo "    make codestyle-fix             Check and fix your messy codestyle."

# ===========================
# Recipes
# ===========================

# Dependencies
do-composer-install:
	@echo "\n=== Installing composer dependencies ===\n"\
	COMPOSER_MEMORY_LIMIT=-1 composer install

do-composer-update:
	@echo "\n=== Updating composer dependencies ===\n"\
	COMPOSER_MEMORY_LIMIT=-1 composer update

# Development
do-commit-intro:
	@echo "\n=== Let's ship it! ===\n"

do-lint-staged-files:
	@node_modules/.bin/lint-staged

do-cs-ecs:
	./vendor/bin/ecs check --config=dev/easy-coding-standard.yml

do-cs-ecs-fix:
	./vendor/bin/ecs check --fix --config=dev/easy-coding-standard.yml

# Project
do-assets-install:
	@echo "\n=== Installing npm dependencies ===\n"
	npm install

# Tests
do-test-unit:
	@echo "\n=== Running unit tests ===\n"
	vendor/bin/phpunit

do-test-infection:
	@echo "\n=== Running unit tests ===\n"
	vendor/bin/infection --threads=4 --min-covered-msi=100

do-test-report:
	@echo "\n=== Click the link below to see the test coverage report ===\n"
	@echo "report/index.html"
