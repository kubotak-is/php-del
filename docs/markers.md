# Markers and Behavior

PHP-DEL discovers named flags and asks the user to select one. All markers
using that selected flag are then processed across the configured files.

## Marker Summary

| Marker | Purpose |
| --- | --- |
| `php-del start <flag>` | Start a removable block |
| `php-del end <flag>` | End a removable block |
| `php-del line <flag>` | Remove the complete line |
| `php-del file <flag>` | Delete the complete file |
| `php-del ignore start` | Start content preserved inside a removed block |
| `php-del ignore end` | End preserved content |

## Flags

Marker matching is case-insensitive, but the original spelling is preserved
in the interactive selection list. Use the same lowercase spelling
everywhere so differently cased variants do not appear as separate choices.

Supported flag characters are:

- ASCII letters
- Digits
- Hyphen: `-`
- Underscore: `_`
- Equals sign: `=`

Examples:

```text
legacy-api
feature_123
edition=community
```

Spaces and punctuation such as `.`, `/`, `:`, and `@` are not supported.
Empty flags are ignored.

Flags are matched completely. Selecting `release` does not process
`release_candidate`.

## Block Deletion

```php
/** php-del start legacy-api */
$client = new LegacyClient();
$client->send();
/** php-del end legacy-api */
```

The start marker, enclosed content, and end marker are removed. Markers may
also appear next to inline expressions, but placing them on dedicated lines
is easier to review.

Every start marker must have an end marker. If either side is missing,
PHP-DEL reports an error and does not write that file.

Do not nest blocks with the same flag. The implementation processes the
first matching start and end comments repeatedly and does not model a nested
syntax tree.

## Line Deletion

```php
use App\Legacy\Client; // php-del line legacy-api
```

The complete line is removed, including indentation and the marker. Avoid
placing unrelated code on the same line.

## Preserved Content

`ignore` markers are meaningful only inside a selected deletion block:

```php
/** php-del start legacy-api */
$removed = true;
/** php-del ignore start */
$preserved = true;
/** php-del ignore end */
$alsoRemoved = true;
/** php-del end legacy-api */
```

Result:

```php
$preserved = true;
```

Ignore markers themselves are removed. They do not carry a flag and are
preserved whenever their surrounding selected block is processed.

Multiple ignore sections can appear in one deletion block.

## File Deletion

```php
/** php-del file legacy-api */
```

Any matching file marker causes the complete file to be deleted. The marker
can appear anywhere in the file.

`--dry-run` reports the file as a deletion target without unlinking it.

## Whitespace

Markers accept normal spaces, tabs, and full-width spaces between marker
keywords. Conventional single-space formatting is recommended:

```text
php-del start feature-a
```

Whitespace and line endings outside matched regions are otherwise preserved.
PHP-DEL does not run a formatter after rewriting.

## Validation

Run the non-destructive validator before deletion or in CI:

```sh
vendor/bin/php-del --validate
```

It checks every configured file and flag. Validation rejects unmatched
`start` / `end` and `ignore` pairs, nested blocks using the same flag,
crossing blocks, `ignore` markers outside a deletion block, empty or invalid
flags, and unknown php-del commands.

Different flags may be nested only when they close in reverse order:

```text
php-del start outer
php-del start inner
php-del end inner
php-del end outer
```

## Format-Specific Syntax

- [Blade templates](blade.md)
- [CSS and preprocessors](css_and_alt_css.md)
