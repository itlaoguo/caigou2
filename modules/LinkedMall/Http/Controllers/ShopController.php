<?php
declare(strict_types=1);

namespace Modules\LinkedMall\Http\Controllers;

use Catch\Base\CatchController as Controller;
use Catch\Exceptions\FailedException;
use Illuminate\Http\Request;


class ShopController extends Controller
{
    public function __construct(
        protected readonly Shop $shopModel,
    ){}

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {

        $results = $this->shopModel->getList();

        return $results;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $result = $this->shopModel->firstBy($id);
        if(!$result){
            throw new FailedException('不存在的店铺');
        }

        return $result;

    }


}
