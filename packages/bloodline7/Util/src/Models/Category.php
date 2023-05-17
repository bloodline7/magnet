<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;





class Category extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];
    protected $table = 'category';

    public function getCreatedAtAttribute(){
        return \Illuminate\Support\Carbon::parse($this->attributes['created_at'])->setTimezone(getConfig('admin_timezone'));
    }
    public function getUpdatedAtAttribute(){
        return \Illuminate\Support\Carbon::parse($this->attributes['updated_at'])->setTimezone(getConfig('admin_timezone'));
    }


    function getCategoryAttribute()
    {
        $result = $this->category()->get();

        $return = '';
        foreach ($result as $item)
        {
            $return = ($return) ? $return . ", " . $item->code->title : $item->code->title;
        }

        return $return;
    }

    function getCodeAttribute(): ?array
    {

        $code = $this->code();

        if(sizeof($code)) return $code;

        return null;

    }

    function code(): array
    {
        $result = $this->category()->get();
        $return = [];
        foreach ($result as $item)
        {
            array_push($return, $item->category_code);
        }

        return $return;
    }


    public function exhibitor()
    {
        $convention = $this->convention();
        return $convention->exhibitor()->first();
    }

    public function convention()
    {
        return $this->belongsTo('Ausumsports\Admin\Models\ConventionExhibitor','convention_exhibitor_id' , 'id' )->first();
    }

    public function category()
    {
        return $this->hasMany('Ausumsports\Admin\Models\ProductCategoryCode', 'product_id', 'id');
    }
}
