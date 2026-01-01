<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步erp产品数据';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->getProducts();

    }

    private function getProducts(){


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

                $response = $client->get('synch/Product.php',array('query'=>$parameters));
                $result = json_decode($response->getBody(), true);
//                echo '<pre>';print
                if($result['status'] === 0 || empty($result['data'])){
                    break;
                }

                // 清洗数据
                $this->info("处理第 {$page} 页数据");

                foreach ($result['data'] as $_product){
//                    echo '<pre>';print_r($_product);echo '</pre>';exit();
                    //订单信息
                    $product = [
                        'product_id'=>$_product['product_id'],
                        'name' => $_product['name'],
                        'main_image'=> $_product['main_image'],
                        'creator_id' => $_product['creator_role_id'],
                        'created_at' => intval($_product['created_time']),
                        'updated_at' => intval($_product['updated_time']),
                    ];

                    DB::table('products')->insert($product);

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
