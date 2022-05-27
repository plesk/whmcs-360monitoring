<?php
/**
 * WHMCS Plesk360 Provisioning Module
 * Lang / Translation Helper
 * (C) 2022 Plesk International GmbH
**/

namespace WHMCS\Module\Server\Plesk360;

require_once __DIR__ . DIRECTORY_SEPARATOR  . 'confighelper.php';

use WHMCS\Module\Server\Plesk360\ConfigHelper;

class LangHelper {

    private $configHelper;
    private $language;

    public function __construct($language)
    {
        $this->configHelper = new ConfigHelper();
        $this->language = $language;
    }

    public function getLangValue($key, $fallback): string
    {
        return $this->configHelper->configs[$key . '_' . $this->language] ?? ($this->configHelper->configs[$key] ?? $fallback);
    }
    
}