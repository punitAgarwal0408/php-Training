<?php

namespace Insurance\ValueObjects;

class ValidationResult
{
    private array $errors = [];

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
