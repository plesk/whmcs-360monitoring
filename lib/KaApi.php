<?php

// Copyright 2022. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use WHMCS\Module\Server\Plesk360Monitoring\Dto\License;

final class KaApi
{
    private const CONTENT_TYPE_JSON = 'application/json';

    private Client $client;

    public function __construct(
        string $scheme,
        string $host,
        int $port,
        string $username,
        string $password
    ) {
        $baseUri = $scheme . '://' . $host . ':' . $port;

        $this->client = new Client([
            'base_uri' => $baseUri,
            'auth' => [$username, $password],
            'headers' => [
                'Accept' => self::CONTENT_TYPE_JSON,
                'Content-Type' => self::CONTENT_TYPE_JSON,
            ],
        ]);
    }

    /**
     * @throws Exception
     */
    public function testConnection(): bool
    {
        try {
            $response = $this->client->get('/jsonrest/business-partner/30/keys');
            $response->getBody()->getContents();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return true;
    }


    /**
     * @throws Exception
     */
    public function createLicense(Plan $plan, int $servers, int $websites): License
    {
        $options = [
            'json' => [
                'items' => $this->buildItems($plan, $servers, $websites),
            ],
        ];

        try {
            $response = $this->client->post('/jsonrest/business-partner/30/keys?return-key-state=yes', $options);
            $data = json_decode($response->getBody()->getContents(), true, 512,JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return new License($data);
    }

    /**
     * @throws Exception
     */
    public function retrieveLicense(string $keyId): License
    {
        try {
            $response = $this->client->get("/jsonrest/business-partner/30/keys/{$keyId}?return-key-state=yes");
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return new License($data);
    }

    /**
     * @throws Exception
     */
    public function suspendLicense(string $keyId): License
    {
        $options = [
            'json' => [
                'suspended' => 'true',
            ],
        ];

        try {
            $response = $this->client->put("/jsonrest/business-partner/30/keys/{$keyId}?return-key-state=yes", $options);
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return new License($data);
    }

    /**
     * @throws Exception
     */
    public function resumeLicense(string $keyId): License
    {
        $options = [
            'json' => [
                'suspended' => 'false',
            ],
        ];

        try {
            $response = $this->client->put("/jsonrest/business-partner/30/keys/{$keyId}?return-key-state=yes", $options);
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return new License($data);
    }

    /**
     * @throws Exception
     */
    public function terminateLicense(string $keyId): License
    {
        try {
            $response = $this->client->delete("/jsonrest/business-partner/30/keys/{$keyId}?return-key-state=yes");
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return new License($data);
    }

    /**
     * @throws Exception
     */
    public function modifyLicense(string $keyId, Plan $plan, int $servers, int $websites): License
    {
        $options = [
            'json' => [
                'items' => $this->buildItems($plan, $servers, $websites),
            ],
        ];

        try {
            $response = $this->client->put("/jsonrest/business-partner/30/keys/{$keyId}?return-key-state=yes", $options);
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return new License($data);
    }

    /**
     * @see https://docs.plesk.com/en-US/onyx/partner-api-3.0/introduction-to-key-administrator-partner-api-30.77827/
     * @return array<array<string, int|string>>
     */
    private function buildItems(Plan $plan, int $servers, int $websites): array
    {
        return [
            [
                'item' => $plan->getPlanApiConst(),
            ],
            [
                'item' => $plan->getAdditionalServersApiConst(),
                'quantity' => $servers,
            ],
            [
                'item' => $plan->getAdditionalWebsitesApiConst(),
                'quantity' => $websites,
            ],
        ];
    }
}
