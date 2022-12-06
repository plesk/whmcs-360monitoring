<?php

namespace WHMCS\Module\Server\Plesk360Monitoring;

class ProductOptions
{
    public const PLAN_ID = 'configoption1';
    public const DOMAIN = 'configoption2';
    public const SERVERS = 'configoption3';
    public const WEBSITES = 'configoption4';

    private const OPTION_SERVERS = 'Servers';
    private const OPTION_WEBSITES = 'Websites';

    /**
     * @param array<array<string, mixed>> $params
     */
    public static function serverAllowance(array $params): int
    {
        $servers = (int)$params[self::SERVERS];

        if (isset($params['configoptions'][self::OPTION_SERVERS])) {
            $servers += (int)$params['configoptions'][self::OPTION_SERVERS];
        }

        return $servers;
    }

    /**
     * @param array<array<string, mixed>> $params
     */
    public static function websiteAllowance(array $params): int
    {
        $websites = (int)$params[self::WEBSITES];

        if (isset($params['configoptions'][self::OPTION_WEBSITES])) {
            $websites += (int)$params['configoptions'][self::OPTION_WEBSITES];
        }

        return $websites;
    }
}
