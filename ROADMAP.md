# ROADMAP

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
