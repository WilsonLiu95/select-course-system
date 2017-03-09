<?php

namespace App\Http\Controllers;

use App\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
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

        return $this->json(1, $key = 'major_course_' . 2 . "_" . 2);


    }
    public function getFile(){
        $test = \Artisan::call('redis:subscribe');
        return $this->json(1,$test);

    }
    public function getMail(){
        $id = request()->id;
        $user = Model\Student::find($id);
        $job = (new SendReminderEmail($user));
        $this->dispatch($job);
    }

}
