<?php

namespace Insurance\ValueObjects;

class RiskAssessment
{
    public function __construct(
        public int $riskScore,
        public bool $hasPreviousClaims
    ) {}
}

 //learn more about this file