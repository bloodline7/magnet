<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $league_id
 * @property string $season_title
 * @property string $season_alias
 * @property integer $season_from
 * @property integer $season_to
 * @property string $season_status
 * @property string $created_at
 * @property string $updated_at
 * @property League $league
 * @property Product[] $products
 */
class Season extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['league_id', 'season_title', 'season_alias', 'season_from', 'season_to', 'season_status', 'created_at', 'updated_at'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'data';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function league()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\League');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Product');
    }
}
