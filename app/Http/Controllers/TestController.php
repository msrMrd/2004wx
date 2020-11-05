<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    public function index(){
    $res=Test::get();
        dump($res);

//        $aa='aa';
//        $bb='ccc';
//        Redis::set($aa,$bb);
//        $aa=Redis::get($aa);
//        dd($aa);
    }
}
