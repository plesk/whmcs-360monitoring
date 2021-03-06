<?php
/**
 * WHMCS Plesk360 Provisioning Module
 * Config Helper
 * (C) 2022 Plesk International GmbH
**/

namespace WHMCS\Module\Server\Plesk360;

class ConfigHelper {

    public $configs;

    public function __construct()
    {
        $this->configs = $this->loadConfigs();
    }

    public function loadConfigs(): array
    {
        $configsFilePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'configs.json';

        if ($configs = file_get_contents($configsFilePath)) {
            return (array) json_decode($configs);
        }

        return [];
    }
    
}