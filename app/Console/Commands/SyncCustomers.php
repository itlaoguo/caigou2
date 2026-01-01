<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class SyncCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取erp会员信息';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getCustomers();
    }

    private function getCustomers(){
        //
        try {

            $this->info('发起erp接口请求...');

            $client = new Client([
                'base_uri' => 'https://www.iferp.local/',    // 基础 URI
                'verify' => false,
            ]);

            $page = 474;
            $page_size = 100;

            while (true){
                $parameters = array(
                    'page' => $page,
                    'page_size' => $page_size
                );

                $response = $client->get('synch/Customer.php',array('query'=>$parameters));
                $result = json_decode($response->getBody(), true);

                if($result['status'] === 0 || empty($result['data'])){
                    break;
                }

                // 清洗数据
                $this->info("处理第 {$page} 页数据");

                foreach ($result['data'] as $_customer){
//                    if(!empty($contacts)){
//                        dd($_customer);
//                    }

                    //联系人
                    $contacts = array();
                    $main_contact_id = intval($_customer['contacts_id']);
                    if(!empty($_customer['contacts'])){

                        foreach ($_customer['contacts'] as $_contact){
                            $contacts[] = array(
                                'name'=> $_contact['name'],
                                'sex'=>  $_contact['sex'],
                                'email'=> $_contact['email'],
                                'telephone'=> $_contact['telephone'],
                                'qq'=> $_contact['qq'],
                                'wechat'=> '',
                                'whatsapp'=> '',
                                'note'=> $_contact['description'],
                                'is_main'=> ($main_contact_id == $_contact['contacts_id'])?1:0,
                                'created_by'=> $_contact['creator_role_id'],
                                'created_at'=> $_contact['create_time'],
                                'updated_at'=> $_contact['update_time'],
                                'deleted_at'=> $_contact['delete_time'],
                            );

                        }

                    }

                    /*获取用户email和telephone，先5k_customer表获取，获取不到;再在主联系人数据中获取，依然获取不到则在其他联系人获取*/
                    $email = trim($_customer['email']);
                    $telephone = trim($_customer['phone']);

                    if($email == '' && !empty($contacts)){//主联系人
                        foreach ($contacts as $_contact){

                            if($_contact['is_main'] == 1 && $_contact['email'] !=''){

                                $email = $_contact['email'];
                                break;
                            }

                        }
                    }

                    if($email == '' && !empty($contacts)){
                        foreach ($contacts as $_contact){//非主联系人

                            if($_contact['email'] !=''){

                                $email = $_contact['email'];
                                break;
                            }

                        }
                    }

                    if($telephone == '' && !empty($contacts)){//主联系人
                        foreach ($contacts as $_contact){

                            if($_contact['is_main'] == 1 && $_contact['telephone'] !=''){

                                $telephone = $_contact['telephone'];
                                break;
                            }

                        }
                    }

                    if($telephone == '' && !empty($contacts)){
                        foreach ($contacts as $_contact){//非主联系人

                            if($_contact['telephone'] !=''){

                                $telephone = $_contact['telephone'];
                                break;
                            }

                        }
                    }

                    if($email == '' && $telephone == ''){//当两个参数都没有值,记录到用户异常表if_customer_exception
                        DB::table('customer_exception')->insert(['customer_id' => $_customer['customer_id']]);
                        continue;
                    }

                    DB::transaction(function () use ($_customer, $email, $telephone, $contacts) {

                        //判断当前customer表是否已添加该用户信息
                        $customerInfo = DB::table('customer')->where('email',$email)->orWhere('telephone',$telephone)->first();

                        if ($customerInfo){

                            if($customerInfo->email=='' && $email!='' ){
                                DB::table('customer')->where('telephone', $telephone)->update(['email' => $email,'updated_at' => date('Y-m-d H:i:s',intval($_customer['updated_at']))]);
                            }

                            if($customerInfo->telephone=='' && $telephone!='' ){
                                DB::table('customer')->where('email', $email)->update(['telephone' => $telephone,'updated_at' => date('Y-m-d H:i:s',intval($_customer['updated_at']))]);
                            }

                            $customer_id = $customerInfo->id;

                        }else{

                            //客户信息
                            $customer = [
                                'email' => $email,
                                'telephone' => $telephone,
                                'created_by' => intval($_customer['creator_role_id']),
                                'updated_by' => intval($_customer['creator_role_id']),
                                'created_at' => intval($_customer['create_time'])>0?date('Y-m-d H:i:s',intval($_customer['create_time'])):null,
                                'updated_at' => intval($_customer['update_time'])>0?date('Y-m-d H:i:s',intval($_customer['update_time'])):null
                            ];

                            $customer_id = DB::table('customer')->insertGetId($customer);

                        }

                        //客户customer platform信息
                        $customerPlatform = [
                            'customer_id' => $customer_id,
                            'platform_code' => trim($_customer['platform_code']),
                            'name' => trim($_customer['name'])
                        ];

                        DB::table('customer_platform')->upsert(
                            $customerPlatform,
                            ['customer_id', 'platform_code'],
                            ['name'] // 更新字段
                        );

                        //联系地址:首先是用户表的address字段，再次是contacts表的联系人的address字段
                        $customer_addresses = array();
                        if(
                            trim($_customer['country_name'])!=''
                            || trim($_customer['country'])
                            || trim($_customer['province_state'])!=''
                            || trim($_customer['city'])!=''
                            || $_customer['address']!=''
                        ){
                            $customer_address_temp = DB::table('customer_address')
                                ->where('customer_id', '=',$customer_id)
                                ->where('country', '=',trim($_customer['country_name']))
                                ->where('country_code', '=',trim($_customer['country']))
                                ->where('province', '=',trim($_customer['province_state']))
                                ->where('city', '=',trim($_customer['city']))
                                ->where('street_address', '=',trim($_customer['street_address']))
                                ->where('zipcode', '=',trim($_customer['zipcode']))
                                ->where('other', '=',trim($_customer['address']))
                                ->first();
                            if(!$customer_address_temp){
                                $customer_addresses[] = [
                                    'customer_id' => $customer_id,
                                    'country' => trim($_customer['country_name']),
                                    'country_code' => trim($_customer['country']),
                                    'province' => trim($_customer['province_state']),
                                    'city' => trim($_customer['city']),
                                    'street_address' => trim($_customer['street_address']),
                                    'zipcode' => trim($_customer['zipcode']),
                                    'note' => trim($_customer['description']),
                                    'other' => trim($_customer['address']),
                                    'created_at'=> intval($_customer['create_time'])>0?date('Y-m-d H:i:s',intval($_customer['create_time'])):null,
                                    'updated_at'=> intval($_customer['create_time'])>0?date('Y-m-d H:i:s',intval($_customer['create_time'])):null,
                                    'deleted_at'=> intval($_customer['delete_time'])>0 ? date('Y-m-d H:i:s',intval($_customer['delete_time'])):null,
                                ];
                            }

                        }

                        if (!empty($_customer['contacts'])) {
                            foreach ($_customer['contacts'] as $_contact) {

                                $customer_address_temp = DB::table('customer_address')
                                    ->where('customer_id', '=',$customer_id)
                                    ->where('country', '=',trim($_contact['country']))
                                    ->where('province', '=',trim($_contact['province']))
                                    ->where('city', '=',trim($_contact['city']))
                                    ->where('street_address', '=',trim($_contact['address']))
                                    ->where('zipcode', '=',trim($_contact['zip_code']))
                                    ->where('other', '=',trim($_contact['address']))
                                    ->first();

                                if(!$customer_address_temp){
                                    $customer_addresses[] = [
                                        'customer_id' => $customer_id,
                                        'country' => $_contact['country'],
                                        'country_code' => '',
                                        'province' => $_contact['province'],
                                        'city' => $_contact['city'],
                                        'street_address' => $_contact['address'],
                                        'zipcode' => $_contact['zip_code'],
                                        'other' => $_contact['address'],
                                        'note' => '',
                                        'created_at'=> intval($_contact['create_time'])>0?date('Y-m-d H:i:s',intval($_contact['create_time'])):null,
                                        'updated_at'=> intval($_contact['create_time'])>0?date('Y-m-d H:i:s',intval($_contact['create_time'])):null,
                                        'deleted_at'=> intval($_contact['delete_time'])>0 ? date('Y-m-d H:i:s',intval($_contact['delete_time'])):null,
                                    ];
                                }
                            }
                        }

                        if(!empty($customer_addresses)){
                            DB::table('customer_address')->insert($customer_addresses);
                        }

                        //联系人
                        if(!empty($contacts)){

                            foreach ($contacts as $_c){
                                $_cs = array(
                                    'customer_id' => $customer_id,
                                    'name'=> $_c['name'],
                                    'sex'=>  $_c['sex'],
                                    'email'=> $_c['email'],
                                    'telephone'=> $_c['telephone'],
                                    'qq'=> $_c['qq'],
                                    'wechat'=> '',
                                    'whatsapp'=> '',
                                    'note'=> $_c['note'],
                                    'is_main'=> $_c['is_main'],
                                    'created_by'=> $_c['creator_id'],
                                    'created_at'=> intval($_contact['create_time'])>0?date('Y-m-d H:i:s',intval($_contact['create_time'])):null,
                                    'updated_at'=> intval($_contact['create_time'])>0?date('Y-m-d H:i:s',intval($_contact['create_time'])):null,
                                    'deleted_at'=> intval($_contact['delete_time'])>0 ? date('Y-m-d H:i:s',intval($_contact['delete_time'])):null,
                                );


                                DB::table('customer_contact')->upsert(
                                    $_cs,
                                    ['customer_id', 'name'],
                                    ['name','sex','email','telephone','qq','wechat','whatsapp','note','is_main','created_by','created_at','updated_at','deleted_at'] // 更新字段
                                );

                            }

                        }


                    });


                }

                // 页码递增
                $page++;
            }

            return 0;


        }
        catch (\Exception $e) {

            $this->error($e->getMessage());
            return 1;
        }

    }

    private function getCustomers2(){

        try {

            $this->info('发起erp接口请求...');

            $client = new Client([
                'base_uri' => 'https://www.iferp.local/',    // 基础 URI
                'verify' => false,
            ]);

            $page = 1;
            $page_size = 100;

            while (true){
                $parameters = array(
                    'page' => $page,
                    'page_size' => $page_size
                );

                $response = $client->get('synch/Customer.php',array('query'=>$parameters));
                $result = json_decode($response->getBody(), true);
                if($result['status'] === 0 || empty($result['data'])){
                    break;
                }
//                echo '<pre>';print_r($result['data']);exit();
                // 清洗数据
                $this->info("处理第 {$page} 页数据");
                foreach ($result['data'] as $_customer){

                    //客户主表信息
                    $customer = [
                        'id' => $_customer['customer_id'],
                        'name' => trim($_customer['name'])!=''?trim($_customer['name']):'-',
                        'created_by' => intval($_customer['creator_role_id']),
                        'updated_by' => intval($_customer['creator_role_id']),
                        'created_at' => intval($_customer['create_time'])>0?date('Y-m-d H:i:s',intval($_customer['create_time'])):NULL,
                        'updated_at' => intval($_customer['update_time'])>0?date('Y-m-d H:i:s',intval($_customer['update_time'])):NULL,
                        'deleted_at' => intval($_customer['delete_time'])>0?date('Y-m-d H:i:s',intval($_customer['delete_time'])):NULL
                    ];

                    if(intval($_customer['customer_id'])<=0){
                        print_r($_customer);
                        continue;
                    }

                    DB::table('customer')->insert($customer);

                }

                // 页码递增
                $page++;
            }

            return 0;


        }
        catch (\Exception $e) {

            $this->error($e->getMessage());
            return 1;
        }

    }

    private function deepseek(){

        $apiKey = 'sk-0a82ae890fa241a183ac2b510dc65df33';
        $apiUrl = "https://api.deepseek.com/v1/chat/completions";

        // 构造请求数据
        $data = [
            "model" => "deepseek-chat",
            "messages" => [
                ["role" => "user", "content" => "用PHP写一个快速排序算法"]
            ],
            "temperature" => 1.0,
            "max_tokens" => 1000
        ];

        // 初始化cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>0,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $apiKey,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        // 发送请求并处理响应
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            die("请求失败: " . curl_error($ch));
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            die("API返回错误状态码: " . $httpCode);
        }

        $result = json_decode($response, true);
        echo "模型回复:\n" . $result['choices'][0]['message']['content'];
    }
}
