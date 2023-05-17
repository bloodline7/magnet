<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class CodeGroup extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $guarded = [];

    public function code()
    {
        return $this->hasMany('Ausumsports\Admin\Models\Code','group_id' , 'id');
    }

}
