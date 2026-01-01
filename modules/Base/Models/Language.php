<?php

declare(strict_types=1);

namespace Modules\Base\Models;

use Catch\Base\CatchModel as Model;

/**
 * @property $id
 * @property $name
 * @property $code
 * @property $sort_order
 * @property $creator_id
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
*/
class Language extends Model
{
    

    protected $table = 'language';

    protected $fillable = [ 'id', 'name', 'code', 'sort_order', 'creator_id', 'created_at', 'updated_at', 'deleted_at' ];

    /**
     * @var array
     */
    protected array $fields = ['id','name','code','sort_order','created_at','updated_at'];

    /**
     * @var array
     */
    protected array $form = ['name','code','sort_order'];

    /**
     * @var array
     */
    public array $searchable = [
        
    ];

    
}
