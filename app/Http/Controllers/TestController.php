<?php

namespace App\Http\Controllers;

use App\Models\User;
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
//
            $xml=file_get_contents("php://input");//获取微信公众平台传过来的信息
               $obj=simplexml_load_string($xml,"SimpleXMLElement",LIBXML_NOCDATA);//将一个xml格式的对象
                switch($obj->MsgType){
                    case "event":
                        //关注
                        if($obj->Event=="subscribe"){
                            $openid=$obj->FromUserName;   //获取用户的openid
                            $AccessToken=$this->getAccesstoken();   //获取token
                            $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$AccessToken."&openid=".$openid."&lang=zh_CN";
//                            dd($url);
                            $user=file_get_contents($url);    //获取第三方 的数据
                            $user=json_decode($user,true);
                            //查到了
//                                if(!Redis::get($openid)){
//                                    Redis::set($openid,'gggt');
//                                    $content="谢谢你关注";
//                                    echo   $this->text($obj,$content);
//                                }else{
//                                    $content="谢谢你们再次关注,我们加倍努力的";
//                                    echo   $this->text($obj,$content);
//                                }
                            if(isset($user['errcode'])){
                                $this->writeLog("获取用户信息失败了");

                            }else{
                                $user_id=User::where('openid',$openid)->first();   //查询一条
                                if($user_id){
                                    $user_id->subscribe=1;   //查看这个用户的状态  1关注   0未关注
                                    $user_id->save();
                                    $content="谢谢你们再次关注,我们加倍努力的";
//                                    echo $this->text($obj,$content);
                                }else{
//                                    $res=[
//                                        "subscribe"=>$user_id["subscribe"],
//                                        "openid"=>$user_id["openid"],
//                                        "nickname"=>$user_id["nickname"],
//                                        "sex"=>$user_id["sex"],
//                                        "city"=>$user_id["city"],
//                                        "country"=>$user_id["country"],
//                                        "province"=>$user_id["province"],
//                                        "language"=>$user_id["language"],
//                                        "headimgurl"=>$user_id["headimgurl"],
//                                        "subscribe_time"=>$user_id["subscribe_time"],
//                                        "subscribe_scene"=>$user_id["subscribe_scene"]
//                                    ];
                                    User::insert($user);
                                    $content="官人，谢谢关注！";
//                                    echo $this->text($obj,$content);

                                }
                            }

                        }
                        //取消关注
                        if($obj->Event=="unsubscribe"){
//                            $content="取消关注成功,期待你下次关注";
//                            $openid=$obj->FromUserName;
//                            $user_id=User::where('user_id',$openid)->first();
                            $user_id->subscribe=0;
                            $user_id->save();
                        }
                        echo   $this->text($obj,$content);
                        break;
                }

        }
    }

    //判断类型
    private function writeLog($data){
        if(is_object($data) || is_array($data)){   //不管是数据和对象都转json 格式
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
        echo sprintf($xml,$ToUserName,$FromUserName,$CreateTime,$MsgType,$content);
    }
}
