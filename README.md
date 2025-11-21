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
./vendor/bin/it src
```

Run `./vendor/bin/it help` for available options.

### Writing tests

Write tests inline on functions and methods with the `\IT\Should` annotation.

```php
#[\IT\Should(return: 4, given: [2, 2])]
function add(int $a, int $b): int {
    return $a + $b;
}
```

Use the `given` parameter to pass the arguments that should be provided to your function or method
when executing the inline test (for example: "it should return 4 given 2, 2").

Method support is planned; the current implementation executes inline tests on functions marked with `#[\IT\Should]`.

### Running tests

```bash
./vendor/bin/it src
```

IT will look at all the functions and classes/methods in `src` that have the `\IT\Should`
attributes, then call each one, checking the output is as expected.

## Development

For contributors working on this repository, the console command lives at `bin/it`, *Not* in `vendor`.
Run `composer install` after cloning to generate the local `vendor/autoload.php` so
`bin/it` and the bundled `examples` work without additional setup.

## Architecture

Main interfaces:

- Locator (finds inline tests in code, via reflection).
- Executor (runs the tests and captures results).
- Comparator (compares actual vs expected, producing human-friendly diffs).
- Reporter (outputs results, exit with non-zero if any failure).
