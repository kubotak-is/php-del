# comment pattern for blade template

## Usage

```html
{{-- php-del start flag --}}
<p>This section will be deleted.</p>
{{-- php-del end flag --}}
```

## Line delete

```html
<h1>This section will be deleted.</h1>{{-- php-del line flag --}}
```

## Codes not covered

```html
{{-- php-del start flag --}}
<p>This section will be deleted.</p>
{{-- php-del ignore start --}}
<p>This area will not be deleted.</p>
{{-- php-del ignore end --}}
{{-- php-del end flag --}}
```

## File delete

```html
{{-- php-del file flag --}}
```
