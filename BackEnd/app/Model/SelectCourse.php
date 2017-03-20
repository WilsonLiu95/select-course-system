<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SelectCourse extends Model
{
    use SoftDeletes;
    protected $table = 'select_course';
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
        return $this->belongsTo('App\Model\Student');
    }
    public function course()
    {
        return $this->belongsTo('App\Model\Course');
    }

}
