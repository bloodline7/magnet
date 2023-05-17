<?php

namespace Ausumsports\Admin\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class EbayProducts extends Model
{

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';
    protected $collection = 'ebay_products';
    protected $guarded = [];

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';


}
