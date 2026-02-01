<?php

require __DIR__ . '/../vendor/autoload.php';


use Insurance\Calculators\VehiclePremiumCalculator;
use Insurance\Policies\VehiclePolicy;
use Insurance\Calculators\HealthPremiumCalculator;
use Insurance\Policies\HealthPolicy;
use Insurance\ValueObjects\RiskAssessment;
use Insurance\Services\PolicyIssuanceService;

// Vehicle policy demo
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
echo "Vehicle Premium Amount: " . $premium->getAmount() . PHP_EOL;

$policy->activate();

$policy->addEndorsement('Added roadside assistance');

$refund = $policy->cancel(new DateTimeImmutable(), 'Customer request');
echo "Vehicle Refund Amount: " . $refund->getAmount() . PHP_EOL;

// Health policy demo
$healthCalculator = new HealthPremiumCalculator();
$healthRisk = new RiskAssessment(80, true);
$healthDiscounts = [ 'no_smoker' => true ];
$healthData = [ 'person_name' => 'John Doe', 'age' => 55 ];

$healthPolicy = new HealthPolicy(
    'H-POL-001',
    $healthCalculator,
    $healthData,
    $healthRisk,
    $healthDiscounts
);

$healthPremium = $healthPolicy->calculatePremium();
echo "Health Premium Amount: " . $healthPremium->getAmount() . PHP_EOL;

