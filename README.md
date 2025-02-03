# orders-be
Backend system to monitor and manage daily user orders

# Setup guide

The project is ready to be run using laravel Sail, a built-in solution provided by Laravel to run the application using Docker.
Docker Desktop is required. On Widows machines, WSL2 (Windows Subsystem for Linux) needs to be installed and enabled.

## 1. Clone the repository

```bash
git clone https://github.com/Ambro-Gio/orders-be.git
```

## 2. Install the dependencies

This command uses a docker container to install the vendor dependencies without installing php and composer on the machine.

```bash
cd path/to/orders-be/orders-be

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php84-composer:latest \
    composer install
```

## 3. Setup the environment file

Copy `.env.example` in `.env`. The example provided is already set up with the local mysql connection used by sail, for convenience.

```bash
cp .env.example .env
```

## 4. Run Sail and generate app key

```bash
./vendor/bin/sail up
./vendor/bin/sail artisan key:generate
```

## 5. Run migrations

```bash
./vendor/bin/sail artisan migrate
```

The project is now accessible via localhost.

# Testing and code coverage

To run the application tests:

```bash

./vendor/bin/sail artisan test

```

Phpunit provides a command to generate a code coverage report.

```bash

./vendor/bin/sail phpunit --coverage-text

```

The report can also be generated as a html document

```bash

./vendor/bin/sail phpunit --coverage-html tests/coverage

```

# Documentation

A documentation for the orders-be Api can be found [here](Docs/Api%20routes.md)
