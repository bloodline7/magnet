<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $manufacturer_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property Manufacturer $manufacturer
 * @property Product[] $products
 */
class Brand extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['manufacturer_id', 'name', 'created_at', 'updated_at'];

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'data';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacturer()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Manufacturer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Product');
    }
}
