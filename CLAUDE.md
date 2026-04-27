# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What is Ouzo

Ouzo is a PHP MVC framework with built-in ORM, dependency injection, and utility libraries. Requires PHP 8.2+.

## Build & Test Commands

```bash
# Install dependencies
composer install

# Run all tests (defaults to PostgreSQL, excludes sqlite3 group)
./vendor/bin/phpunit test

# Run tests against a specific database
db=mysql ./vendor/bin/phpunit --exclude-group postgres,sqlite3 test
db=postgres ./vendor/bin/phpunit --exclude-group mysql,sqlite3 test
db=sqlite3 ./vendor/bin/phpunit --exclude-group mysql,postgres,non-sqlite3 test

# Run a single test file
./vendor/bin/phpunit test/src/Ouzo/Core/Db/ModelQueryBuilderTest.php

# Run a single test method
./vendor/bin/phpunit --filter testMethodName test/src/Ouzo/Core/Db/ModelQueryBuilderTest.php
```

Test databases must be seeded before first run using SQL scripts in `test/test-db/`.

## Architecture

The `Ouzo\` namespace maps to four source modules via PSR-4:

- **`src/Ouzo/Core`** — MVC framework: routing, controllers, views, ORM (Active Record), database layer, config, sessions, middleware, i18n
- **`src/Ouzo/Goodies`** — Standalone utility library: `Arrays`, `Strings`, `Functions`, `Clock`, `FluentArray`, `Optional`, `Json`, `Objects`, validators, iterators. Also provides test helpers (`Assert`, `CatchException`, `Mock`)
- **`src/Ouzo/Inject`** — Dependency injection container using PHP 8 attributes (`#[Inject]`, `#[Named]`)
- **`src/Ouzo/Migrations`** — Database migration system

Goodies and Inject are also published as standalone packages (`letsdrink/ouzo-goodies`, `letsdrink/ouzo-inject`).

### Request lifecycle

`Bootstrap` → `FrontController` → middleware chain (`SessionStarter` → `DefaultRequestId` → `LogRequest` → custom) → `RequestExecutor` resolves route → controller action → `View` renders response.

### ORM

`Model` extends `Validatable`, uses Active Record pattern. Relations: `belongsTo`, `hasOne`, `hasMany`. Query building via `ModelQueryBuilder` with fluent API. Database dialects: `MySqlDialect`, `PostgresDialect`, `Sqlite3Dialect`.

### Routing

Static API on `Route`: `get()`, `post()`, `put()`, `delete()`, `resource()`, `group()`. Annotation-based routing also supported.

## Testing Conventions

- PHPUnit 10.2+ with `#[Test]` attributes (not `@test` annotations)
- `#[Group('mysql')]`, `#[Group('postgres')]`, `#[Group('sqlite3')]`, `#[Group('non-sqlite3')]` for DB-specific tests
- Test structure: `//given`, `//when`, `//then` comments
- Base classes:
  - `DbTransactionalTestCase` — wraps each test in a transaction that rolls back
  - `ControllerTestCase` — HTTP testing with request/response mocking
- Custom fluent assertions via `Ouzo\Tests\Assert`:
  - `Assert::thatArray($arr)->hasSize(3)->contains('x')`
  - `Assert::thatString($s)->startsWith('foo')`
  - `Assert::thatModel($m)->hasSameAttributesAs($expected)`
- `CatchException::when($obj)->method()` + `CatchException::assertThat()->isInstanceOf(...)` for exception testing
- Custom mock framework in `Ouzo\Tests\Mock\Mock`

## Key Directories

- `bin/` — CLI commands (Symfony Console): model/controller/migration generators, route listing
- `config/test/` — Test environment config (DB connection selected by `db` env var)
- `test/Application/` — Fixture models and controllers used by tests
- `test/test-db/` — SQL schema scripts for each database engine
- `console` — CLI entry point script
