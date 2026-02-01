<?php

namespace Insurance\Calculators;

use Insurance\Policies\PremiumCalculatorInterface;
use Insurance\ValueObjects\Money;
use Insurance\ValueObjects\RiskAssessment;

class HealthPremiumCalculator implements PremiumCalculatorInterface
{
    public function calculateBasePremium(array $data): Money
    {
        $age = $data['age'] ?? 30;
        $base = 20000;
        if ($age > 50) {
            $base += 10000;
        }

        return new Money($base);
    }

    public function applyRiskFactors(Money $premium, RiskAssessment $risk): Money
    {
        if ($risk->riskScore > 70) {
            return $premium->multiply(1.3);
        }

        if ($risk->hasPreviousClaims) {
            return $premium->multiply(1.2);
        }

        return $premium;
    }

    public function applyDiscounts(Money $premium, array $discounts): Money
    {
        if (!empty($discounts['no_smoker'])) {
            return $premium->multiply(0.85);
        }

        return $premium;
    }
}
