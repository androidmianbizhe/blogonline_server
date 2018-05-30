<?php

namespace app\index\controller;

use think\Db;
use think\Controller;
use app\index\model\LoginModel;
use think\Session;


class Login extends Controller {

  public function index(){
    return 'lihao';
  }

  public function login(){

    if(request()->isPost()){

      //实例化模型
      $loginModel = new LoginModel();

      $acount = input('username');
      $passwd = input('password');

      if($acount == "" || $passwd == ""){
        return;
      }

      //验证登陆信息
      $loginState = $loginModel->login($acount,$passwd);
      //如果登陆成功
      $client_token = "";

      $userInfo = "";

      $uid = $loginModel->getUidSession();

      if($loginState == 1){

        $client_token = $this->encode_token();
        //服务器保存的token
        //
        $server_token =  base64_encode(hash_hmac('sha256', $client_token, 'lihao', true));

        //更新服务器token
        Db::name('user')->where('uid', $uid)->update(['token' => $server_token]);

      }

      $LoginInfo = [
        'returnCode'=>$loginState,
        'uid'=>$uid,
        'token'=>$client_token
      ];

      return json_encode($LoginInfo);
    }
    return $this->fetch('login');

  }

  function register(){

    if(request()->isPost()){

      //实例化model
      $loginModel = new LoginModel();

      $userInfo = $loginModel->register(input('nickname'),input('openID'),input('loginWay'),input('phone_num'));

      $client_token = $this->encode_token();
      //服务器保存的token
      //
      $server_token =  base64_encode(hash_hmac('sha256', $client_token, 'lihao', true));

      //更新服务器token
      Db::name('user')->where('uid', $userInfo)->update(['token' => $server_token]);

      $LoginInfo = [
        'returnCode'=>1,
        'uid'=>$userInfo,
        'token'=>$client_token
      ];

      return json_encode($LoginInfo);
    }
    return $this->fetch('register');
  }

  //生成token令牌
  //访问资源时 将客户端发来的token进行base64_encode(hash_hmac('sha256', $client_token, 'lihao', true)}加密
  //再与服务器进行比对
  function encode_token(){

    $uid = Session('uid');
    $nickname = Session('nickname');
    $header = [
      'type'=>'JWT',
      'alg'=>'HS256'
    ];

    $deadline = 7*24*60*60*1000 + time();

    $payload = [
      'uid'=>$uid,
      'nickname'=>$nickname,
      'deadline'=>$deadline
    ];

    $secret = 'lihao';

    $json_header = json_encode($header);//转为json字符串
    $json_payload = json_encode($payload);

    $encodedString = base64_encode($json_header).".".base64_encode($json_payload);

    return $encodedString;
  }


  function logout(){
    session(null);
  }


  function registerByCell(){

    if(request()->isPost()){

      $phone = input("cell");

      if($phone == null){

        $returnCode = 2;//空的
      }

      //判断用户是否已經注册
      $user_phone = Db::name("user")->where("phone_num",$phone)->find();

      if($user_phone){

        $returnCode = 3;//你的号码已被注册

      }else {

        $loginModel = new LoginModel();

        $content = $loginModel->sendSms($phone);//发验证短信

        //发送以后
        //返回到客户端
        $returnCode = 1;
        //验证码
      }

      //返回
      $returnInfo = [
        "returnCode"=>$returnCode
      ];

      return json_encode($returnInfo);

    }

    return $this->fetch('registerCell');
  }

  public function checkCode(){

    if(Request()->isPost()){

      $phone = input("phone");
      $code = input("code");

      //Session::set('phone_num',$phone);
      //Session::set('check_code',$code);

      if($phone == null || $code == null){
        $returnCode = 2;//空的
      }

      $loginModel = new LoginModel();

      $server_cell = Session::get("phone_num")+"";

      $server_code = Session::get("check_code")+"";

      if($server_cell == $phone && $server_code == $code){

        $returnCode = 1;//正确
      }else {

        $returnCode = 3;//验证码错误
      }

      $info = [
        "returnCode"=>$returnCode
      ];

      return json_encode($info);
    }

    return $this->fetch('checkCode');

  }

  public function settingPwd(){

    if(request()->isPost()){

       $returnCode = 1;

       $phone = input("phone");
       $code = input("password");

       if($phone == "" || $code == ""){
          $returnCode = 1;
       }

       $loginModel = new LoginModel();
       $uid = $loginModel->setPwd($phone,$code);
       if($uid == null){

          $returnCode = 1;
       }else {
         //如果登陆成功
         $client_token = "";

         $userInfo = "";

         $client_token = $this->encode_token();
         //服务器保存的token
         //
         $server_token = base64_encode(hash_hmac('sha256', $client_token, 'lihao', true));

         //更新服务器token
         Db::name('user')->where('uid', $uid)->update(['token' => $server_token]);

       }

       $LoginInfo = [
        'returnCode'=>2,
        'uid'=>$uid,
        'token'=>$client_token
       ];

       return json_encode($LoginInfo);
    }
    return $this->fetch('setpwd');
  }


  public function getAllFrom(){

    $loginModel = new LoginModel();
    $froms = $loginModel->getFrom();

    return json_encode($froms);

  }

  public function getTypeByFromId(){

    $from_id = input('from_id');
    $size = input('size');

    $loginModel = new LoginModel();
    $baseFroms = $loginModel->getTypeById($from_id,$size);

    return json_encode($baseFroms);
  }

  public function getArticleRegx(){

    $from_id = input("from_id");

    $loginModel = new LoginModel();
    $res = $loginModel->getRegsById($from_id);

    return json_encode($res);

  }

}


?>
