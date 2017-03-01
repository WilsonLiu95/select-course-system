<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Direction extends Model
{
    use SoftDeletes;
    protected $table = 'direction';
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

    // 关联
    public function major(){
        return $this->belongsToMany('App\Model\Major','major_direction')
            ->withPivot('institute_id')
            ->withTimestamps();;
    }
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }

    public function course()
    {
        return $this->hasMany('App\Model\Course');
    }

}
