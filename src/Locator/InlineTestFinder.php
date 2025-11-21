<?php

declare(strict_types=1);

namespace IT\Locator;

use IT\Model\InlineTest;
use IT\Should;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class InlineTestFinder
{
    /**
     * @return list<InlineTest>
     */
    public function collect(): array
    {
        return [
            ...$this->collectFunctionTests(),
            ...$this->collectMethodTests(),
        ];
    }

    /**
     * @return list<InlineTest>
     */
    private function collectFunctionTests(): array
    {
        $tests = [];
        $userFunctions = get_defined_functions()['user'] ?? [];

        foreach ($userFunctions as $functionName) {
            $reflection = new ReflectionFunction($functionName);
            $attributes = $reflection->getAttributes(Should::class);

            foreach ($attributes as $attribute) {
                /** @var Should $test */
                $test = $attribute->newInstance();

                $tests[] = new InlineTest(
                    $functionName,
                    $test->given,
                    $test->return,
                    $reflection,
                    $test->with,
                );
            }
        }

        return $tests;
    }

    /**
     * @return list<InlineTest>
     */
    private function collectMethodTests(): array
    {
        $tests = [];
        $classes = get_declared_classes();

        foreach ($classes as $className) {
            $reflectionClass = new ReflectionClass($className);

            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                if ($reflectionMethod->getDeclaringClass()->getName() !== $className) {
                    continue;
                }

                $attributes = $reflectionMethod->getAttributes(Should::class);

                foreach ($attributes as $attribute) {
                    /** @var Should $test */
                    $test = $attribute->newInstance();

                    $tests[] = new InlineTest(
                        $className . '::' . $reflectionMethod->getName(),
                        $test->given,
                        $test->return,
                        $reflectionMethod,
                        $test->with,
                    );
                }
            }
        }

        return $tests;
    }
}
