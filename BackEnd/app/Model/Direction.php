<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    protected $table = 'direction';

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

    // 关联

    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }

    public function course()
    {
        return $this->hasMany('App\Model\Course');
    }

}
