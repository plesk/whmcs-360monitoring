<?php
/**
 * WHMCS Plesk 360 Monitoring Provisioning Module
 * Version 1.4
 * (C) 2022 Plesk International GmbH
**/

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'kaclient.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'langhelper.php';

use WHMCS\Module\Server\Plesk360Monitoring\KAApiClient;
use WHMCS\Module\Server\Plesk360\LangHelper;

const PARAM_USERNAME = 'configoption1';
const PARAM_PASSWORD = 'configoption2';

function p360monitoring_MetaData()
{
    return array(
        'DisplayName' => '360 Monitoring',
        'APIVersion' => '1.1',
        'RequiresServer' => false,
        'ServiceSingleSignOnLabel' => false,
    );
}

function p360monitoring_ConfigOptions()
{
    return array(
        // configoption1
        'Plesk KA Username' => array(
            'Type' => 'text',
            'Size' => '128',
            'Default' => '',
            'Description' => '',
        ),
        // configoption2
        'Plesk KA Password' => array(
            'Type' => 'password',
            'Size' => '64',
            'Default' => '',
            'Description' => '',
        ),
        // configoption3
        'Servers' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => '<br/>Fixed amount of Servers for use in static products and addons. Leave empty for configurable products.',
        ),
        // configoption4
        'Websites' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => '<br/>Fixed amount of Websites for use in static products and addons. Leave empty for configurable products.',
        ),
    );
}

function p360monitoring_ClientArea(array $params)
{
    $keyId = $params['customfields']['keyId'];
    $uid = $params['customfields']['activationInfoUid'];
    $activated = $params['customfields']['activationInfoActivated'];
    $activationLink = $params['customfields']['activationLink'];
    $terminated = $params['model']->serviceProperties->get('terminated');
    $suspended = $params['model']->serviceProperties->get('suspended');
    $servers = p360monitoring_GetProductOptionValue($params, 'Servers');
    $websites = p360monitoring_GetProductOptionValue($params, 'Websites');

    try 
    {
        if (!$activated) {
            $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
            $result = $api->retrieve($keyId);
            $activationLink = $result['keyIdentifiers']['activationLink'];
            $activated = $result['activationInfo']['activated'];
            $uid = $result['activationInfo']['uid'];
            $terminated = $result['terminated'];
            $suspended = $result['suspended'];
            $params['model']->serviceProperties->save(['activationLink' => $activationLink,
                                                       'activationInfoUid' => $uid,
                                                       'activationInfoActivated' => $activated,
                                                       'terminated' => $terminated,
                                                       'suspended' => $suspended
                                                      ]);
        }          
        
        $langHelper = new LangHelper($_SESSION['Language']);
        
        $returnHtml = '<div class="tab-content"><div class="row"><div class="col-sm-1"><strong>' . $langHelper->getLangValue('label_features', 'Features') . '</strong></div></div><div class="row"><div class="col-sm-1">' . $langHelper->getLangValue('label_servers', 'Servers') . '</div><div class="col-sm-3 text-right">' . $servers . '</div></div><div class="row"><div class="col-sm-1">' . $langHelper->getLangValue('label_websites', 'Websites') . '</div><div class="col-sm-3 text-right">' . $websites . '</div></div>';
        if ($activated) {
            $returnHtml = $returnHtml . '<div class="row"><div class="col-sm-3 text-left">' . $langHelper->getLangValue('button_license_activated', 'License activated') . '</div></div>';
        }
        $returnHtml = $returnHtml . '</div><br/>';

        if (!$terminated && !$suspended) {
            if (!$activated) {
                $returnHtml = $returnHtml . '<div class="tab-content"><a class="btn btn-block btn-info" href="' . $activationLink . '" target="_blank">' . $langHelper->getLangValue('button_activate_license', 'Activate license') . '</a></div><br/>';
            } 
            $returnHtml = $returnHtml . '<div class="tab-content"><a class="btn btn-block btn-default" href="' . $langHelper->getLangValue('dashboard_url', 'https://monitoring.platform360.io/dashboard/overview') . '" target="_blank">' . $langHelper->getLangValue('button_dashboard', '360 Monitoring Dashboard') . '</a></div><br/>';
        }

        return $returnHtml;

    } catch (Exception $e) {
        logModuleCall(
            'p360monitoring',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }
    
    return $returnHtml;
}

function p360monitoring_CreateAccount(array $params)
{
    try {
        $servers = p360monitoring_GetProductOptionValue($params, 'Servers');
        $websites = p360monitoring_GetProductOptionValue($params, 'Websites');

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->create($servers, $websites, '');

        p360monitoring_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'p360monitoring',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function p360monitoring_SuspendAccount(array $params)
{
    try {
        $keyId = $params['customfields']['keyId'];

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->suspend('true', $keyId);

        p360monitoring_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'p360monitoring',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function p360monitoring_UnsuspendAccount(array $params)
{
    try {
        $keyId = $params['customfields']['keyId'];

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->suspend('false', $keyId);

        p360monitoring_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'p360monitoring',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';}

function p360monitoring_TerminateAccount(array $params)
{
    try {
        $keyId = $params['customfields']['keyId'];

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->delete($keyId);

        p360monitoring_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'p360monitoring',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function p360monitoring_ChangePackage(array $params)
{
    try {
        $keyId = $params['customfields']['keyId'];
        $servers = p360monitoring_GetProductOptionValue($params, 'Servers');
        $websites = p360monitoring_GetProductOptionValue($params, 'Websites');

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->update($servers,$websites,$keyId);

        p360monitoring_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'p360monitoring',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function p360monitoring_UpdateModel($params, $result) {
    $params['model']->serviceProperties->save(['ownerId' => $result['ownerId'],
                                               'keyId' => $result['keyIdentifiers']['keyId'],
                                               'keyNumber' => $result['keyIdentifiers']['keyNumber'],
                                               'activationCode' => $result['keyIdentifiers']['activationCode'],
                                               'activationLink' => $result['keyIdentifiers']['activationLink'],
                                               'activationInfoUid' => $result['activationInfo']['uid'],
                                               'activationInfoActivated' => $result['activationInfo']['activated'],
                                               'status' => $result['status'],
                                               'terminated' => $result['terminated'],
                                               'suspended' => $result['suspended']
                                            ]);
}

function p360monitoring_GetProductOptionValue($params, $optionName)
{
    // First try to get value from configurable options (pay-as-you-grow)
    if (isset($params['configoptions'][$optionName])) {
        return (string)$params['configoptions'][$optionName];
    }

    // Next try to get value from custom fields (workaround solution)
    if (isset($params['customfields'][$optionName])) {
        return (string)$params['customfields'][$optionName];
    }

    // Finally try to get value from module settings (fixed product or addon)
    $configOptions = p360monitoring_ConfigOptions();
    $configOptionCount = 1;

    foreach ($configOptions as $key => $value) {
        if ($key === $optionName) {
            if (isset($params['configoption' . $configOptionCount])) {
                return (string)$params['configoption' . $configOptionCount];
            }
        }
        $configOptionCount++;
    }

    return '';
}
