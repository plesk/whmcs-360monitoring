<?php
/**
 * WHMCS Plesk 360 Monitoring Provisioning Module
 * KA Client
 * (C) 2022 Plesk International GmbH
**/

namespace WHMCS\Module\Server\Plesk360Monitoring;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'curlhelper.php';

use WHMCS\Module\Server\Plesk360\CURLHelper as CURLHelper;

class KAApiClient {

    const API_URL_BASE = 'https://api.central.plesk.com/30/keys';
    const JSON_CONTENT_TYPE = 'application/json';

    protected $results = array();
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function create($servers, $websites, $email)
    {
        $postData = json_encode(array(
            'items' => array(
                array(
                    'item' => '360-MON-1M'
                ),
                array(
                    'item' => '360-MON-SRV-1M',
                    'quantity' => $servers
                ),
                array(
                    'item' => '360-MON-SITE-1M',
                    'quantity' => $websites
                ),
            ),
            'activationInfo' => array(
                'activated' => 1,
                'uid' => $email
            ),
        ));

        $ch =  CURLHelper::preparePOST(self::API_URL_BASE.'?return-key-state=yes', $this->username, $this->password, self::JSON_CONTENT_TYPE, $postData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);
       
        if (defined("WHMCS")) {
            logModuleCall(
                'p360monitoring',
                'create',
                $postData,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function retrieve($keyId)
    {
        $ch =  CURLHelper::prepareGET(self::API_URL_BASE.'/'.$keyId, $this->username, $this->password, self::JSON_CONTENT_TYPE);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);
        
        if (defined("WHMCS")) {
            logModuleCall(
                'p360monitoring',
                'update',
                $keyId,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function update($servers, $websites, $keyId)
    {
        $postData = json_encode(array(
            'items' => array(
                array(
                    'item' => '360-MON-1M'
                ),
                array(
                    'item' => '360-MON-SRV-1M',
                    'quantity' => $servers
                ),
                array(
                    'item' => '360-MON-SITE-1M',
                    'quantity' => $websites
                ),
            ),
        ));
        $ch =  CURLHelper::preparePUT(self::API_URL_BASE.'/'.$keyId.'?return-key-state=yes', $this->username, $this->password, self::JSON_CONTENT_TYPE, $postData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);
        
        if (defined("WHMCS")) {
            logModuleCall(
                'p360monitoring',
                'update',
                $postData,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function suspend($suspended, $keyId)
    {
        $postData = json_encode(array('suspended' => $suspended));

        $ch =  CURLHelper::preparePUT(self::API_URL_BASE.'/'.$keyId.'?return-key-state=yes', $this->username, $this->password, self::JSON_CONTENT_TYPE, $postData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);
        
        if (defined("WHMCS")) {
            logModuleCall(
                'p360monitoring',
                'suspend',
                $postData,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function delete($keyId)
    {
        $ch =  CURLHelper::prepareDELETE(self::API_URL_BASE.'/'.$keyId.'?return-key-state=yes', $this->username, $this->password);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);

        if (defined("WHMCS")) {
            logModuleCall(
                'p360monitoring',
                'delete',
                '',
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function processResponse($response)
    {
        return json_decode($response, true);
    }

}