<?php

namespace Ausumsports\Admin\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class EbayItems extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';
    protected $collection = 'ebayItems';
    protected $guarded = [];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


}
