<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'major';
    protected $dates = ['deleted_at'];
    protected $hidden = ['pivot'];
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['name','institute_id','major_code'];
    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */

    public function direction(){
        return $this->belongsToMany('App\Model\Direction')
            ->withTimestamps();
    }

    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function student()
    {
        return $this->hasMany('App\Model\Student');
    }
}
