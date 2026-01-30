<?php

declare(strict_types=1);

namespace Modules\LinkedMall\Models;


//use Catch\Base\CatchModel as Model;
use Catch\Traits\DB\BaseOperate;
use Catch\Traits\DB\ScopeTrait;
use Catch\Traits\DB\Trans;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $number
 * @property $name
 * @property $creator_id
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
*/
class Shop extends Model
{

    use BaseOperate, Trans, ScopeTrait;

    protected $table = 'shop';

    protected $fillable = [ 'id', 'shop_id', 'distributor_id','purchaser_id', 'shop_type','shop_name','cooperation_shops','end_date','start_date','status', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected array $fields = ['id','shop_id', 'distributor_id','purchaser_id', 'shop_type','shop_name','cooperation_shops','end_date','start_date','status', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected array $form = ['shop_id', 'distributor_id','purchaser_id', 'shop_type','shop_name','cooperation_shops','end_date','start_date','status', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    public array $searchable = [
        'shop_name' => 'like',
    ];

    protected bool $isPaginate = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];


    public static function bootSoftDeletes(): void
    {
        // 不添加软删除全局作用域
    }


}
