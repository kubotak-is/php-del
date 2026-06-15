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

The command currently requires an interactive terminal. A flag cannot be
passed as a command-line argument.

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
