# AGENTS.md

## Project overview

PHP-DEL is a small PHP CLI tool that removes source code marked with
`php-del` comments. It supports PHP, Blade templates, CSS, Sass, SCSS, and
Stylus. The package requires PHP 8.2 or newer and `ext-mbstring`.

The CLI reads `php-del.json` from the current working directory, discovers
flags in configured directories, asks the user to select one, then either
rewrites matching files or deletes files carrying a matching `file` marker.

## Common commands

```sh
composer install
composer test
vendor/bin/phpunit
./php-del --help
./php-del --dry-run
```

The repository also provides Docker/Task commands for the supported PHP
versions:

```sh
task install
task test
task install-php82
task test-php82
task install-php83
task test-php83
task install-php84
task test-php84
task install-php85
task test-php85
task test-all
```

CI runs `composer test` on PHP 8.2, 8.3, 8.4, and 8.5. When changing syntax,
dependencies, or runtime behavior, preserve compatibility with all four.

## Repository map

- `php-del`: executable entry point and Composer autoloader discovery.
- `src/Application.php`: CLI arguments, flag selection, rewrite/delete loop,
  output, and per-file error handling.
- `src/Factory/ConfigFactory.php`, `src/Config.php`: load and validate
  `<cwd>/php-del.json`.
- `src/Finder.php`: recursively find configured extensions and aggregate
  `start`, `line`, and `file` flags.
- `src/Rewriter.php`: repeatedly remove matching blocks and lines while
  preserving content enclosed by `ignore` markers.
- `src/Deleter.php`: detect whole-file deletion markers.
- `src/Comment/`: locate line and paired ("sandwich") comments and calculate
  exact replacement ranges.
- `src/Comment/Pattern/`: file-format-specific regular expressions.
- `src/Flag/`: flag values, occurrence counts, and the list used by CLImate.
- `src/File/`: typed `ArrayIterator` wrappers for discovered file paths.
- `tests/actual/`: source fixtures supplied to the parser/rewriter.
- `tests/expect/`: byte-sensitive expected rewrite results.
- `docs/`: supported marker syntax for Blade and stylesheet formats.

## Processing flow

1. `Application` loads configuration through `ConfigFactory`.
2. `Finder` scans configured directories and builds a unique, counted flag
   list plus the files containing relevant markers.
3. CLImate prompts for one flag.
4. Each target file is first checked by `Deleter` for a `file` marker.
5. Otherwise, `CommentPatternProvider` chooses a pattern from the file name
   and `Rewriter` removes matching blocks and single lines.
6. Paired `ignore start`/`ignore end` content inside a deleted block is kept,
   but the ignore marker comments themselves are removed.

## Behavioral constraints

- Supported markers are `start <flag>`, `end <flag>`, `line <flag>`,
  `file <flag>`, and unflagged `ignore start`/`ignore end`.
- Flag discovery currently accepts letters, digits, `_`, `-`, and `=`.
  Pattern classes interpolate the selected flag into regular expressions, so
  changes to accepted characters require careful escaping and regression
  tests.
- `.blade.php` must be detected before the generic `.php` extension.
- A paired marker with only a start or only an end must throw
  `SandWitchCommentException`.
- Whitespace and line boundaries are part of the behavior. Rewriter tests use
  complete fixture equality, so do not normalize formatting incidentally.
- `Finder` resolves configured directories relative to `getcwd()`, not
  relative to the package source.
- `php-del.json` defaults `extensions` to `["php"]`, but `dirs` is required.
- Unknown rewrite extensions must continue to raise
  `UndefinedExtensionException`.

## Change guidance

- Keep `declare(strict_types=1);`, the `PHPDel\` PSR-4 namespace, and the
  existing small-class organization.
- Prefer extending `CommentPatternProvider` and adding a dedicated
  `CommentPattern` implementation when supporting a new file syntax.
- Treat regular-expression changes as cross-format changes. Check raw PHP,
  Blade, CSS, and AltCSS behavior unless the change is conclusively isolated.
- Add or update both `tests/actual` and `tests/expect` fixtures for rewrite
  behavior. Add exception fixtures when validating malformed pairs.
- Keep tests deterministic and non-interactive; test `Finder`, `Deleter`, and
  `Rewriter` directly rather than driving the CLImate prompt.
- Do not edit `vendor/` or generated PHPUnit cache files.
- Review `docs/php-support-migration.md` when changing the PHP compatibility
  range or release process.

## Safety

Running `./php-del` without `--dry-run` can overwrite source files and can
unlink entire files. The repository's checked-in `php-del.json` points at
`tests/actual`, so an interactive run may mutate test fixtures. Prefer unit
tests for verification. If manual CLI verification is necessary, use
`--dry-run` or work on disposable fixture copies and inspect `git diff`
afterward.

`--dry-run` must not write or unlink files. Keep both rewrite and whole-file
deletion paths covered when changing dry-run behavior.
