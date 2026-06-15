# CLI Workflow and Troubleshooting

## Recommended Workflow

PHP-DEL overwrites and deletes files in place. Use this workflow:

1. Start from a clean Git working tree.
2. Confirm `php-del.json` points only to intended directories.
3. Run `vendor/bin/php-del --dry-run`.
4. Select the target flag.
5. Review the reported file paths and occurrence counts.
6. Run `vendor/bin/php-del` and select the same flag.
7. Inspect `git diff` and run the project's tests.
8. Commit the generated source changes.

In a non-interactive environment, replace the flag selection in steps 4 and 6
with `--flag=<name>` (see [Non-interactive Selection](#non-interactive-selection)).

## Interactive Selection

```sh
vendor/bin/php-del
```

Example:

```text
Finding flag...
Please choice me one of the following flag:
  flag-a (4)
  legacy-api (2)
```

The number is the count of discovered `start`, `line`, and `file` markers,
not the number of files or final rewrite operations.

When no flag argument is given, the command requires an interactive terminal.
Pass `--flag=<name>` to select a flag without the prompt (see
[Non-interactive Selection](#non-interactive-selection)).

## Non-interactive Selection

Pass the target flag directly to skip the prompt. This is the recommended mode
for CI pipelines and AI agents, where no interactive terminal is available:

```sh
vendor/bin/php-del --flag=legacy-api
```

If the given flag does not exist in the discovered list, the command prints an
error and exits with status `1` without modifying any file.

## Listing Flags

List every discovered flag and its occurrence count, then exit without
deleting anything:

```sh
vendor/bin/php-del --list-flags
```

```text
flag-a (4)
legacy-api (2)
```

Use this to discover which flags are available before choosing one to delete.

## Dry Run

```sh
vendor/bin/php-del --dry-run
```

Dry run performs discovery and rewrite calculation but does not:

- Write rewritten content
- Delete files carrying a matching `file` marker

The output still labels matching files as rewrite or deletion targets.

## Help

```sh
vendor/bin/php-del --help
```

## Output

Successful target files are displayed with one of these suffixes:

```text
/path/to/file.php(2)
/path/to/file.php(delete)
```

`(2)` means two block or line rewrite operations were found in that file.
`(delete)` means the file contains a matching whole-file marker.

Errors are reported per file and processing continues with the remaining
target files.

## Exit Codes

| Code | Meaning |
| --- | --- |
| `0` | Deletion completed, flags were listed, or no matching flag was found. |
| `1` | A flag passed with `--flag` does not exist. |

Per-file errors are reported and skipped; they do not change the exit code.
Unhandled runtime errors (for example, a missing or invalid `php-del.json`)
terminate the process with a non-zero status set by the PHP runtime.

## Troubleshooting

### Composer dependencies are missing

```text
You need to set up the project dependencies using Composer
```

Run:

```sh
composer install
```

### Configuration cannot be read

Confirm that `php-del.json` exists in the current working directory:

```sh
pwd
ls -l php-del.json
```

### No flags are found

Check:

- `dirs` points to the correct source directories.
- The file extension appears in `extensions`.
- The marker uses `start`, `line`, or `file`.
- The flag is not empty.
- The flag uses only supported characters.

`end` and `ignore` markers do not create entries in the selection list by
themselves.

### A file reports an unmatched marker

Typical errors:

```text
There is a start comment, but no end.
There is an end comment, but no start.
```

Find all markers for the selected flag and restore a valid start/end pair.
The affected file is not written when this error occurs.

### An extension is unsupported

Only PHP, Blade, CSS, Sass, SCSS, and Stylus have rewrite patterns. Remove
unsupported values from `extensions` or add a pattern implementation and
tests to PHP-DEL.

### Unexpected formatting remains

PHP-DEL removes matched text ranges and preserves surrounding whitespace.
Blank lines may remain after deletion. Run the target project's normal
formatter after reviewing the semantic diff.

### Recovering an accidental deletion

PHP-DEL creates no backups. Restore changes with the version-control workflow
appropriate for the project. For Git, inspect `git status` and restore only
the intended files; avoid discarding unrelated local changes.
