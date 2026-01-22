<?php

require_once __DIR__ . '/../src/Insurance/ValueObjects/Money.php';
require_once __DIR__ . '/../src/Insurance/ValueObjects/PolicyStatus.php';
require_once __DIR__ . '/../src/Insurance/ValueObjects/ValidationResult.php';
require_once __DIR__ . '/../src/Insurance/ValueObjects/RiskAssessment.php';

require_once __DIR__ . '/../src/Insurance/Policies/PremiumCalculatorInterface.php';
require_once __DIR__ . '/../src/Insurance/Calculators/VehiclePremiumCalculator.php';
require_once __DIR__ . '/../src/Insurance/Policies/AbstractPolicy.php';
require_once __DIR__ . '/../src/Insurance/Policies/VehiclePolicy.php';

require_once __DIR__ . '/../src/Insurance/Services/PolicyIssuanceService.php';

use Insurance\Calculators\VehiclePremiumCalculator;
use Insurance\Policies\VehiclePolicy;
use Insurance\ValueObjects\RiskAssessment;
use Insurance\Services\PolicyIssuanceService;

$calculator = new VehiclePremiumCalculator();

$risk = new RiskAssessment(40, true);

$discounts = [
    'loyal_customer' => true
];

$policyData = [
    'vehicle_number' => 'KA01AB1234'
];

$policy = new VehiclePolicy(
    'POL-001',
    $calculator,
    $policyData,
    $risk,
    $discounts
);

$premium = $policy->calculatePremium();
echo "Premium Amount: " . $premium->getAmount() . PHP_EOL;

$policy->activate();

$policy->addEndorsement('Added roadside assistance');

$refund = $policy->cancel(new DateTimeImmutable(), 'Customer request');
echo "Refund Amount: " . $refund->getAmount() . PHP_EOL;

