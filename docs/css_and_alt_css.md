# comment pattern for CSS or AltCSS

## Configuration
Create php-del.json in the root directory of the project

```json
{
  "dirs": [
    "src"
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

## Usage

for CSS
```css
/* php-del start flag */
.delete {
    display: none;
}
/* php-del end flag */
```

for Alt CSS
```scss
// php-del start flag
.delete {
    display: none;
}
// php-del end flag
```

## Line delete

for CSS
```css
.delete { 
    display: none; /** php-del line flag */
    color: red;
}
```

for Alt CSS
```scss
.delete { 
    display: none; // php-del line flag
    color: red;
}
```

## Codes not covered

for CSS
```html
.delete {
    /* php-del start flag */
    display: none;
    /* php-del ignore start */
    color: red;
    /* php-del ignore end */
    /* php-del end flag */
}
```

for Alt CSS
```scss
.delete {
    // php-del start flag
    display: none;
    // php-del ignore start
    color: red;
    // php-del ignore end
    // php-del end flag
}
```

## File delete

for CSS
```css
/* php-del file flag */
```

for Alt CSS
```scss
// php-del file flag
```