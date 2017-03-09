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
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendReminderEmail;

class Test extends Controller
{
    public function getIndex(Request $request)
    {
        \Session::set('select_course_map',array(
            1,2,34,533
        ));
        \Session::set('detail',Model\Student::find(1)->account());
        \Session::push('select_course_map',[2233,22]);
        return session()->all();

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
