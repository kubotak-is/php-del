# AGENTS.md

## Project overview

PHP-DEL is a small PHP CLI tool that removes source code marked with
`php-del` comments. It supports PHP, Blade templates, CSS, Sass, SCSS, and
Stylus. The package requires PHP 8.2 or newer and `ext-mbstring`.

The CLI reads `php-del.json` from the current working directory, discovers
flags in configured directories, selects one (interactively, or via `--flag`
for non-interactive use), then either rewrites matching files or deletes files
carrying a matching `file` marker.

## Common commands

```sh
composer install
composer analyse
composer test
vendor/bin/phpunit
./php-del --help
./php-del --dry-run
./php-del --list-flags
./php-del --flag=<name>
./php-del --validate
```

The repository also provides Docker/Task commands for the supported PHP
versions:

```sh
task install
task analyse
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

CI runs `composer analyse` on PHP 8.2 and `composer test` on PHP 8.2, 8.3,
8.4, and 8.5. When changing syntax, dependencies, or runtime behavior,
preserve compatibility with all four.

## Repository map

- `php-del`: executable entry point and Composer autoloader discovery.
- `src/Application.php`: CLI arguments, flag selection, rewrite/delete loop,
  output, and per-file error handling.
- `src/Factory/ConfigFactory.php`, `src/Config.php`: load and validate
  `<cwd>/php-del.json`.
- `src/Finder.php`: recursively find configured extensions and aggregate
  `start`, `line`, and `file` flags.
- `src/FileFinder.php`: enumerate configured files relative to `getcwd()`.
- `src/Validation/`: scan comments and validate all marker structures without
  modifying files.
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
- `docs/configuration.md`: `php-del.json` schema, path resolution, and scope.
- `docs/markers.md`: common marker semantics and matching constraints.
- `docs/usage.md`: safe CLI workflow, output, and troubleshooting.
- `docs/blade.md`, `docs/css_and_alt_css.md`: format-specific syntax.
- `docs/development.md`: local setup, test matrix, fixtures, and extensions.
- `docs/releasing.md`: SemVer, Git tags, and Packagist publication.

## Processing flow

1. `Application` loads configuration through `ConfigFactory`.
2. `Finder` scans configured directories and builds a unique, counted flag
   list plus the files containing relevant markers.
3. The flag is resolved: `--flag` selects it non-interactively, `--list-flags`
   prints the list and exits, otherwise CLImate prompts for one flag.
4. Each target file is first checked by `Deleter` for a `file` marker.
5. Otherwise, `CommentPatternProvider` chooses a pattern from the file name
   and `Rewriter` removes matching blocks and single lines.
6. Paired `ignore start`/`ignore end` content inside a deleted block is kept,
   but the ignore marker comments themselves are removed.

With `--validate`, `Application` skips flag discovery and deletion, scans all
configured files, prints position-based diagnostics, and returns `0` for
success, `1` for marker errors, or `2` when validation cannot complete.

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
- `--flag <name>` skips the prompt and is implicitly non-interactive; an
  unknown flag must exit `1`. `--list-flags` prints names and counts, then
  exits `0`. Decorative TTY output (`blink()`) is suppressed in these modes.
- `main(): int` returns the exit code and `php-del` propagates it via
  `exit()`. Only flag resolution affects the code (`0` success, `1` unknown
  flag) during normal deletion; per-file failures are reported but keep the
  overall code `0`.
- `--validate` must remain non-interactive and non-destructive. It cannot be
  combined with `--flag`, `--list-flags`, or `--dry-run`.

## Change guidance

- Keep `declare(strict_types=1);`, the `PHPDel\` PSR-4 namespace, and the
  existing small-class organization.
- Keep `composer analyse` passing at PHPStan level max and PHP 8.2.
- Prefer extending `CommentPatternProvider` and adding a dedicated
  `CommentPattern` implementation when supporting a new file syntax.
- Treat regular-expression changes as cross-format changes. Check raw PHP,
  Blade, CSS, and AltCSS behavior unless the change is conclusively isolated.
- Add or update both `tests/actual` and `tests/expect` fixtures for rewrite
  behavior. Add exception fixtures when validating malformed pairs.
- Keep tests deterministic and non-interactive; test `Finder`, `Deleter`, and
  `Rewriter` directly rather than driving the CLImate prompt. End-to-end CLI
  behavior may be tested by running `php-del` as a subprocess with `--flag` /
  `--list-flags` / `--validate` (see `tests/Unit/ApplicationE2ETest.php`),
  since those paths never reach the prompt.
- Do not edit `vendor/` or generated PHPUnit cache files.
- Review `docs/releasing.md` when changing the PHP compatibility range or
  release process.

## Safety

Running `./php-del` without `--dry-run` can overwrite source files and can
unlink entire files. The repository's checked-in `php-del.json` points at
`tests/actual`, so an interactive run may mutate test fixtures. Prefer unit
tests for verification. If manual CLI verification is necessary, use
`--dry-run` or work on disposable fixture copies and inspect `git diff`
afterward.

`--dry-run` must not write or unlink files. Keep both rewrite and whole-file
deletion paths covered when changing dry-run behavior.

`--validate` must inspect all configured flags and must not write or unlink
files under any outcome.

The checked-in configuration includes malformed exception fixtures, so a
repository-root `./php-del --validate` is expected to return `1`.
