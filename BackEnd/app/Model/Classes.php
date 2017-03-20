<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Classes extends Model
{
    use SoftDeletes;
    protected $table = 'classes';
    protected $dates = ['deleted_at'];
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['created_at','updated_at'];
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function major()
    {
        return $this->belongsTo('App\Model\Major');
    }
    public function grade()
    {
        return $this->belongsTo('App\Model\Grade');
    }
}
