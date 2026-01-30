<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Modules\LinkedMall\Models\LinkMall;
use Modules\LinkedMall\Models\Shop;

class GetShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-shop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取店铺信息';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->getShopList();

            return 0;
        }catch (\Exception $exception){

            $this->error($exception->getMessage());
            return 1;

        }
    }

    private function getShopList(){
        //
        try {

            $this->info('发起linkedmall接口请求...');

            $client = new LinkMall();

            $page = 1;
            $page_size = 20;

            while (true){

                $parameters = array(
                    'page' => $page,
                    'pageSize' => $page_size
                );

                $response = $client->getPurchaserShops($parameters);

                if(empty($response)){
                    break;
                }

                foreach ($response as $shop){

                    $shopObj = new Shop();

                    $shopObj->shop_id = $shop['shopId'];
                    $shopObj->purchaser_id = $shop['shopId'];
                    $shopObj->shop_type = $shop['shopType'];
                    $shopObj->shop_name = $shop['shopName'];
                    $shopObj->cooperation_shops = json_encode($shop['cooperationShops']);
                    $shopObj->status = $shop['status'];

                    $shopObj->save();

                }

                if(count($response)<$page_size){
                    break;
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
