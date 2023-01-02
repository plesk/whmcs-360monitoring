<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

require __DIR__ . '/vendor/autoload.php';

use WHMCS\Database\Capsule;
use WHMCS\Module\Server\Plesk360Monitoring\Constants;
use WHMCS\Module\Server\Plesk360Monitoring\CustomFields;

add_hook('ProductEdit', 1, function (array $vars) {
    $productId = $vars['pid'] ?? null;
    $serverType = $vars['servertype'] ?? null;

    if (($productId === null) || ($serverType === null)) {
        return [];
    }

    if ($serverType !== Constants::MODULE_NAME) {
        return [];
    }

    $customFields = new CustomFields(Capsule::connection());

    $customFields->addMissingProductCustomFields($productId);

    return [];
});
