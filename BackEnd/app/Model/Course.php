<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Course extends Model
{
    use SoftDeletes;
    protected $table = 'course';
    protected $dates = ['deleted_at'];
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [];
    protected $hidden = ['pivot'];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['created_at','updated_at'];
//    关联关系

    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function direction()
    {
        return $this->belongsToMany('App\Model\Direction')
            ->withTimestamps();
    }
    public function selectCourse()
    {
        return $this->hasMany('App\Model\SelectCourse');
    }

}
