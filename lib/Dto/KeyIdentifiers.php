<?php

namespace WHMCS\Module\Server\Plesk360Monitoring\Dto;

use Exception;

class KeyIdentifiers
{
    private string $keyId;
    private string $keyNumber;
    private string $activationCode;
    private string $activationLink;

    /**
     * @param array<string, string> $data
     * @throws Exception
     */
    public function __construct(array $data)
    {
        if (isset($data['keyId'])) {
            $this->keyId = $data['keyId'];
        } else {
            throw new Exception('Missing params');
        }
        if (isset($data['keyNumber'])) {
            $this->keyNumber = $data['keyNumber'];
        } else {
            throw new Exception('Missing params');
        }
        if (isset($data['activationCode'])) {
            $this->activationCode = $data['activationCode'];
        } else {
            throw new Exception('Missing params');
        }
        if (isset($data['activationLink'])) {
            $this->activationLink = $data['activationLink'];
        } else {
            throw new Exception('Missing params');
        }
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }


    public function getKeyNumber(): string
    {
        return $this->keyNumber;
    }

    public function getActivationCode(): string
    {
        return $this->activationCode;
    }

    public function getActivationLink(): string
    {
        return $this->activationLink;
    }
}
