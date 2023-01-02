<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring\Plans;

use WHMCS\Module\Server\Plesk360Monitoring\Plan;

final class ProPlan implements Plan
{
    public function getId(): string
    {
        return 'pro';
    }

    public function getName(): string
    {
        return '360 Monitoring Pro';
    }

    public function getPlanApiConst(): string
    {
        return '360-MON-PRO-1M';
    }

    public function getAdditionalServersApiConst(): string
    {
        return '360-MON-PRO-SRV-1M';
    }

    public function getAdditionalWebsitesApiConst(): string
    {
        return '360-MON-PRO-SITE-1M';
    }
}
