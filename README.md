# PHP-DEL

[![Unit Test](https://github.com/kubotak-is/php-del/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/kubotak-is/php-del/actions/workflows/phpunit.yml)
[![Latest Stable Version](https://poser.pugx.org/kubotak-is/php-del/v)](https://packagist.org/packages/kubotak-is/php-del)
[![PHP Version Require](https://poser.pugx.org/kubotak-is/php-del/require/php)](https://packagist.org/packages/kubotak-is/php-del)
[![License](https://poser.pugx.org/kubotak-is/php-del/license)](https://packagist.org/packages/kubotak-is/php-del)

PHP-DEL is a CLI tool that permanently removes source code marked with
`php-del` comments. It runs interactively by default and offers a
non-interactive mode (`--flag`) for CI and AI-agent automation. It is useful
for maintaining optional, environment-specific, or temporary code paths and
removing one selected feature before a release or deployment.

```php
public function example(): void
{
    /** php-del start legacy-api */
    $this->callLegacyApi();
    /** php-del end legacy-api */
}
```

After selecting `legacy-api`, the marked block and its marker comments are
removed.

## Requirements

- PHP 8.2, 8.3, 8.4, or 8.5
- `ext-mbstring`
- Composer 2

PHP-DEL follows the PHP project's supported-version lifecycle.

## Installation

Install PHP-DEL as a development dependency:

```sh
composer require --dev kubotak-is/php-del
```

Create `php-del.json` in the directory where the command will be run:

```json
{
  "dirs": [
    "src",
    "resources/views"
  ],
  "extensions": [
    "php"
  ]
}
```

`dirs` paths are resolved from the current working directory. The
`extensions` setting defaults to `["php"]` when omitted. Blade files are
included by the `php` extension because their final extension is `.php`.

See [Configuration](docs/configuration.md) for all supported formats and
examples.

## Quick Start

1. Add a flag to the code that should be removed:

   ```php
   /** php-del start remove-me */
   $temporaryCode = true;
   /** php-del end remove-me */
   ```

2. Preview the operation:

   ```sh
   vendor/bin/php-del --dry-run
   ```

3. Select `remove-me` from the interactive list and inspect the reported
   files.

4. Run without `--dry-run` to apply the deletion:

   ```sh
   vendor/bin/php-del
   ```

5. Review the resulting diff:

   ```sh
   git diff
   ```

The command scans all configured files, counts each discovered flag, and
prompts for one flag. It then processes every file containing that flag. To
skip the prompt, pass `--flag=<name>` (see [Non-interactive Mode](#non-interactive-mode)).

## Marker Reference

### Delete a block

```php
/** php-del start feature-a */
$featureA = true;
/** php-del end feature-a */
```

### Delete one line

Place a `line` marker on the line to remove:

```php
use App\Legacy\Client; // php-del line feature-a
```

### Preserve part of a deleted block

`ignore` markers do not have a flag. Their contents survive when the
surrounding flagged block is removed:

```php
/** php-del start feature-a */
$removed = true;
/** php-del ignore start */
$preserved = true;
/** php-del ignore end */
/** php-del end feature-a */
```

Result:

```php
$preserved = true;
```

### Delete an entire file

```php
<?php

/** php-del file feature-a */

final class FeatureA
{
}
```

Selecting `feature-a` deletes the file unless `--dry-run` is active.

For exact matching rules and format-specific examples, see
[Markers and behavior](docs/markers.md).

## Supported Files

| Format | Extensions | Documentation |
| --- | --- | --- |
| PHP | `.php` | [Markers and behavior](docs/markers.md) |
| Blade | `.blade.php` | [Blade templates](docs/blade.md) |
| CSS | `.css` | [CSS and preprocessors](docs/css_and_alt_css.md) |
| Sass/SCSS | `.sass`, `.scss` | [CSS and preprocessors](docs/css_and_alt_css.md) |
| Stylus | `.stylus` | [CSS and preprocessors](docs/css_and_alt_css.md) |

## CLI Options

```text
--flag=<name>  Delete the given flag without the interactive prompt
--list-flags   List discovered flags with their occurrence counts, then exit
--dry-run      Discover and report changes without writing or deleting files
--validate     Validate all php-del markers without modifying files
--help         Print the command usage
```

When `--flag` is omitted, PHP-DEL prompts for a flag interactively. Passing
`--flag`, `--list-flags`, and `--validate` run without any prompt, which makes
them safe for CI pipelines and AI agents that have no interactive terminal.

## Validation

Validate every php-del marker in the configured files without selecting a
flag or modifying any file:

```sh
vendor/bin/php-del --validate
```

Validation reports malformed flags, unknown marker commands, unmatched or
crossing blocks, and invalid `ignore` placement with file, line, column, and
diagnostic ID. It checks all configured files and flags, unlike `--dry-run`,
which previews one selected flag.

Validation exits with `0` when all markers are valid, `1` when marker errors
are found, and `2` when validation cannot complete because of configuration,
option, file access, or unsupported-extension errors.

## Non-interactive Mode

Specify the flag directly to skip the interactive prompt:

```sh
# List available flags first (machine-readable name and count)
vendor/bin/php-del --list-flags

# Delete a flag without prompting
vendor/bin/php-del --flag=feature-a

# Preview without writing or deleting
vendor/bin/php-del --flag=feature-a --dry-run
```

If the given flag does not exist in the discovered list, PHP-DEL prints an
error and exits with status `1` without modifying any file.

### Exit Codes

| Code | Meaning |
| --- | --- |
| `0` | Deletion completed, flags were listed, or no matching flag was found. |
| `1` | A flag passed with `--flag` does not exist. |

Per-file processing errors are reported and skipped; they do not change the
overall exit code. Unhandled runtime errors (for example, a missing or invalid
`php-del.json`) terminate the process with a non-zero status set by the PHP
runtime.

## Safety

PHP-DEL performs destructive source changes:

- Rewritten files are overwritten in place.
- `file` markers delete the matching file.
- No backup files are created.
- An unmatched `start` or `end` marker reports an error for that file.

Run it only in a version-controlled working tree. Start with `--dry-run`,
apply the operation without unrelated local changes, and inspect `git diff`
before committing.

## Documentation

- [Documentation index](docs/README.md)
- [Configuration](docs/configuration.md)
- [Markers and behavior](docs/markers.md)
- [CLI workflow and troubleshooting](docs/usage.md)
- [Blade templates](docs/blade.md)
- [CSS and preprocessors](docs/css_and_alt_css.md)
- [Development guide](docs/development.md)
- [Release guide](docs/releasing.md)

## Development

The repository includes Docker environments for every supported PHP version:

```sh
task install
task test
task test-all
```

See [Development](docs/development.md) for individual PHP-version tasks,
dependency updates, fixtures, and validation commands.

## License

PHP-DEL is released under the [MIT License](LICENSE).
