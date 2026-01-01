<?php

declare(strict_types=1);

namespace Modules\Base\Models;

use Catch\Base\CatchModel as Model;

/**
 * @property $id
 * @property $code
 * @property $address_format
 * @property $sort_order
 * @property $creator_id
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
*/
class Country extends Model
{
    

    protected $table = 'country';

    protected $fillable = [ 'id', 'code', 'address_format', 'sort_order', 'creator_id', 'created_at', 'updated_at', 'deleted_at' ];

    /**
     * @var array
     */
    protected array $fields = ['id','code','address_format','sort_order','created_at','updated_at'];

    /**
     * @var array
     */
    protected array $form = ['code'];

    /**
     * @var array
     */
    public array $searchable = [
        
    ];

    
}
