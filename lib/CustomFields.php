<?php

// Copyright 2022. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Plesk360Monitoring;

use Illuminate\Database\ConnectionInterface;

final class CustomFields
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function addMissingProductCustomFields(int $productId): void
    {
        $rows = $this->connection->table('tblcustomfields')->where('relid', '=', $productId)->get();

        $hasFieldKeyId = false;
        $hasFieldActivationCode = false;
        $hasFieldActivationUrl = false;

        foreach ($rows as $row) {
            if ($row->fieldname === ServiceProperties::KEY_ID) {
                $hasFieldKeyId = true;

                continue;
            }

            if ($row->fieldname === ServiceProperties::ACTIVATION_CODE) {
                $hasFieldActivationCode = true;

                continue;
            }

            if ($row->fieldname === ServiceProperties::ACTIVATION_URL) {
                $hasFieldActivationUrl = true;

                continue;
            }
        }

        $curDate = date('Y-m-d H:i:s');

        if (!$hasFieldKeyId) {
            $this->addProductCustomField($productId, ServiceProperties::KEY_ID, $curDate);
        }

        if (!$hasFieldActivationCode) {
            $this->addProductCustomField($productId, ServiceProperties::ACTIVATION_CODE, $curDate);
        }

        if (!$hasFieldActivationUrl) {
            $this->addProductCustomField($productId, ServiceProperties::ACTIVATION_URL, $curDate);
        }
    }

    private function addProductCustomField(int $productId, string $fieldName, string $curDate): void
    {
        $this->connection->table('tblcustomfields')->insert([
            'type' => 'product',
            'relid' => $productId,
            'fieldname' => $fieldName,
            'fieldtype' => 'text',
            'adminonly' => 'on',
            'created_at' => $curDate,
            'updated_at' => $curDate,
        ]);
    }
}
