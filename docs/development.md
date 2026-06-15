# Development

## Prerequisites

- Docker with Docker Compose
- [Task](https://taskfile.dev/) 3.x
- Git

Local PHP and Composer are optional because the repository provides
containerized environments.

## Install Dependencies

Use the minimum supported PHP version:

```sh
task install
```

Equivalent command:

```sh
docker compose run --rm php82 composer install
```

## Run Tests

Minimum supported PHP:

```sh
task test
```

Every supported PHP version:

```sh
task test-all
```

Individual versions:

```sh
task test-php82
task test-php83
task test-php84
task test-php85
```

CI runs the same suite on PHP 8.2, 8.3, 8.4, and 8.5.

## Static Analysis

Run PHPStan at the maximum rule level using the minimum supported PHP
version:

```sh
task analyse
```

Equivalent command:

```sh
docker compose run --rm php82 composer analyse
```

PHPStan analyzes `src` and the `php-del` entry point. The configured PHP
version is 8.2 so new code remains compatible with the minimum supported
runtime.

## Composer Validation

```sh
docker compose run --rm php82 composer validate --strict
```

Update dependencies using the minimum supported PHP version so the lock file
does not accidentally require a newer runtime:

```sh
docker compose run --rm php82 composer update --with-all-dependencies
```

## Test Structure

- `tests/Unit`: PHPUnit tests
- `tests/actual`: input fixtures
- `tests/expect`: exact expected rewrite output

Rewriter tests compare complete file contents. Whitespace, line endings, and
marker placement are part of the tested behavior.

When changing matching behavior:

1. Add or update an input fixture in `tests/actual`.
2. Add the exact expected output in `tests/expect`.
3. Cover malformed marker pairs when relevant.
4. Run tests for all supported PHP versions.

## Adding a File Format

1. Add a `CommentPattern` implementation under
   `src/Comment/Pattern`.
2. Register the extension in `CommentPatternProvider`.
3. Ensure `Finder` can discover the extension.
4. Add rewrite, line, ignore, file-delete, and malformed-pair tests.
5. Document the configuration and marker syntax.

The implementation is regular-expression based. Escape user-controlled flag
values and preserve complete-match boundaries.

## Manual CLI Verification

The checked-in `php-del.json` targets `tests/actual`. Running the CLI without
`--dry-run` can modify test fixtures.

Prefer unit tests. For manual verification:

```sh
vendor/bin/php-del --dry-run
```

The checked-in fixtures intentionally include malformed marker pairs, so
running `vendor/bin/php-del --validate` against the repository's own
`php-del.json` is expected to fail. Exercise validation through its unit and
E2E tests or with a disposable configuration containing only valid fixtures.

If an applied run is necessary, use disposable fixture copies and verify
`git status` immediately afterward.

## Final Checks

```sh
task analyse
task test-all
docker compose run --rm php82 composer validate --strict
git diff --check
git status --short
```

Do not commit `vendor`, `.phpunit.cache`, IDE files, or other generated
artifacts.
