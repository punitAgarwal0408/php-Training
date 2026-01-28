<?php

namespace Insurance\Policies;

use Insurance\ValueObjects\Money;
use Insurance\ValueObjects\RiskAssessment;

interface PremiumCalculatorInterface
{
    public function calculateBasePremium(array $data): Money;
    public function applyRiskFactors(Money $premium, RiskAssessment $risk): Money;
    public function applyDiscounts(Money $premium, array $discounts): Money;
}
