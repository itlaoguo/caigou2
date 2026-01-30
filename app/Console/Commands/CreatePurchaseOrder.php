<?php

namespace App\Console\Commands;

use AlibabaCloud\SDK\Linkedmall\V20230930\Models\AddressInfo;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\CreatePurchaseOrderRequest;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\ProductDTO;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\PurchaseOrderCreateCmd;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Tea;
use Catch\Exceptions\FailedException;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Modules\LinkedMall\Models\LinkMall;

class CreatePurchaseOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-purchase-order';

    /**
     * 提交创建采购单请求到阿里linkedmall
     *
     * @var string
     */
    protected $description = '创建采购单';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //获取所有待运行采购单，循环提交每个采购单到linkedmall
        try {
            $this->getShopList();

            return 0;
        }catch (\Exception $exception){

            $this->error($exception->getMessage());
            return 1;

        }
    }

    private function createPurchaseOrder($paramters){

        $purchaseOrderCreateCmdProductListProductDTO0 = new ProductDTO([
            "purchaserId" => "PID2200006482",
            "quantity" => $paramters['quantity'],
            "productId" => $paramters['product_id'],
            "skuId" => $paramters['sku_id'],
        ]);
        $purchaseOrderCreateCmdAddressInfo = new AddressInfo([
            "receiver" => $paramters['receiver'],
            "receiverPhone" =>  $paramters['receiver_phone'],
            "addressDetail" => $paramters['address_detail'],
        ]);
        $purchaseOrderCreateCmd = new PurchaseOrderCreateCmd([
            "outerPurchaseOrderId" => date('Ymd',time()).$paramters['id'],
            "buyerId" => $paramters['buyer_id']??"9527",
            "deliveryAddress" => $purchaseOrderCreateCmdAddressInfo,
            "productList" => [
                $purchaseOrderCreateCmdProductListProductDTO0
            ]
        ]);

        $createPurchaseOrderRequest = new CreatePurchaseOrderRequest([
            "body" => $purchaseOrderCreateCmd
        ]);
        $headers = [];
        try {
            // 复制代码运行请自行打印 API 的返回值
            $response = $this->client->createPurchaseOrderWithOptions($createPurchaseOrderRequest, $headers, $this->runtime);
            return Tea::merge($response->body);
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }

            $errorData = [
                'message' => $error->message ?? '未知错误',
                'code' => $error->code ?? '未知错误码'
            ];

            throw new FailedException('LinkedMall API调用失败: ' . json_encode($errorData, JSON_UNESCAPED_UNICODE));
        }


    }

}
