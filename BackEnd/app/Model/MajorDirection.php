<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 2017/2/28
 * Time: 下午8:08
 */
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MajorDirection extends Model
{
    use SoftDeletes;
    protected $table = 'major_direction';
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

    public function institute()
    {
        return $this->belongsTo('App\Model\Institute');
    }
    public function grade()
    {
        return $this->belongsTo('App\Model\Grade');
    }
}
