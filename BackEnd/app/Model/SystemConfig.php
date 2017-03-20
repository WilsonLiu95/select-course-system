<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemConfig extends Model
{
    use SoftDeletes;
    protected $table = 'system_config';
    protected $dates = ['deleted_at'];

    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
}
