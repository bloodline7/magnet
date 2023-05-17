<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;

class ProductImage extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];
    //    public $timestamps = false;
    //protected $table = 'product_category';

    public function products()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\Product', 'id', 'product_id');
    }

}
