<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $callback_url = 'https://httpbin.org/get';
        $request_token_url = 'http://silver.readytogo.gr/oauth/initiate';
        $authorization_url = 'http://silver.readytogo.gr/admin/oauth_authorize';
        $consumer_key = 'd0dfb1f1c192559714be8025e77a2a46';
        $consumer_secret = 'f95380ffb53546c95929f51ea380c158';
        try {
            $client = new \OAuth($consumer_key, $consumer_secret);
            $client->enableDebug();
            var_dump($client->getLastResponse());

//            $token['oauth_token'];
//            $token['oauth_token_secret'];
//            $token['oauth_callback_confirmed'];
            $token = $client->getRequestToken($request_token_url,$callback_url, OAUTH_HTTP_METHOD_GET);
            var_dump($token);
            $res = $client->fetch($authorization_url, ['oauth_token'=>$token['oauth_token']],OAUTH_HTTP_METHOD_POST);
            var_dump($res);
        }catch (OAuthException $e){
            print_r($e);
        }
    }
}
