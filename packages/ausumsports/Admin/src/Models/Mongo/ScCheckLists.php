<?php

namespace Ausumsports\Admin\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class ScCheckLists extends Model
{

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    protected $collection = 'checklists';
    protected $guarded = [];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';


}
