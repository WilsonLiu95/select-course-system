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
    public function postEdit(){
        $this->validate(request(),[
            'is_common_open'=> 'required|bool',
            'is_direction_open'=> 'required|bool',
            'min_common_credit'=> 'required|integer',
            'max_common_credit'=> 'required|integer',
            'min_direction_credit'=> 'required|integer',
            'max_direction_credit'=> 'required|integer',
        ]);
        if(request()->min_common_credit > request()->max_common_credit){
            return $this->errorMsg('公选课最低学分不能高于最高学分');
        }
        if(request()->min_direction_credit > request()->min_direction_credit){
            return $this->errorMsg('专业方向选修课的最低学分不能高于最高学分');
        }
        $newConfig = request()->only('is_common_open','min_common_credit', 'max_common_credit',
            'is_direction_open','min_direction_credit','max_direction_credit');
        $updateResult = Institute::find($this->institute_id)->systemConfig()->update($newConfig);

        if($updateResult){
            $this->cacheSystemConfig($this->institute_id, true);
            $this->cacheFlush($this->institute_id);
            return $this->json(['msg'=>'更新成功']);
        }else{
            return $this->errorMsg('更新失败,请重试');
        }
    }
//    ======================= 专业major相关操作====================================
    public function postMajorUpdate(){
        $this->validate(request(),[
            'id'=>'required|integer',
            'major_code'=>'required|integer',
            'name'=>'required'
        ]);

        if(request()->id){ // id不为0表示新增专业
            $isExists = Major::where('institute_id',$this->institute_id)->where('major_code',request()->major_code)
                ->where('id','!=', request()->id)->exists();
            if($isExists){ // 2个代表
                return $this->errorMsg('专业代码已存在'.request()->major_code .',请换一个专业代码');
            }
            $isSuccess = Major::find(request()->id)->update([
                'name'=>request()->name,
                'major_code'=>request()->major_code
            ]);
            $this->cacheFlush($this->institute_id);
            return $this->json([
                'isSuccess' =>$isSuccess,
                'msg'=>'更新成功'
            ]);
        }else{
            $isExists = Major::where('institute_id',$this->institute_id)
                ->where('major_code',request()->major_code)->exists();
            if($isExists){
                return $this->errorMsg('专业代码已存在'.request()->major_code .',请换一个专业代码');
            }

            $isSuccess = Major::create([
                'name'=>request()->name,
                'institute_id'=>$this->institute_id,
                'major_code'=>request()->major_code
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
