# CSS and Preprocessors

PHP-DEL supports CSS, Sass, SCSS, and Stylus files.

```json
{
  "dirs": [
    "resources/css",
    "resources/styles"
  ],
  "extensions": [
    "css",
    "sass",
    "scss",
    "stylus"
  ]
}
```

CSS uses block comments. Sass, SCSS, and Stylus additionally support
single-line `//` marker comments.

## CSS

### Block deletion

```css
/* php-del start old-theme */
.legacy-banner {
    display: block;
}
/* php-del end old-theme */
```

### Line deletion

The complete line containing the marker is removed:

```css
.page {
    color: red; /* php-del line old-theme */
    background: white;
}
```

### Preserving declarations

```css
/* php-del start old-theme */
.page {
    color: red;
    /* php-del ignore start */
    background: white;
    /* php-del ignore end */
}
/* php-del end old-theme */
```

### File deletion

```css
/* php-del file old-theme */
```

## Sass, SCSS, and Stylus

### Block deletion

```scss
// php-del start old-theme
.legacy-banner {
    display: block;
}
// php-del end old-theme
```

Block-style comments are also accepted:

```scss
/* php-del start old-theme */
.legacy-banner {
    display: block;
}
/* php-del end old-theme */
```

### Line deletion

```scss
.page {
    color: red; // php-del line old-theme
    background: white;
}
```

### Preserving nested content

```scss
// php-del start old-theme
.page {
    color: red;
    // php-del ignore start
    background: white;
    // php-del ignore end
}
// php-del end old-theme
```

### File deletion

```scss
// php-del file old-theme
```

## Pairing Rules

Every `start <flag>` must have a corresponding `end <flag>`. An unmatched
marker reports an error for that file. Keep paired comments in the same
comment style where possible to make the source easy to audit.

See [Markers and behavior](markers.md) for shared matching and whitespace
rules.
