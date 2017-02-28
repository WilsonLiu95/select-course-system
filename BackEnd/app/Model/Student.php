<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    protected $table = 'student';

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
    public function grade()
    {
        return $this->belongsTo('App\Model\Grade');
    }
    public function schedule()
    {
        return $this->hasMany('App\Model\Schedule');
    }
    public function course()
    {
        return $this->hasMany('App\Model\Course');
    }


}
