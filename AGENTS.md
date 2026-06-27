# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

phpYABS is a web app for managing a used-book shop (buy/sell). It is the author's first project from 2003,
now being rewritten ("refucktoring") on a modern Symfony 8 / PHP 8.5 stack as an exercise. Expect the app
to be partially broken at any given time — the rewrite is in progress.

## Stack

- **Backend**: Symfony 8 (PHP `^8.5`), Doctrine ORM 3 + DBAL 3, MariaDB.
- **Frontend**: Vite + Vue 3 + Stimulus (Symfony UX), Tailwind 4, TypeScript, served via `pentatrion/vite-bundle`.
- **Money**: `moneyphp/money` + `tbbc/money-bundle` — never use floats for monetary amounts.
- **PSR-4**: `PhpYabs\` → `src/`, `PhpYabs\Tests\` → `tests/`.

## Commands

Everything runs inside Docker Compose; the `Makefile` is the canonical entry point. Prefer `make` targets
over invoking tools directly, because the PHP/Node toolchain lives in the containers.

- `make get-ready` — pull/build images, start containers, `composer install`. Run this first.
- `make up` / `make down` / `make reload` — container lifecycle.
- `make bash` — shell into the `php` container.
- `make pipeline` — full CI sequence: `get-ready` → unit → phpstan → cs-fixer.

Tests & quality (each shells into the container):
- `make phpunit` — full PHPUnit suite (PHPUnit 13). Pass args via `args=...`, e.g.
  `make phpunit args="--filter ISBN13Test"` to run a single test, or `make phpunit args=tests/ValueObject/ISBN13Test.php`.
- `make unit` — fast `unit` testsuite (everything under `tests/` except `tests/Integration/`, so no DB needed).
  The companion `integration` testsuite (`tests/Integration/`) needs the database and runs as part of `make phpunit`.
  Both suites are defined in `phpunit.dist.xml`.
- `make behat` — Behat feature tests (`features/*.feature`, context in `tests/Behat/`). Uses MinkExtension
  with the Symfony driver (no real browser).
- `make phpstan` — static analysis (level 4, config `phpstan.dist.neon`).
- `make cs-fixer` / `make cs-fixer-dry` — PHP CS Fixer (config `.php-cs-fixer.dist.php`).
- `make rector args=...` — Rector (config `rector.php`).

Frontend / assets:
- `make assets` — `npm run build` (Vite production build) in the `node` container.
- `make npm-install` / `make npm-update`.
- `npm run dev` (the `node` container runs this by default) — Vite dev server on port 5173.
- `npm run lint` — ESLint with `--fix` over `assets/`.

Local ports: app `:18080`, MariaDB `:13306`, phpMyAdmin `:18090`, Vite `:5173`.

## Architecture

- **Controllers** (`src/Controller/`) use attribute routing (`config/routes.yaml` autoloads `src/Controller/`
  as `type: attribute`). They extend a local `AbstractController` (`src/Controller/AbstractController.php`)
  that injects `EntityManagerInterface` — described in-code as a "Façade pattern" base.
- **Entities** (`src/Entity/`): `Book`, `Purchase`, `PurchaseLine`, `Destination`, `Hit`, `Rate`. A purchase
  is a cart (`Purchase` has many `PurchaseLine`).
- **Repositories** (`src/Repository/`) hold query logic, including `StatisticsRepository` and `HitRepository`.
- **Services** (`src/Service/`): `PurchaseService` owns the active-cart logic — on construction it loads the
  latest `Purchase` and starts a fresh one if the latest already has lines. Cart state lives in the DB, not
  the session.
- **Value objects** (`src/ValueObject/`): `ISBN`, `ISBN10`, `ISBN13` — immutable, well unit-tested; the model
  of how new code should look.
- **DTOs** (`src/DTO/`) carry purchase-list/line data between layers.
- `src/bootstrap.php` is the bootstrap loaded by PHPStan and the test suite; `src/Kernel.php` is the app kernel
  (`KERNEL_CLASS=PhpYabs\Kernel`).

## Conventions

- New/rewritten code: `declare(strict_types=1)`, constructor property promotion, readonly where possible —
  follow the value objects and `PurchaseService` as the reference style, not the legacy code.
- All code, comments, and commit messages in English.
