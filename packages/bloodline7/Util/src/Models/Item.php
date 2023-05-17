<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $ebay_item_no
 * @property string $item_title
 * @property string $item_images
 * @property string $item_type
 * @property mixed $item_info
 * @property float $shipping_price
 * @property float $sold_price
 * @property string $ended_at
 * @property string $created_at
 * @property string $updated_at
 * @property SingleCard $singleCard
 */
class Item extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['ebay_item_no', 'item_title', 'item_images', 'item_type', 'item_info', 'shipping_price', 'sold_price', 'ended_at', 'created_at', 'updated_at'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'data';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function singleCard()
    {
        return $this->hasOne('Ausumsports\Admin\Models\SingleCard', 'id');
    }
}
