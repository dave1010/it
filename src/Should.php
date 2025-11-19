<?php

declare(strict_types=1);

namespace IT;

use IT\Exception\ShouldException;

/**
 * A lightweight assertion helper for command-line checks.
 */
class Should
{
    public function __construct(
        private readonly mixed $value,
        private readonly ?string $label = null
    ) {
    }

    public static function that(mixed $value, ?string $label = null): self
    {
        return new self($value, $label);
    }

    public function beTrue(?string $message = null): void
    {
        $this->assert(
            $this->value === true,
            $message ?? $this->formatMessage('to be true')
        );
    }

    public function beFalse(?string $message = null): void
    {
        $this->assert(
            $this->value === false,
            $message ?? $this->formatMessage('to be false')
        );
    }

    public function equal(mixed $expected, ?string $message = null): void
    {
        $this->assert(
            $this->value === $expected,
            $message ?? $this->formatMessage('to match', $expected)
        );
    }

    private function assert(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new ShouldException($message);
        }
    }

    private function formatMessage(string $expectation, mixed $expectedValue = null): string
    {
        $label = $this->label ?? 'Value';
        $actual = $this->describe($this->value);

        if ($expectedValue === null) {
            return sprintf('%s expected %s but got %s.', $label, $expectation, $actual);
        }

        $expected = $this->describe($expectedValue);

        return sprintf('%s expected %s (%s) but got %s.', $label, $expectation, $expected, $actual);
    }

    private function describe(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return var_export($value, true);
    }
}
