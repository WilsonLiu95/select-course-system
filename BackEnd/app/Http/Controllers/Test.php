<?php

namespace App\Http\Controllers;

use App\Model\Direction;
use App\Model\Major;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Schema;
use DB;
use Illuminate\Database\Schema\Blueprint;

class Test extends Controller
{

    public function getIndex()
    {
        return rand(1,3);
    }


}
