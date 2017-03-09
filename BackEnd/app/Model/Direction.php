<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Direction extends Model
{
    use SoftDeletes;
    protected $hidden = ['pivot'];
    protected $table = 'direction';
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

    // 关联
    public function major(){
        return $this->belongsToMany('App\Model\Major')
            ->withTimestamps();
    }
    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }

    public function course()
    {
        return $this->belongsToMany('App\Model\Course')
            ->withPivot('institute_id')
            ->withTimestamps();
    }

}
