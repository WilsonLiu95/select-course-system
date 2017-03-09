<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Student extends Model
{
    use SoftDeletes;
    protected $table = 'student';
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

    public function account(){
        $orign = $this->toArray();
        $orign['institute'] = $this->institute()->value("name");
        $orign['major'] = $this->major()->value('name');
        $orign['direction'] = $this->direction()->value('name');
        $orign['classes'] = $this->classes()->value('name');
        $orign['grade'] = $this->grade()->value('name');
        return array_except($orign,['id','institute_id','major_id','major_code','direction_id','classes_id','classes_code','grade_id']);


    }
    // 关联
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function major()
    {
        return $this->belongsTo('App\Model\Major');
    }
    public function direction()
    {
        return $this->belongsTo('App\Model\Direction');
    }
    public function grade()
    {
        return $this->belongsTo('App\Model\Grade');
    }
    public function classes()
    {
        return $this->belongsTo('App\Model\Classes');
    }



    public function selectCourse()
    {
        return $this->hasMany('App\Model\SelectCourse');
    }



}
