# Configuration

PHP-DEL reads `php-del.json` from the current working directory. The file is
required whenever the CLI runs.

## Complete Example

```json
{
  "dirs": [
    "app",
    "resources/views",
    "resources/css"
  ],
  "extensions": [
    "php",
    "css",
    "scss"
  ]
}
```

## `dirs`

`dirs` is required and must be an array of directory paths.

```json
{
  "dirs": [
    "src",
    "templates"
  ]
}
```

Each path is resolved relative to the directory from which
`vendor/bin/php-del` is executed. PHP-DEL recursively scans files below each
directory.

Run the command from the project root when the configuration contains
project-relative paths:

```sh
cd /path/to/project
vendor/bin/php-del --dry-run
```

PHP-DEL does not currently expand configuration paths relative to the
location of the executable or Composer package.

## `extensions`

`extensions` is optional and must be an array. It defaults to:

```json
{
  "extensions": [
    "php"
  ]
}
```

Supported values:

| Value | Files |
| --- | --- |
| `php` | PHP and Blade files |
| `css` | CSS files |
| `sass` | Sass files |
| `scss` | SCSS files |
| `stylus` | Stylus files |

Extension values do not include a leading dot. Matching is case-sensitive at
the file-discovery stage, so use lowercase extensions in both file names and
configuration.

Blade files are discovered with `php`, then recognized as `.blade.php` when
their comment syntax is selected.

## Multiple Source Trees

Directories can contain different supported formats:

```json
{
  "dirs": [
    "app",
    "resources"
  ],
  "extensions": [
    "php",
    "css",
    "sass",
    "scss",
    "stylus"
  ]
}
```

Only files containing `start`, `line`, or `file` markers are included in the
interactive flag selection.

## Validation and Errors

PHP-DEL rejects these configurations:

- Missing `dirs`
- A non-array `dirs` value
- A non-array `extensions` value
- Invalid JSON
- A missing or unreadable `php-del.json`

Unknown extensions may be discovered when listed in `extensions`, but they
cannot be rewritten because no comment pattern exists for them. Use only the
supported values above.

## Recommended Scope

Keep `dirs` as narrow as practical. Avoid scanning:

- `vendor`
- Generated assets
- Build output
- Cache directories
- Files that should never be modified by a development tool

PHP-DEL has no exclusion-pattern option. Directory selection is the primary
way to control its scope.
