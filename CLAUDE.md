# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Language

All commit messages, PR titles, PR descriptions, code comments, and documentation must be written in English.

## Code Style

### Imports
- Always import classes with `use` statements. Never use fully qualified class names (FQCN) inline.
- Order: PHP built-in → Ouzo framework → third-party.

### Types
- Always declare parameter types and return types.
- Fluent methods return `static` (not `self`).
- Use `is_null()` instead of `=== null`.

### Strings & Arrays
- Single quotes by default: `'value'`.
- Double quotes only for interpolation: `"prefix_{$var}"`.
- Always `[]` for arrays, never `array()`.

### Docblocks
- Only when types cannot be expressed in code or behavior needs explanation.
- Do not add `@param`/`@return` when method signatures are sufficient.

### Copyright header (every file)
```php
<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
```

### Classes
- `final` only on classes with constants only (e.g. `HttpMethod`, `HttpStatus`).
- `readonly class` for immutable value objects.
- No leading underscore on private methods.
- Keep methods short (3-20 lines), extract longer logic into private helpers.

### Error Handling
- Throw exceptions immediately with readable messages, no error codes.
- Never suppress or silently handle errors.

## What is Ouzo

Ouzo is a PHP MVC framework with built-in ORM, dependency injection, and utility libraries. Requires PHP 8.4+.

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

- `#[Test]` attribute, not `test` prefix in method names
- Method names: `shouldDoSomething()` pattern
- Test structure: `//given`, `//when`, `//then` comments
- `#[DataProvider('name')]` attribute, not `@dataProvider` annotation
- `#[Group('mysql')]`, `#[Group('postgres')]`, `#[Group('sqlite3')]`, `#[Group('non-sqlite3')]` for DB-specific tests
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
