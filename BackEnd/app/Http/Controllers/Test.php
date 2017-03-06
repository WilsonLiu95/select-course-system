<?php

namespace App\Http\Controllers;

use App\Model\Direction;
use App\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Psy\Exception\ErrorException;
use Schema;
use DB;
use Illuminate\Database\Schema\Blueprint;

class Test extends Controller
{

    public function getIndex()
    {
        $a = 2;
        return 22;
    }


}
