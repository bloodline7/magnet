<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $team_id
 * @property int $player_id
 * @property string $position
 * @property mixed $stats
 * @property string $created_at
 * @property string $updated_at
 * @property Player $player
 * @property Team $team
 */
class Roster extends Model
{
    /**
     * @var array
     */
    protected $fillable = [ 'team_id', 'player_id', 'position', 'stats', 'created_at', 'updated_at'];

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'data';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Player','player_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Team', 'team_id');
    }
}
