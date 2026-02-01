<?php

namespace Insurance\Policies;

use Insurance\ValueObjects\Money;
use Insurance\ValueObjects\RiskAssessment;
use Insurance\ValueObjects\ValidationResult;

class HealthPolicy extends AbstractPolicy
{
    public function __construct(
        string $policyNumber,
        private PremiumCalculatorInterface $calculator,
        private array $policyData,
        private RiskAssessment $risk,
        private array $discounts
    ) {
        parent::__construct($policyNumber);
    }

    public function calculatePremium(): Money
    {
        $premium = $this->calculator->calculateBasePremium($this->policyData);
        $premium = $this->calculator->applyRiskFactors($premium, $this->risk);
        return $this->calculator->applyDiscounts($premium, $this->discounts);
    }

    public function validateCoverage(): ValidationResult
    {
        $result = new ValidationResult();

        if (empty($this->policyData['person_name'])) {
            $result->addError('Person name required');
        }

        if (empty($this->policyData['age'])) {
            $result->addError('Age is required');
        }

        return $result;
    }
}
