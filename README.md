# it

IT: Inline Test for PHP

## Install

```php
composer require --dev dave1010/it-should
```

## Usage

### Writing tests

Write tests inline on functions and methods with the \It\Should annotation.

```php
#[\It\Should(return: 4, with: [2, 2])]
function add(int $a, int $b): int {
    return $a + $b;
}
```

### Running tests

```bash
./vendor/bin/it
```


