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
//            dd($a);die;
//            $obj=$this->receiveMsg();
            $xml=file_get_contents("php://input");//获取微信公众平台传过来的信息
//            file_put_contents("data.txt",$xml); //将数据写入到某个文件
            $obj=simplexml_load_string($xml,"SimpleXMLElement",LIBXML_NOCDATA);//将一个xml格式的字
                switch($obj->MsgType){
                    case "event":
                        //关注
                        if($obj->Event=="subscribe"){
                            $openid=$obj->FromUserName;
                            $AccessToken=$this->getAccesstoken();
                            $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$AccessToken."&openid=".$openid."&lang=zh_CN";
                            $user=file_get_contents($url,true);    //获取第三方 的数据
                            if(isset($user['errcode'])){
                                $this->writeLog("获取用户失败");
                            }else{
                                //查到了
                                $content="谢谢，你关注";
                            }
                        }
                        //取消关注
                        if($obj->Event="unsubscribe"){
//                            $content="取消关注成功,期待你下次关注";
                        }
                }


        }
    }

    //判断类型
    private function writeLog($data){
        if(is_object($data) || is_array($data)){
            $data=json_encode($data);
        }
        file_put_contents('2004.txt',$data);die;
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
    function text($obj,$content){
        $ToUserName=$obj->FromUserName;
        $FromUserName=$obj->ToUserName;
        $CreateTime=time();
        $MsgType="text";
        $xml="<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[%s]]></MsgType>
              <Content><![CDATA[%s]]></Content>
            </xml>";
    }
}
