<?php

namespace App\Magento;

use App\MagentoOAuth;
use Exception;
use OAuth;
use OAuthException;

class MagentoClient
{
    const ADMIN_TOKEN_URL = '/rest/V1/integration/admin/token';
    protected $credentials;

    protected $baseUrl = 'http://silver.readytogo.gr/api/rest/';

    protected $consumer_key = 'd0dfb1f1c192559714be8025e77a2a46';
    protected $consumer_secret = 'f95380ffb53546c95929f51ea380c158';

    protected $state;
    protected $token;
    protected $secret;

    protected $oauth_client;

    public function __construct()
    {
        $this->state = MagentoOAuth::getFirst('key', 'state');
        $this->token = MagentoOAuth::getFirst('key', 'token');
        $this->secret = MagentoOAuth::getFirst('key', 'secret');

        $auth_type = ($this->state->value == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
        try {
            $this->oauth_client = new OAuth($this->consumer_key, $this->consumer_secret, OAUTH_SIG_METHOD_HMACSHA1,
                $auth_type);
            $this->oauth_client->enableDebug();
            $this->oauth_client->setSSLChecks(OAUTH_SSLCHECK_NONE);
        } catch (OAuthException $e) {
            print_r($e->getMessage());
        }
    }

    public function get($url, $params)
    {
        return $this->request('GET', $url, $params);
    }

    public function request($method, $url, $options = [])
    {
        echo $method . ' ' . $url . PHP_EOL;
        try {
            $this->oauth_client->setToken($this->token->value, $this->secret->value);
            echo 'token set' . PHP_EOL;
            $this->oauth_client->fetch(
                $this->baseUrl . $url,
                json_encode($options),
                $method,
                ['Content-Type' => 'application/json', 'Accept' => 'application/json']
            );
            echo 'request send' . PHP_EOL;
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

    public function request2($method, $url, $options = "")
    {
        try {
            $this->oauth_client->setToken($this->token->value, $this->secret->value);
            echo 'token set' . PHP_EOL;
            $this->oauth_client->fetch(
                $this->baseUrl . $url,
                json_encode($options),
                $method,
                ['Content-Type' => 'application/json', 'Accept' => 'application/json']
            );
            echo 'request send' . PHP_EOL;
            $response = $this->oauth_client->getLastResponse();
            return json_decode($response);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }
}
