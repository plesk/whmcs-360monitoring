<?php

// Copyright 2022. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring;

interface Plan
{
    public function getId(): string;

    public function getName(): string;

    public function getPlanApiConst(): string;

    public function getAdditionalServersApiConst(): string;

    public function getAdditionalWebsitesApiConst(): string;
}
