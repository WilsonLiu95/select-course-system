<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    protected $table = 'institute';

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
    public function major_direction()
    {

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

    }


}
