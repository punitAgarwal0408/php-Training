<?php
namespace Insurance\ValueObjects;
final class PolicyStatus
{
    private function __construct(private string $value) {}

    public static function draft(): self
    {
        return new self('draft');
    }

    public static function pending(): self
    {
        return new self('pending');
    }

    public static function active(): self
    {
        return new self('active');
    }

    public static function cancelled(): self
    {
        return new self('cancelled');
    }

    public function canCancel(): bool
    {
        return in_array($this->value, ['pending', 'active']);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
