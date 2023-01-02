<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require __DIR__ . '/vendor/autoload.php';

use WHMCS\Module\Server\Plesk360Monitoring\KaApi;
use WHMCS\Module\Server\Plesk360Monitoring\Logger;
use WHMCS\Module\Server\Plesk360Monitoring\ServerOptions;
use WHMCS\Module\Server\Plesk360Monitoring\ServiceProperties;
use WHMCS\Module\Server\Plesk360Monitoring\PlanCollection;
use WHMCS\Module\Server\Plesk360Monitoring\Plans\ProPlan;
use WHMCS\Module\Server\Plesk360Monitoring\ProductOptions;
use WHMCS\Module\Server\Plesk360Monitoring\Translator;
use WHMCS\Module\Server\Plesk360Monitoring\UrlHelper;

function p360monitoring_getKaApiClient(array $params): KaApi
{
    return new KaApi(
        $params[ServerOptions::SERVER_SCHEME],
        $params[ServerOptions::SERVER_HOST],
        (int)$params[ServerOptions::SERVER_PORT],
        $params[ServerOptions::SERVER_USERNAME],
        $params[ServerOptions::SERVER_PASSWORD]
    );
}

function p360monitoring_MetaData(): array
{
    return [
        'DisplayName' => '360 Monitoring',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'ServiceSingleSignOnLabel' => false,
    ];
}

function p360monitoring_ConfigOptions(): array
{
    global $CONFIG;

    $plans = new PlanCollection();
    $planOptions = [];

    foreach ($plans->getAll() as $plan) {
        $planOptions[$plan->getId()] = $plan->getName();
    }

    $proPlan = new ProPlan();
    $translator = Translator::getInstance($CONFIG);

    return [
        ProductOptions::PLAN_ID => [
            'FriendlyName' => $translator->translate('p360monitoring_label_plan'),
            'Type' => 'dropdown',
            'Size' => '25',
            'Options' => $planOptions,
            'Default' => $proPlan->getId(),
            'SimpleMode' => true,
        ],
        ProductOptions::DOMAIN => [
            'FriendlyName' => $translator->translate('p360monitoring_label_domain'),
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'SimpleMode' => true,
        ],
        ProductOptions::SERVERS => [
            'FriendlyName' => $translator->translate('p360monitoring_label_additional_servers'),
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'SimpleMode' => true,
        ],
        ProductOptions::WEBSITES => [
            'FriendlyName' => $translator->translate('p360monitoring_label_additional_websites'),
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'SimpleMode' => true,
        ],
    ];
}

function p360monitoring_ClientArea(array $params): string
{
    global $CONFIG;

    $kaApi = p360monitoring_getKaApiClient($params);
    $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
    $translator = Translator::getInstance($CONFIG);

    try {
        $license = $kaApi->retrieveLicense($keyId);
        $domain = $params[ProductOptions::DOMAIN];
        $activationUrl = UrlHelper::getActivationUrl($license, $domain);
        $dashboardUrl = UrlHelper::getDashboardUrl($domain);

        if ($license->getActivationInfo()->isActivated()) {
            return '<div class="tab-content"><div class="row"><div class="col-sm-3 text-left">' . $translator->translate('p360monitoring_button_license_activated') . '</div></div></div><br/>';
        }

        $html = '';

        if (!$license->isTerminated() && !$license->isSuspended()) {
            $html .= '<div class="tab-content"><a class="btn btn-block btn-info" href="' . $activationUrl . '" target="_blank">' . $translator->translate('p360monitoring_button_activate_license') . '</a></div><br/>';
        }

        $html .= '<div class="tab-content"><a class="btn btn-block btn-default" href="' . $dashboardUrl . '" target="_blank">' . $translator->translate('p360monitoring_button_dashboard') . '</a></div><br/>';

        return $html;
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_CreateAccount(array $params): string
{
    try {
        $servers = ProductOptions::serverAllowance($params);
        $websites = ProductOptions::websiteAllowance($params);
        $plans = new PlanCollection();
        $plan = $plans->getPlanById($params[ProductOptions::PLAN_ID]);
        $kaApi = p360monitoring_getKaApiClient($params);
        $license = $kaApi->createLicense($plan, $servers, $websites);
        $domain = $params[ProductOptions::DOMAIN];

        $params['model']->serviceProperties->save([
            ServiceProperties::KEY_ID => $license->getKeyIdentifiers()->getKeyId(),
            ServiceProperties::ACTIVATION_CODE => $license->getKeyIdentifiers()->getActivationCode(),
            ServiceProperties::ACTIVATION_URL => UrlHelper::getActivationUrl($license, $domain),
        ]);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_SuspendAccount(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $kaApi = p360monitoring_getKaApiClient($params);

        $kaApi->suspendLicense($keyId);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_UnsuspendAccount(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $kaApi = p360monitoring_getKaApiClient($params);

        $kaApi->resumeLicense($keyId);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_TerminateAccount(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $kaApi = p360monitoring_getKaApiClient($params);

        $kaApi->terminateLicense($keyId);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_ChangePackage(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $servers = ProductOptions::serverAllowance($params);
        $websites = ProductOptions::websiteAllowance($params);
        $plans = new PlanCollection();
        $plan = $plans->getPlanById($params[ProductOptions::PLAN_ID]);
        $kaApi = p360monitoring_getKaApiClient($params);

        $kaApi->modifyLicense($keyId, $plan, $servers, $websites);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_TestConnection(array $params): array
{
    try {
        $kaApi = p360monitoring_getKaApiClient($params);

        $kaApi->testConnection();

        return [
            'success' => true,
            'error' => '',
        ];
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return [
            'success' => false,
            'error' => $exception->getMessage(),
        ];
    }
}
