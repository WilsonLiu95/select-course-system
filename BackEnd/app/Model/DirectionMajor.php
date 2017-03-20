<?php
/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 2017/2/28
 * Time: 下午8:08
 */
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
class DirectionMajor extends Model
{
    protected $table = 'direction_major';
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

}
