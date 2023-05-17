<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $brand_id
 * @property integer $sports_id
 * @property int $season_id
 * @property string $product_title
 * @property string $product_model
 * @property integer $year
 * @property string $product_image
 * @property string $created_at
 * @property string $updated_at
 * @property Brand $brand
 * @property Season $season
 * @property Sport $sport
 * @property PlayerCard[] $playerCards
 * @property ProductBox[] $productBoxes
 * @property Checklist[] $checklists
 */
class Product extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['brand_id', 'sports_id', 'season_id', 'product_title', 'product_model', 'year', 'product_image', 'created_at', 'updated_at'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'data';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Brand');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function season()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Season');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sport()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Sport', 'sports_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playerCards()
    {
        return $this->hasMany('Ausumsports\Admin\Models\PlayerCard');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productBoxes()
    {
        return $this->hasMany('Ausumsports\Admin\Models\ProductBox');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checklists()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Checklist');
    }
}
