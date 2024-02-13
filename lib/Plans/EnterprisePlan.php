<?php

// Copyright 2024. WebPros International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring\Plans;

use WHMCS\Module\Server\Plesk360Monitoring\Plan;

final class EnterprisePlan implements Plan
{
    public function getId(): string
    {
        return 'enterprise';
    }

    public function getName(): string
    {
        return '360 Monitoring Enterprise';
    }

    public function getPlanApiConst(): string
    {
        return '360-MON-ENT-1M';
    }

    public function getAdditionalServersApiConst(): string
    {
        return '360-MON-ENT-SRV-1M';
    }

    public function getAdditionalWebsitesApiConst(): string
    {
        return '360-MON-ENT-SITE-1M';
    }
}
