<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setUseAttribute($value)
    {
        $this->attributes['use'] = ($value) ? 1 : 0;
    }


    public function content(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('Ausumsports\Admin\Models\RouterContents', 'id')->withDefault([
            'use' => 1,
            'content' => ''
        ]);
    }

}
