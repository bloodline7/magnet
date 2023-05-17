<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $league_id
 * @property string $team_name
 * @property string $team_region
 * @property string $team_logo
 * @property string $created_at
 * @property string $updated_at
 * @property League $league
 * @property Roster[] $rosters
 * @property Checklist[] $checklists
 */
class Team extends Model
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
    protected $fillable = ['league_id', 'team_name', 'team_region', 'team_logo', 'created_at', 'updated_at'];

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
        return $this->belongsTo('Ausumsports\Admin\Models\League', 'league_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rosters()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Roster', 'team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checklists()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Checklist', 'team_id');
    }
}
