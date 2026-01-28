<?php

namespace Insurance\Policies;

use DateTimeImmutable;
use Insurance\ValueObjects\Money;
use Insurance\ValueObjects\PolicyStatus;
use Insurance\ValueObjects\ValidationResult;

abstract class AbstractPolicy
{
    protected string $policyNumber;
    protected PolicyStatus $status;
    protected Money $premium;
    protected array $endorsements = [];

    public function __construct(string $policyNumber)
    {
        $this->policyNumber = $policyNumber;
        $this->status = PolicyStatus::draft();
    }

    abstract public function calculatePremium(): Money;
    abstract public function validateCoverage(): ValidationResult;

    public function cancel(DateTimeImmutable $date, string $reason): Money
    {
        if (!$this->status->canCancel()) {
            throw new \RuntimeException('Policy cannot be cancelled');
        }

        $this->status = PolicyStatus::cancelled();

        // simple pro-rata refund: 50%
        return $this->premium->multiply(0.5);
    }

    public function activate(): void
    {
    $this->status = PolicyStatus::active();
    }


    public function addEndorsement(string $endorsement): void
    {
        $this->endorsements[] = $endorsement;
        $this->premium = $this->calculatePremium();
    }
}
