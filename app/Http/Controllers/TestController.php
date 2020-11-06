<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Log;
class TestController extends Controller
{
    public function index(){
        $res=request()->get('echostr','');
        if($this->checkSignature() && !empty($res)){
            echo $res;
        }else{
            $a=$this->getAccesstoken();
//            dd($res);
//            $obj=$this->receiveMsg();
            $xml=file_get_contents("php://input");//获取微信公众平台创过来的信息
            file_put_contents('error_2004.txt',$xml);
//            file_put_contents("data.txt",$data); //将数据写入到某个文件
            $obj=simplexml_load_string($xml,"SimpleXMLElement",LIBXML_NOCDATA);//将一个xml格式的字
//            Log::info('error_2004.txt',$xml);
//                switch($obj->MsgType){
//                    case "event":
//                        //关注
//                        if($obj->Event=="subscribe"){
//
//                        }
//                        //取消关注
//                        if($obj->Event="unsubscribe"){
//
//                        }
//                }


        }
    }


    //接收微信公众传过来的信息
    private function receiveMsg(){
        $xml=file_get_contents("php://input");//获取微信公众平台创过来的信息
//            file_put_contents("data.txt",$data); //将数据写入到某个文件
        $obj=simplexml_load_string($xml,"SimpleXMLElement",LIBXML_NOCDATA);//将一个xml格式的字符串转化为一个对象方便使用
        return $obj;
    }

    //对接
    private function checkSignature()
    {
        $signature =request()->get("signature");
        $timestamp =request()->get ("timestamp");
        $nonce = request()->get('nonce');

        $token = "Token";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
