<?php

namespace Ausumsports\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;


class ConfigData extends Model
{
    use HasFactory, Notifiable, Cachable;
    protected $guarded = [];
    protected $primaryKey = "path";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $cachePrefix = "config";
    protected $cacheCooldownSeconds = 60; // 5 minutes

}
