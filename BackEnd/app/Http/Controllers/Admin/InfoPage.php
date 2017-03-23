<?php

namespace App\Http\Controllers\Admin;

use App\Model\Direction;
use App\Model\Institute;
use App\Model\Major;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CacheHandle;
class InfoPage extends Controller
{
    use CacheHandle;
    private $institute_id;
    public function __construct()
    {
        parent::__construct();
        // 操作账户信息较多,默认生成
        $this->institute_id = session()->get('institute_id');
    }
    public function getIndex(){

        $data['major'] = Institute::find($this->institute_id)->major()->get();
        $data['direction'] = Institute::find($this->institute_id)->direction()->with(['major'=>function($query){
            $query->select('major.id','major.name');
        }])
            ->select('name','id')
            ->get();
        $data['config'] = Institute::find($this->institute_id)->systemConfig()
            ->select('is_common_open','min_common_credit', 'max_common_credit',
                'is_direction_open','min_direction_credit','max_direction_credit')
            ->first();
        return $this->json($data);
    }

//    ======================= 专业major相关操作====================================
    public function postMajorUpdate(){
        $this->validate(request(),[
            'id'=>'required|integer',
            'name'=>'required'
        ]);

        if(request()->id){ // id不为0表示新增专业
            $isSuccess = Major::find(request()->id)->update([
                'name'=>request()->name,
            ]);
            $this->cacheFlush($this->institute_id);
            return $this->json([
                'isSuccess' =>$isSuccess,
                'msg'=>'更新成功'
            ]);
        }else{
            $isSuccess = Major::create([
                'name'=>request()->name,
                'institute_id'=>$this->institute_id
            ]);
            $this->cacheFlush($this->institute_id);
            return $this->json([
                'isSuccess' =>$isSuccess,
                'msg'=>'新增成功'
            ]);
        }
    }
    public function getMajorDelete(){
        $this->validate(request(),[
            'id'=>'required|integer',
        ]);
        $data['isSuccess']=Major::find(request()->id)
            ->delete();
        $this->cacheFlush($this->institute_id);
        $data['msg']='删除成功';
        return $this->json($data);
    }

//    ======================= 方向direction相关操作====================================
    public function postDirectionUpdate(){
        $this->validate(request(),[
            'id'=>'required|integer',
            'major'=>'required|array',
            'name'=>'required'
        ]);
        if(request()->id){ // id不为0表示更新专业方向
            $isSuccess = Direction::find(request()->id)->update([
                'name'=>request()->name,
            ]);
            $major_list = collect(request()->major);
            $origin_list = Direction::find(request()->id)->major()->lists('major.id');
            $attach = $major_list->diff($origin_list)->all();
            $detach = $origin_list->diff($major_list)->all();
            Direction::find(request()->id)->major()->attach($attach);
            if($detach){
                Direction::find(request()->id)->major()->detach($detach);
            }
            $this->cacheFlush($this->institute_id);
            return $this->json([
                'isSuccess' =>$isSuccess,
                'msg'=>'更新成功'
            ]);
        }else{
            $dir = Direction::create([
                'name'=>request()->name,
                'institute_id'=>$this->institute_id
            ]);
            $dir->major()->attach(request()->major);
            $this->cacheFlush($this->institute_id);
            return $this->json([
                'msg'=>'新增成功'
            ]);

        }
    }
    public function getDirectionDelete(){
        $this->validate(request(),[
            'id'=>'required|integer',
        ]);
        Direction::find(request()->id)->major()->detach(); // 清除所有的关联
        $data['isSuccess'] = Direction::find(request()->id)
            ->delete();
        $this->cacheFlush($this->institute_id);
        $data['msg'] = '删除成功';
        return $this->json($data);
    }

}
