<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    protected $table = 'institute';
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



    public function grade()
    {
        return $this->hasMany('App\Model\Grade');
    }
    public function systemConfig()
    {
        return $this->hasMany('App\Model\SystemConfig');
    }
    public function major()
    {
        return $this->hasMany('App\Model\Major');
    }
    public function direction()
    {
        return $this->hasMany('App\Model\Direction');
    }

    public function student()
    {
        return $this->hasMany('App\Model\Student');
    }
    public function classes()
    {
        return $this->hasMany('App\Model\Classes');
    }
    public function course()
    {
        return $this->hasMany('App\Model\Course');
    }



}
