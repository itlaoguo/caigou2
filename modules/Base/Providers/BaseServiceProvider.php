<?php

namespace Modules\Base\Providers;

use Catch\CatchAdmin;
use Catch\Providers\CatchModuleServiceProvider;

class BaseServiceProvider extends CatchModuleServiceProvider
{
    /**
     * route path
     *
     * @return string
     */
    public function moduleName(): string
    {
        // TODO: Implement path() method.
        return 'base';
    }
}
