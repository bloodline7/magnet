<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $sports_id
 * @property string $title
 * @property string $code
 * @property string $region
 * @property string $logo
 * @property integer $popularity
 * @property string $created_at
 * @property string $updated_at
 * @property Sport $sport
 * @property Season[] $seasons
 * @property Team[] $teams
 */
class League extends Model
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
    protected $fillable = ['sports_id', 'title', 'code', 'region', 'logo', 'popularity', 'default',  'created_at', 'updated_at'];

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'data';

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
    public function seasons()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Season', 'league_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Team');
    }
}
