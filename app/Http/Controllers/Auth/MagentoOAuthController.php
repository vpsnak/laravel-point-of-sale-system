<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\MagentoOAuth;
use Illuminate\Http\Request;
use OAuth;
use OAuthException;

class MagentoOAuthController extends Controller
{
    protected $state;
    protected $token;
    protected $secret;
    
    protected $oauth_client;
    
    protected $consumer_key = 'd0dfb1f1c192559714be8025e77a2a46';
    protected $consumer_secret = 'f95380ffb53546c95929f51ea380c158';
    
    protected $callback_url = 'http://localhost:8000/api/magento/authorize';
    protected $request_token_url = 'http://silver.readytogo.gr/oauth/initiate';
    protected $admin_authorization_url = 'http://silver.readytogo.gr/admin/oauth_authorize';
    protected $access_token_url = 'http://silver.readytogo.gr/oauth/token';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!MagentoOAuth::checkExists('key', 'state')) {
            MagentoOAuth::store(['key' => 'state', 'value' => 0]);
        }
        if (!MagentoOAuth::checkExists('key', 'token')) {
            MagentoOAuth::store(['key' => 'token']);
        }
        if (!MagentoOAuth::checkExists('key', 'secret')) {
            MagentoOAuth::store(['key' => 'secret']);
        }
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
            report($e);
        }
    }
    
    public function authorizeMagento(Request $request)
    {
        $oauth_token = $request->input('oauth_token');
        if (!isset($oauth_token) && $this->state->value == 0) {
            $this->state->value = 0;
            $this->state->save();
        }
        try {
            if (!isset($oauth_token) && !$this->state->value) {
                $request_token = $this->oauth_client->getRequestToken($this->request_token_url, $this->callback_url,
                    OAUTH_HTTP_METHOD_GET);
                $this->secret->value = $request_token['oauth_token_secret'];
                $this->secret->save();
                $this->state->value = 1;
                $this->state->save();
                return redirect($this->admin_authorization_url . '?oauth_token=' . $request_token['oauth_token']);
            } else {
                if ($this->state->value == 1) {
                    $this->oauth_client->setToken($oauth_token, $this->secret->value);
                    $access_token = $this->oauth_client->getAccessToken($this->access_token_url, null, null,
                        OAUTH_HTTP_METHOD_GET);
                    $this->state->value = 2;
                    $this->state->save();
                    $this->token->value = $access_token['oauth_token'];
                    $this->token->save();
                    $this->secret->value = $access_token['oauth_token_secret'];
                    $this->secret->save();
                    return redirect($this->callback_url);
                } else {
                    // do request
                    $this->oauth_client->setToken($this->token->value, $this->secret->value);
                    $this->oauth_client->fetch('http://silver.readytogo.gr/api/rest/products', [], 'GET',
                        ['Content-Type' => 'application/json', 'Accept' => '*/*']);
                    $response = $this->oauth_client->getLastResponse();
                    return response()->json([
                        'status' => 'active',
                        'message' => "
                         (         (
                         )\ )      )\ )  *   )
                        (()/( (   (()/(` )  /(      )        (
                         /(_)))\   /(_))( )(_))  ( /(  `  )  )\
                        (_)) ((_) (_)) (_(_())   )(_)) /(/( ((_)
                        | _ \| __|/ __||_   _|  ((_)_ ((_)_\ (_)
                        |   /| _| \__ \  | |    / _` || '_ \)| |
                        |_|_\|___||___/  |_|    \__,_|| .__/ |_|
                                                      |_|
                        "
                    ]);
                }
            }
        } catch (OAuthException $e) {
            report($e);
            return response()->json([
                'status' => 'inactive',
                'message' => 'There was an error. You can not access data over REST api.'
            ]);
        }
    }
    
}
