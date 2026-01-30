<?php

namespace Modules\LinkedMall\Providers;

use Catch\CatchAdmin;
use Catch\Providers\CatchModuleServiceProvider;

class LinkedMallServiceProvider extends CatchModuleServiceProvider
{
    /**
     * route path
     *
     * @return string
     */
    public function moduleName(): string
    {
        // TODO: Implement path() method.
        return 'linkedmall';
    }
}
