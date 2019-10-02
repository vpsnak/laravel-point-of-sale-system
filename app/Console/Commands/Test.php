<?php

namespace App\Console\Commands;

use App\Http\Controllers\Magento\Customer;
use GuzzleHttp\Client;
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
        // oob
        $callback_url = 'https://httpbin.org/get';
        $customer = \App\Customer::find(9);
        $client = new Customer();
//        $res = $client->create([
//            'website_id' => 1,
//            'group_id' => 1,
//            'firstname' => $customer->first_name,
//            'lastname' => $customer->last_name,
//            'email' => $customer->email,
//            'password' => 'asd123123',
//            'eponymia' => $customer->company_name,
//        ]);
        $res = $client->getByField('email', $customer->email);
        var_dump($res);
    }

    public function getMagentoFormKey($token, $url)
    {
        $client = new Client();
        $result = $client->get($url, [
            'query' => [
                'oauth_token' => $token
            ]
        ]);
        $form_html = $result->getBody()->getContents();
        preg_match('/<input name="form_key" type="hidden" value="(.*?)"/s', $form_html, $form_key);
        preg_match('/<form method="post" action="(.*?)"/s', $form_html, $form_action);
        return [
            'form_key' => $form_key[1],
            'form_action' => $form_action[1]
        ];
    }

    public function confirmAuthorization($token, $url)
    {
        $user = 'dev';
        $password = 'admin123!@#';
        $authorization_url = 'http://silver.readytogo.gr/admin/oauth_authorize';
        $authorization_confirm_url = 'http://silver.readytogo.gr/admin/oauth_authorize/confirm';
        $form_data = $this->getMagentoFormKey($token, $authorization_url);
        var_dump($form_data);


        $client = new Client();
        $result = $client->post($authorization_confirm_url, [
            'query' => [
                'oauth_token' => $token,
                'login[username]' => $user,
                'login[password]' => $password,
                'form_key' => $form_data['form_key'],
            ],
            'form_params' => [
                'oauth_token' => $token,
                'login[username]' => $user,
                'login[password]' => $password,
                'form_key' => $form_data['form_key'],
            ]
        ]);
        preg_match('/<div class="login-form"(.*?)class="legal"/s', $result->getBody()->getContents(), $form_result);
        return $form_result[1];
    }
}
