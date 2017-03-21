<?php

namespace App\Http\Controllers;

use App\Model;
use Flexihash\Flexihash;
use GuzzleHttp\Cookie\SessionCookieJar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Psy\Util\Json;
use Validator;
use App\Http\Requests;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Wechat\BaseTrait;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendReminderEmail;

class Test extends Controller
{
    use BaseTrait;
    public function getIndex()
    {
        Model\Student::where('institute_id', 1)
            ->whereIn('grade_id',[1,2])->delete();
        Model\Grade::where('institute_id', 1)
            ->whereIn('id',[1,2])->delete();
        Model\Classes::where('institute_id', 1)
            ->whereIn('grade_id',[1,2])->delete();
        Model\SelectCourse::where('institute_id', 1)
            ->whereIn('grade_id',[1,2])->delete();
    }

    public function getFile(){
        Cache::put('test',['a'=>2,"b"=>2],10);
    }
    public function getMail(){
        $id = request()->id;
        $user = Model\Student::find($id);
        $job = (new SendReminderEmail($user));
        $this->dispatch($job);
    }

}
