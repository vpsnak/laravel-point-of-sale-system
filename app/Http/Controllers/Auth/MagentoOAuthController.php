<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use OAuth;
use OAuthException;

class MagentoOAuthController extends Controller
{
    protected $state;
    protected $token;
    protected $secret;

    protected $oauth_client;

    protected $consumer_key;
    protected $consumer_secret;

    protected $callback_url = '/api/magento/authorize';
    protected $request_token_url = '/oauth/initiate';
    protected $admin_authorization_url = '/oauth_authorize';
    protected $access_token_url = '/oauth/token';

    public function authorizeMagento(Request $request)
    {
        if (!isset($this->oauth_client)) {
            $this->initOAuth();
        }
        $oauth_token = $request->input('oauth_token');
        if (!isset($oauth_token) && $this->state->value === '0') {
            $this->state->value = 0;
            $this->state->save();
        }
        try {
            if (!isset($oauth_token) && !$this->state->value) {
                $request_token = $this->oauth_client->getRequestToken(
                    $this->request_token_url,
                    $this->callback_url,
                    OAUTH_HTTP_METHOD_GET
                );
                $this->secret->value = $request_token['oauth_token_secret'];
                $this->secret->save();
                $this->state->value = 1;
                $this->state->save();
                return redirect($this->admin_authorization_url . '?oauth_token=' . $request_token['oauth_token']);
            } else {
                if ($this->state->value === '1') {
                    $this->oauth_client->setToken($oauth_token, $this->secret->value);
                    $access_token = $this->oauth_client->getAccessToken(
                        $this->access_token_url,
                        null,
                        null,
                        OAUTH_HTTP_METHOD_GET
                    );
                    if (array_key_exists('oauth_token', $access_token) || array_key_exists(
                        'oauth_token_secret',
                        $access_token
                    )) {
                        $this->state->value = 2;
                        $this->state->save();
                        $this->token->value = $access_token['oauth_token'];
                        $this->token->save();
                        $this->secret->value = $access_token['oauth_token_secret'];
                        $this->secret->save();
                        return redirect($this->callback_url);
                    } else {
                        $this->state->value = 0;
                        $this->state->save();
                        return response()->json([
                            'status' => 'bad_request',
                            'message' => 'Response was malformed.'
                        ]);
                    }
                } else {
                    // do request
                    $this->oauth_client->setToken($this->token->value, $this->secret->value);
                    $this->oauth_client->fetch(
                        config('magento.MAGENTO_URL') . '/api/rest/products',
                        [],
                        'GET',
                        ['Content-Type' => 'application/json', 'Accept' => '*/*']
                    );
                    return response()->json([
                        'status' => 'active',
                        'message' => "Authenticated"
                    ]);
                }
            }
        } catch (OAuthException $e) {
            report($e);
            Setting::truncate();
            return response()->json([
                'status' => 'exception',
                'message' => 'There was an error. You can not access data over REST api.'
            ], $this->oauth_client->getLastResponseInfo()['http_code']);
        }
    }

    private function initOAuth()
    {
        $magento_url = config('magento.MAGENTO_URL');
        $magento_admin = config('magento.MAGENTO_ADMIN');
        $this->consumer_key = config('magento.OAUTH_CONSUMER_KEY');
        $this->consumer_secret = config('magento.OAUTH_CONSUMER_SECRET');
        $this->callback_url = config('app.url') . '/api/magento/authorize';
        $this->request_token_url = "$magento_url/oauth/initiate";
        $this->admin_authorization_url = "$magento_url/$magento_admin/oauth_authorize";
        $this->access_token_url = $magento_url . '/oauth/token';

        if (!Setting::where('key', 'state')->exists()) {
            Setting::create(['key' => 'state', 'value' => 0]);
        }
        if (!Setting::where('key', 'token')->exists()) {
            Setting::create(['key' => 'token']);
        }
        if (!Setting::where('key', 'secret')->exists()) {
            Setting::create(['key' => 'secret']);
        }
        $this->state = Setting::where('key', 'state')->first();
        $this->token = Setting::where('key', 'token')->first();
        $this->secret = Setting::where('key', 'secret')->first();

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
        } catch (OAuthException $e) {
            report($e);
        }
    }
}
