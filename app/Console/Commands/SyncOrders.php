<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class SyncOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步erp订单数据';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->getOrders();

    }

    private function getOrders(){


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

                $response = $client->get('synch/Order.php',array('query'=>$parameters));
                $result = json_decode($response->getBody(), true);
//                echo '<pre>';print
                if($result['status'] === 0 || empty($result['data'])){
                    break;
                }

                // 清洗数据
                $this->info("处理第 {$page} 页数据");

                foreach ($result['data'] as $_order){

                    DB::transaction(function () use ($_order) {

//                        echo '<Pre>';print_r($_order);exit();

                        //订单信息
                        $order = [
                            'order_id'=>$_order['sales_id'],
                            'company_id'=> 1,//都是翼飞智能
                            'platform_code'=>'ERP',
                            'customer_email' => $_order['email'],
                            'customer_telephone' => $_order['phone'],
                            'number' => $_order['sn_code'],
                            'subject' => $_order['subject'],
                            'total' => $_order['settlement_amount'],
                            'pay_status' => $_order['collection_status'],
                            'note' => $_order['description'],
                            'sales_at'=> $_order['create_time'],
                            'created_by' => $_order['creator_role_id'],
//                            'updated_by' => $_order['creator_role_id'],
                            'created_at' => date('Y-m-d H:i:s',intval($_order['create_time'])),
                            'updated_at' => intval($_order['last_time'])>0?date('Y-m-d H:i:s',intval($_order['last_time'])):null,
                            'deleted_at' => null,
                        ];

                        DB::table('order')->insert($order);

                        //订单产品
                        if(!empty($_order['products'])){
                            $_products = [];
                            foreach ($_order['products'] as $_product){

                                if(!$_product['name']){
                                    $_product['name'] = '-';
                                    continue;
                                }
                                $_products[] = array(
                                    'order_id'=>$_product['sales_id'],
                                    'product_id'=>$_product['product_id'],
                                    'product_name'=>$_product['name'],
                                    'product_image'=>$_product['product_image'],
                                    'product_sku'=>$_product['product_sku'],
                                    'price'=>$_product['unit_price'],
                                    'currency'=>$_product['currency'],
                                    'quantity'=>$_product['amount'],
                                    'unit'=>$_product['unit'],
                                    'note'=>$_product['description'],
                                );
                            }

                            DB::table('order_product')->insert($_products);
                        }

                        //订单收货地址
                        DB::table('order_shipping_address')->insert([
                            'order_id'=>$_order['sales_id'],
                            'contact_name' => '',
                            'contact_telephone' => '',
                            'country'=>'',
                            'country_code'=>'',
                            'province'=>'',
                            'city'=>'',
                            'address'=>'',
                            'other_address'=>$_order['shipping_address'],
                            'zipcode'=>'',
                            'note'=>''
                        ]);

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
}
