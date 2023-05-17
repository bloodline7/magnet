<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $player_id
 * @property int $checklist_id
 * @property string $grading_com
 * @property float $grade
 * @property mixed $card_info
 * @property string $created_at
 * @property string $updated_at
 * @property Player $player
 * @property Checklist $checklist
 * @property Item $item
 */
class SingleCard extends Model
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
    protected $fillable = ['player_id', 'checklist_id', 'grading_com', 'grade', 'card_info', 'created_at', 'updated_at'];

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
    public function checklist()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Checklist');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Item', 'id');
    }
}
