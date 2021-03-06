<?php

namespace App\Magento;

use App\Setting;
use Exception;
use OAuth;

class MagentoClient
{
    const ADMIN_TOKEN_URL = '/rest/V1/integration/admin/token';
    protected $credentials;

    protected $baseUrl;

    protected $consumer_key;
    protected $consumer_secret;

    protected $state;
    protected $token;
    protected $secret;

    protected $oauth_client;

    public function __construct()
    {
        $this->initClient();
    }

    public function initClient()
    {
        $this->baseUrl = config('magento.MAGENTO_URL') . '/api/rest/';
        $this->consumer_key = config('magento.OAUTH_CONSUMER_KEY');
        $this->consumer_secret = config('magento.OAUTH_CONSUMER_SECRET');
        $this->state = Setting::where('key', 'state')->firstOrFail();
        $this->token = Setting::where('key', 'token')->firstOrFail();
        $this->secret = Setting::where('key', 'secret')->firstOrFail();

        $auth_type = ($this->state->value === '2') ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
        try {
            $this->oauth_client = new OAuth(
                $this->consumer_key,
                $this->consumer_secret,
                OAUTH_SIG_METHOD_HMACSHA1,
                $auth_type
            );
            $this->oauth_client->enableDebug();
            $this->oauth_client->setSSLChecks(OAUTH_SSLCHECK_NONE);
            $this->oauth_client->setToken($this->token->value, $this->secret->value);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function get($url, $params)
    {
        return $this->request('GET', $url, $params);
    }

    public function request($method, $url, $options = [])
    {
        $this->initClient();
        if (app()->runningInConsole()) {
            echo "{$method} {$url}" . PHP_EOL;
        }
        try {
            $this->oauth_client->fetch(
                $this->baseUrl . $url,
                $options,
                $method,
                ['Content-Type' => 'application/json', 'Accept' => 'application/json']
            );
            if (app()->runningInConsole()) {
                echo 'request send' . PHP_EOL;
            }
            $response = $this->oauth_client->getLastResponse();
            return json_decode($response);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }

    public function post($url, $data)
    {
        return $this->request('POST', $url, json_encode($data));
    }

    public function put($url, $data)
    {
        return $this->request('PUT', $url, json_encode($data));
    }
}
