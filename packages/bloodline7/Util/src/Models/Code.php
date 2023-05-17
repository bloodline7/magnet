<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;





class Code extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $guarded = [];

    public function getCreatedAtAttribute(){
        return \Illuminate\Support\Carbon::parse($this->attributes['created_at'])->setTimezone(getConfig('admin_timezone'));
    }
    public function getUpdatedAtAttribute(){
        return \Illuminate\Support\Carbon::parse($this->attributes['updated_at'])->setTimezone(getConfig('admin_timezone'));
    }


    public function group()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\CodeGroup', 'group_id');
    }
}
