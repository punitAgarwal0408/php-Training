<?php

namespace Insurance\Services;

use Insurance\Policies\AbstractPolicy;

class PolicyIssuanceService
{
    public function issue(AbstractPolicy $policy): void
    {
        $validation = $policy->validateCoverage();

        if (!$validation->isValid()) {
            throw new \RuntimeException('Policy validation failed');
        }
    }
}
