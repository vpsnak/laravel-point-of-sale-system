<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use OAuth;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'w2:test';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // oob
        $callback_url = 'https://httpbin.org/get';
        $request_token_url = 'http://silver.readytogo.gr/oauth/initiate';
        $authorization_confrim_url = 'http://silver.readytogo.gr/admin/oauth_authorize/confirm';
        $access_token_url = 'http://silver.readytogo.gr/oauth/token';
        $consumer_key = 'd0dfb1f1c192559714be8025e77a2a46';
        $consumer_secret = 'f95380ffb53546c95929f51ea380c158';
        
        $token = '093c756b89b36bcf697ba925fe62d8df';
        $secret = '34e3b6245efb905dad0e935722ce9a47';
        $verifier = '36d8f852932ccd25b1279822e53811be';
        
        $resource_url = 'http://silver.readytogo.gr/api/rest/customers';
        try {
            $client = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
            $client->enableDebug();
            
            //            $token['oauth_token'];
            //            $token['oauth_token_secret'];
            //            $token['oauth_callback_confirmed'];
            $temp_token = $client->getRequestToken($request_token_url, $callback_url, OAUTH_HTTP_METHOD_GET);
            var_dump($temp_token);
            var_dump($this->confirmAuthorization($temp_token['oauth_token'], $authorization_confrim_url));
            
            // @TODO get formkey and post authorize confirm to authenticate
            
            
            //            $client->setToken($token['oauth_token'], $token['oauth_token_secret']);
            //            $client->setToken($token, $secret);
            //            $response = $client->fetch($resource_url, [], 'GET', ['Content-type' => 'application/json']);
            //            var_dump($response);
        } catch (OAuthException $e) {
            print_r($e);
        }
    }
    
    public function getMagentoFormKey($token, $url)
    {
        $client = new Client();
        $result = $client->request('GET', $url, [
            'query' => [
                'oauth_token' => $token
            ]
        ]);
        $form_html = $result->getBody()->getContents();
        preg_match('/<input name="form_key" type="hidden" value="(.*?)"/s', $form_html, $form_key);
        return $form_key[1];
    }
    
    public function confirmAuthorization($token, $url)
    {
        $user = 'dev';
        $password = 'admin123!@#';
        $authorization_url = 'http://silver.readytogo.gr/admin/oauth_authorize';
        $form_key = $this->getMagentoFormKey($token, $authorization_url);
        var_dump($form_key);
        $client = new Client();
        $result = $client->request('POST', $url, [
            'form_params' => [
                'login[username]' => $user,
                'login[password]' => $password,
                'form_key' => $form_key,
            ]
        ]);
        return $result->getBody()->getContents();
    }
}
