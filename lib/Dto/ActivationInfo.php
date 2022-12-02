<?php

namespace WHMCS\Module\Server\Plesk360Monitoring\Dto;

use Exception;

class ActivationInfo
{
    private string $uid;
    private bool $activated;

    /**
     * @param array<string, string> $data
     * @throws Exception
     */
    public function __construct(array $data)
    {
        if (isset($data['uid'])) {
            $this->uid = $data['uid'];
        } else {
            throw new Exception('Missing params');
        }

        if (isset($data['activated'])) {
            $this->activated = (bool)$data['activated'];
        } else {
            throw new Exception('Missing params');
        }
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }
}
