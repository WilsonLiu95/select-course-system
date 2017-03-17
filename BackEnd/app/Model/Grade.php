<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Grade extends Model
{
    use SoftDeletes;
    protected $table = 'grade';
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
    public function student()
    {
        return $this->hasMany('App\Model\Student');
    }
    public function selectCourse()
    {
        return $this->hasMany('App\Model\SelectCourse');
    }
    public function systemConfig()
    {
        return $this->hasMany('App\Model\SystemConfig');
    }
    public function course()
    {
        return $this->hasMany('App\Model\Course');
    }
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
}
