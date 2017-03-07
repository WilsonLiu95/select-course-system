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


class Test extends Controller
{
    public function getIndex(Request $request)
    {
        $v = $this->validate($request,[
            'title' => 'required|max:2',
        ]);

        return $this->json(1,$v);

    }
    public function getFile(){

        $test = \Artisan::call('redis:subscribe');

        return $this->json(1,$test);

    }
    public function getMail(){

        Redis::publish('test-channel', json_encode(['foo' => 'bar']));
        echo \Redis::OPT_READ_TIMEOUT;
    }

}
