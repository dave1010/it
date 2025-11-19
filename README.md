# IT

IT: Inline Test for PHP

## Install

```php
composer require --dev dave1010/it-should
```

## Usage

### Console command

Install dependencies and run the built-in expectations:

```bash
composer install
php bin/console
```

Run `php bin/console help` for available options.

### Writing tests

Write tests inline on functions and methods with the `\IT\Should` annotation.

```php
#[\IT\Should(return: 4, with: [2, 2])]
function add(int $a, int $b): int {
    return $a + $b;
}
```

IT will automatically instantiate objects when testing instance methods. Optionally pass constructor arguments in the `constructed` parameter.

Thanks to PHP 8.5's closures in constant expressions, you can pass a callable to create the system under test:

```php
#[\IT\Should(return: 4, with: [2, 2], it: static function () { return new Calc; })]
class Calc
{
    function add(int $a, int $b): int {
        return $a + $b;
    }
}
```

### Running tests

```bash
./vendor/bin/it src
```

IT will look at all the functions and classes/methods in `src` that have the `\IT\Should`
attributes, then call each one, checking the output is as expected.

## Architecture

Main interfaces:

- Locator (finds inline tests in code, via reflection).
- Executor (runs the tests and captures results).
- Comparator (compares actual vs expected, producing human-friendly diffs).
- Reporter (outputs results, exit with non-zero if any failure).
