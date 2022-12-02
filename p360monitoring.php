<?php

// Copyright 2022. Plesk International GmbH. All rights reserved.

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require __DIR__ . '/vendor/autoload.php';

use WHMCS\Module\Server\Plesk360Monitoring\Dto\License;
use WHMCS\Module\Server\Plesk360Monitoring\KaApi;
use WHMCS\Module\Server\Plesk360Monitoring\Logger;
use WHMCS\Module\Server\Plesk360Monitoring\ServerOptions;
use WHMCS\Module\Server\Plesk360Monitoring\PlanCollection;
use WHMCS\Module\Server\Plesk360Monitoring\Plans\ProPlan;
use WHMCS\Module\Server\Plesk360Monitoring\ProductOptions;
use WHMCS\Module\Server\Plesk360Monitoring\Translator;

function p360monitoring_getKaApiClient(array $params): KaApi
{
    return new KaApi(
        $params[ServerOptions::SERVER_SCHEME],
        $params[ServerOptions::SERVER_HOST],
        $params[ServerOptions::SERVER_PORT],
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

    $keyId = $params['customfields']['keyId'];
    $uid = $params['customfields']['activationInfoUid'];
    $activated = $params['customfields']['activationInfoActivated'];
    $activationLink = $params['customfields']['activationLink'];
    $terminated = $params['model']->serviceProperties->get('terminated');
    $suspended = $params['model']->serviceProperties->get('suspended');
    $translator = Translator::getInstance($CONFIG);

    try {
        if (!$activated) {
            $kaApi = p360monitoring_getKaApiClient($params);

            $license = $kaApi->retrieveLicense($keyId);
            $activationLink = $license->getKeyIdentifiers()->getActivationLink();
            $activated = $license->getActivationInfo()->isActivated();
            $uid = $license->getActivationInfo()->getUid();
            $terminated = $license->isTerminated();
            $suspended = $license->isSuspended();

            $params['model']->serviceProperties->save([
                'activationLink' => $activationLink,
                'activationInfoUid' => $uid,
                'activationInfoActivated' => $activated,
                'terminated' => $terminated,
                'suspended' => $suspended
            ]);
        }

        $returnHtml = '';

        if ($activated) {
            $returnHtml .= '<div class="tab-content"><div class="row"><div class="col-sm-3 text-left">' . $translator->translate('p360monitoring_button_license_activated') . '</div></div></div><br/>';
        }

        if (!$terminated && !$suspended) {
            if (!$activated) {
                $returnHtml .= '<div class="tab-content"><a class="btn btn-block btn-info" href="' . $activationLink . '" target="_blank">' . $translator->translate('p360monitoring_button_activate_license') . '</a></div><br/>';
            }

            $returnHtml .= '<div class="tab-content"><a class="btn btn-block btn-default" href="https://monitoring.platform360.io/dashboard/overview" target="_blank">' . $translator->translate('p360monitoring_button_dashboard') . '</a></div><br/>';
        }

        return $returnHtml;
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

        p360monitoring_UpdateModel($params, $license);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_SuspendAccount(array $params): string
{
    try {
        $keyId = $params['customfields']['keyId'];

        $kaApi = p360monitoring_getKaApiClient($params);

        $license = $kaApi->suspendLicense($keyId);

        p360monitoring_UpdateModel($params, $license);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_UnsuspendAccount(array $params): string
{
    try {
        $keyId = $params['customfields']['keyId'];

        $kaApi = p360monitoring_getKaApiClient($params);

        $license = $kaApi->resumeLicense($keyId);

        p360monitoring_UpdateModel($params, $license);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_TerminateAccount(array $params): string
{
    try {
        $keyId = $params['customfields']['keyId'];

        $kaApi = p360monitoring_getKaApiClient($params);

        $license = $kaApi->terminateLicense($keyId);

        p360monitoring_UpdateModel($params, $license);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_ChangePackage(array $params): string
{
    try {
        $keyId = $params['customfields']['keyId'];
        $servers = ProductOptions::serverAllowance($params);
        $websites = ProductOptions::websiteAllowance($params);
        $plans = new PlanCollection();
        $plan = $plans->getPlanById($params[ProductOptions::PLAN_ID]);

        $kaApi = p360monitoring_getKaApiClient($params);

        $license = $kaApi->modifyLicense($keyId, $plan, $servers, $websites);

        p360monitoring_UpdateModel($params, $license);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function p360monitoring_UpdateModel($params, License $license): void
{
    $params['model']->serviceProperties->save([
        'ownerId' => $license->getOwnerId(),
        'keyId' => $license->getKeyIdentifiers()->getKeyId(),
        'keyNumber' => $license->getKeyIdentifiers()->getKeyNumber(),
        'activationCode' => $license->getKeyIdentifiers()->getActivationCode(),
        'activationLink' => $license->getKeyIdentifiers()->getActivationLink(),
        'activationInfoUid' => $license->getActivationInfo()->getUid(),
        'activationInfoActivated' => $license->getActivationInfo()->isActivated(),
        'status' => $license->getStatus(),
        'terminated' => $license->isTerminated(),
        'suspended' => $license->isSuspended(),
    ]);
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
