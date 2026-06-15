# Blade Templates

PHP-DEL recognizes Laravel Blade comments in files ending in `.blade.php`.
Add `"php"` to `extensions` because Blade files use `.php` as their final
extension.

```json
{
  "dirs": [
    "resources/views"
  ],
  "extensions": [
    "php"
  ]
}
```

## Block Deletion

```blade
{{-- php-del start legacy-checkout --}}
<a href="{{ route('legacy.checkout') }}">Legacy checkout</a>
{{-- php-del end legacy-checkout --}}
```

Both comments and the enclosed markup are removed.

## Line Deletion

The `line` marker removes the complete line containing the marker:

```blade
<h1>Temporary heading</h1>{{-- php-del line legacy-checkout --}}
```

Keep the marker on the same line as the markup to remove.

## Preserving Content

Use unflagged `ignore` markers inside a flagged block:

```blade
{{-- php-del start legacy-checkout --}}
<p>This text is removed.</p>
{{-- php-del ignore start --}}
<p>This text is preserved.</p>
{{-- php-del ignore end --}}
<p>This text is also removed.</p>
{{-- php-del end legacy-checkout --}}
```

Result:

```blade
<p>This text is preserved.</p>
```

## File Deletion

Place a file marker anywhere in the Blade file:

```blade
{{-- php-del file legacy-checkout --}}
```

Selecting `legacy-checkout` deletes the file. With `--dry-run`, the file is
reported but remains unchanged.

## Pairing Rules

Every `start <flag>` must have a corresponding `end <flag>`. An unmatched
marker causes PHP-DEL to report an error for that file and leave it unchanged.
Nested blocks using the same flag are not a supported structure.

See [Markers and behavior](markers.md) for the common matching rules.
