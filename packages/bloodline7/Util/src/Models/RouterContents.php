<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouterContents extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function setUseAttribute($value)
    {
        $this->attributes['use'] = ($value) ? 1 : 0;
    }

    public function getContentAttribute($value)
    {
        return ($value) ? $value : '';
    }


    public function router()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Register','id');

    }
}
