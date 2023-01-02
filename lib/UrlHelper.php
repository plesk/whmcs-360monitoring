<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring;

use WHMCS\Module\Server\Plesk360Monitoring\Dto\License;

final class UrlHelper
{
    private const DEFAULT_DASHBOARD_URL = 'https://monitoring.platform360.io/dashboard/overview';

    public static function getActivationUrl(License $license, string $domain): string
    {
        if ($domain === '') {
            return $license->getKeyIdentifiers()->getActivationLink();
        }

        return 'https://' . $domain . '/license/activate/' . $license->getKeyIdentifiers()->getActivationCode();
    }

    public static function getDashboardUrl(string $domain): string
    {
        if ($domain === '') {
            return self::DEFAULT_DASHBOARD_URL;
        }

        return 'https://' . $domain . '/dashboard/overview';
    }
}
