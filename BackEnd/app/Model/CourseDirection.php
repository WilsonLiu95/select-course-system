<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CourseDirection extends Model
{
    use SoftDeletes;
    protected $table = 'course_direction';
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

    public function course()
    {
        return $this->belongsTo('App\Model\Course');
    }
    public function direction()
    {
        return $this->belongsTo('App\Model\Direction');
    }
}
