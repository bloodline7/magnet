<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $sports_id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $birth_date
 * @property string $birth_place
 * @property integer $familiarity
 * @property boolean $retirements
 * @property string $affiliation_college
 * @property string $created_at
 * @property string $updated_at
 * @property Sport $sport
 * @property Roster[] $rosters
 * @property Checklist[] $checklists
 * @property PlayerCard[] $playerCards
 */
class Player extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['sports_id', 'first_name', 'last_name', 'full_name', 'birth_date', 'birth_place', 'familiarity', 'retirements', 'affiliation_college', 'created_at', 'updated_at'];

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
    public function rosters()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Roster');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checklists()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Checklist');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playerCards()
    {
        return $this->hasMany('Ausumsports\Admin\Models\PlayerCard');
    }
}
