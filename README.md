# IT

IT: Inline Test for PHP

## Install

```php
composer require --dev dave1010/it-should
```

## Usage

### Console command

Run inline tests under one or more files/directories:

```bash
php bin/it src
```

When installed as a dependency, use `./vendor/bin/it`. Run `php bin/it help` for available options.

### Writing tests

Write tests inline on functions and methods with the `\IT\Should` annotation.

```php
#[\IT\Should(return: 4, with: [2, 2])]
function add(int $a, int $b): int {
    return $a + $b;
}
```

Method support is planned; the current implementation executes inline tests on functions marked with `#[\IT\Should]`.

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
