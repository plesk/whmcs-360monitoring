<?php

// Copyright 2024. WebPros International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring\Plans;

use WHMCS\Module\Server\Plesk360Monitoring\Plan;

final class BusinessPlan implements Plan
{
    public function getId(): string
    {
        return 'business';
    }

    public function getName(): string
    {
        return '360 Monitoring Business';
    }

    public function getPlanApiConst(): string
    {
        return '360-MON-BIZ-1M';
    }

    public function getAdditionalServersApiConst(): string
    {
        return '360-MON-BIZ-SRV-1M';
    }

    public function getAdditionalWebsitesApiConst(): string
    {
        return '360-MON-BIZ-SITE-1M';
    }
}
