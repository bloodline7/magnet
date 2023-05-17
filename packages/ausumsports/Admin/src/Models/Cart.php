<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ausumsports\Admin\Models\Category;
use Illuminate\Support\Facades\Log;

class Cart extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    public function getCreatedAtAttribute(){
        return \Illuminate\Support\Carbon::parse($this->attributes['created_at'])->setTimezone(getConfig('admin_timezone'));
    }
    public function getUpdatedAtAttribute(){
        return \Illuminate\Support\Carbon::parse($this->attributes['updated_at'])->setTimezone(getConfig('admin_timezone'));
    }

    public function product()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Product','product_id' , 'id' );
    }

}
