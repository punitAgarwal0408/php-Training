<?php

namespace Insurance\Calculators;

use Insurance\Policies\PremiumCalculatorInterface;
use Insurance\ValueObjects\Money;
use Insurance\ValueObjects\RiskAssessment;

class VehiclePremiumCalculator implements PremiumCalculatorInterface
{
    public function calculateBasePremium(array $data): Money
    {
        return new Money(50000); 
    }

    public function applyRiskFactors(Money $premium, RiskAssessment $risk): Money
    {
        if ($risk->hasPreviousClaims) {
            return $premium->multiply(1.2);
        }

        return $premium;
    }

    public function applyDiscounts(Money $premium, array $discounts): Money
    {
        if ($discounts['loyal_customer'] ?? false) {
            return $premium->multiply(0.9);
        }

        return $premium;
    }
}
