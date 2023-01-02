<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring;

use RuntimeException;
use WHMCS\Module\Server\Plesk360Monitoring\Plans\BusinessPlan;
use WHMCS\Module\Server\Plesk360Monitoring\Plans\EnterprisePlan;
use WHMCS\Module\Server\Plesk360Monitoring\Plans\FlexiblePlan;
use WHMCS\Module\Server\Plesk360Monitoring\Plans\ProPlan;

final class PlanCollection
{
    /**
     * @var Plan[]
     */
    private array $plans = [];

    public function __construct()
    {
        $proPlan = new ProPlan();
        $businessPlan = new BusinessPlan();
        $enterprisePlan = new EnterprisePlan();
        $flexiblePlan = new FlexiblePlan();

        $this->plans[$proPlan->getId()] = $proPlan;
        $this->plans[$businessPlan->getId()] = $businessPlan;
        $this->plans[$enterprisePlan->getId()] = $enterprisePlan;
        $this->plans[$flexiblePlan->getId()] = $flexiblePlan;
    }

    /**
     * @return Plan[]
     */
    public function getAll(): array
    {
        return $this->plans;
    }

    public function getPlanById(string $id): Plan
    {
        if (!isset($this->plans[$id])) {
            throw new RuntimeException("Plan with id '{$id}' not found");
        }

        return $this->plans[$id];
    }
}
