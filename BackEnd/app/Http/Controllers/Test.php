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
        $data['t'] = Model\Student::where('direction_id','IN', [1])->get();
        return $this->json($data);
    }

    public function getDelete(){
        Model\Student::whereIn('grade_id',[1,2])->delete();
        Model\Classes::whereIn('grade_id',[1,2])->delete();
        Model\SelectCourse::whereIn('grade_id',[1,2])->delete();
    }
    public function getMail(){

    }

}
