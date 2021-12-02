# PHP-DEL
[![Unit Test](https://github.com/kubotak-is/php-del/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/kubotak-is/php-del/actions/workflows/phpunit.yml)
[![Latest Stable Version](http://poser.pugx.org/kubotak-is/php-del/v)](https://packagist.org/packages/kubotak-is/php-del)
[![PHP Version Require](http://poser.pugx.org/kubotak-is/php-del/require/php)](https://packagist.org/packages/kubotak-is/php-del)
[![License](http://poser.pugx.org/kubotak-is/php-del/license)](https://packagist.org/packages/kubotak-is/php-del)

Tool to remove code based on specific comments.

## Configuration
Create php-del.json in the root directory of the project

```json
{
  "dirs": [
    "src"
  ],
  "extensions": [
    "php"
  ]
}
```
### dirs
Specify the directory to be searched for files.

### extensions(Optional: Default php)
Specify the extension to be searched.


## Usage

Add a comment with a flag for code like the following
```php
public function code() {
    /** php-del start flag-a */
    $something = 1;
    /** php-del end flag-a */
}
```

You can use the composer command to erase the target code by specifying a flag.

```
/vendor/bin/php-del flag-a
```

Deletion result
```php
public function code() {
}
```

### Codes not covered
The ignore comment can be added to remove it from the deletion list.

```php
public function code() {
    /** php-del start flag-a */
    $something = 1;
    /** php-del ignore start */
    $ignore = 2;
    /** php-del ignore end */
    /** php-del end flag-a */
}
```

Deletion result
```php
public function code() {
    $ignore = 2;
}
```
