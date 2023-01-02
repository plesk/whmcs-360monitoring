<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring\Plans;

use WHMCS\Module\Server\Plesk360Monitoring\Plan;

final class FlexiblePlan implements Plan
{
    public function getId(): string
    {
        return 'flexible';
    }

    public function getName(): string
    {
        return '360 Monitoring Flexible';
    }

    public function getPlanApiConst(): string
    {
        return '360-MON-1M';
    }

    public function getAdditionalServersApiConst(): string
    {
        return '360-MON-SRV-1M';
    }

    public function getAdditionalWebsitesApiConst(): string
    {
        return '360-MON-SITE-1M';
    }
}
