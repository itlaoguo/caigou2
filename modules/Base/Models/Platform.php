<?php

declare(strict_types=1);

namespace Modules\Base\Models;

use Catch\Base\CatchModel as Model;
use Modules\Customer\Models\Customer;

/**
 * @property $id
 * @property $name
 * @property $url
 * @property $note
 * @property $creator_id
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
*/
class Platform extends Model
{


    protected $table = 'platform';

    protected $fillable = [ 'id', 'name', 'url', 'note', 'creator_id', 'created_at', 'updated_at', 'deleted_at' ];

    /**
     * @var array
     */
    protected array $fields = ['id','name','code','url','note','created_at','updated_at'];

    /**
     * @var array
     */
    protected array $form = ['name','url','note'];

    /**
     * @var array
     */
    public array $searchable = [

    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }


}
