<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'major';

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
    protected $touches = ['institute'];


    public function direction(){
        return $this->belongsToMany('App\Model\Direction')
            ->withPivot('institute_id')
            ->withTimestamps();
    }
//    protected $guarded = ['created_at','updated_at'];

    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function student()
    {
        return $this->hasMany('App\Model\Student');
    }
}
