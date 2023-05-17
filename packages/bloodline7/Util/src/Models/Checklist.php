<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property integer $team_id
 * @property int $player_id
 * @property string $card_set
 * @property integer $card_number
 * @property integer $print_run
 * @property string $created_at
 * @property string $updated_at
 * @property Player $player
 * @property Product $product
 * @property Team $team
 */
class Checklist extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['product_id', 'team_id', 'player_id', 'card_set', 'card_number', 'print_run', 'created_at', 'updated_at'];

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
        return $this->belongsTo('Ausumsports\Admin\Models\Player');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Team');
    }
}
