<?php

namespace app\index\controller;

use think\Controller;
use app\index\model\UserModel;

class User extends Controller {

  //获取详情信息
  function getDetailInfoOfUser(){

    $uid = input('uid');
    $requestUid = input('requestUid');//要访问用户资料的用户id
    $token = input('token');


    $returnCode = 0;
    $data = null;
    $userInfo = [];

    $userModel = new UserModel();

    $token_state = $userModel->checkToken($uid,$token);

    //检测requestUid
    if($requestUid == ""){
      $token_state = 0;
    }

    if($token_state == 1){
      $returnCode = 1;
      $data = $userModel->getDetailUserInfo($requestUid);
    } else if($token_state == 0){
      $returnCode = 0;
    } else if ($token_state == 2) {
      $returnCode = 2;
    }

    $userInfo = [
      'returnCode'=>$returnCode,
      'user'=>$data
    ];

    return json_encode($userInfo);

  }

  //获取基本信息
  function getSimpleInfoOfUser(){

    $uid = input('uid');
    $token = input('token');

    $returnCode = 0;
    $data = null;
    $userInfo = [];

    $userModel = new UserModel();

    $token_state = $userModel->checkToken($uid,$token);

    if($token_state == 1){
      $returnCode = 1;
      $data = $userModel->getUserSimpleInfo($uid);
    } else if($token_state == 0){
      $returnCode = 0;
    } else if ($token_state == 2) {
      $returnCode = 2;
    }

    $userInfo = [
      'returnCode'=>$returnCode,
      'user'=>$data
    ];

    return json_encode($userInfo);

  }

  //更新用户资料
  function updateUserIcon(){

    if(request()->isPost()){

      $returnCode = 0;
      //获取信息
      $uid = input('uid');
      $token = input('token');

      if($uid == "" || $token == ""){
        $returnCode = 0;
      }else {

        $userModel = new UserModel();
        //判断token
        $token_state = $userModel->checkToken($uid,$token);

        if($token_state == 1){
          //正确
          $returnCode = 1;

          //上传图片
          //接受上传的文件
          //接受上传的文件
    			if($_FILES['pic']['tmp_name']){
    				// 获取表单上传文件 例如上传了001.jpg
    			    $file = request()->file('pic');

    			    // 移动到框架应用根目录/public/uploads/ 目录下
    			    $info = $file->move(ROOT_PATH . 'public' . DS . 'static/uploads');

    			    if($info){
    			        // 成功上传后 获取上传信息
    			        $path = 'public/static/uploads/'.date('Ymd').'/'.$info->getFilename();

                  //更改数据库图片路径
                  $userModel->updateIconOfUser($uid,$path);

                  $returnCode = 3;
    			    }else{
    			        // 上传失败获取错误信息
    			        $returnCode = 4;
    			    }
    			}

        } else if($token_state == 0){
          //空的
          $returnCode = 0;
        } else {
          //重新登录
          $returnCode = 2;
        }

      }
      $info = [
        'returnCode'=>$returnCode
      ];
      return json_encode($info);
    }

    //return $this->fetch('uploads');
  }

  //更新昵称
  public function updateUserNickname(){

      $uid = input('uid');
      $token = input('token');
      $nickname = input('nickname');

      $returnCode = 0;//0 重复 1 成功 2 空的 3重新登陆

      if($uid == ""||$token == ""||$nickname == ""){
        $returnCode = 2;//为空
      } else {
        $userModel = new UserModel();
        $token_state = $userModel->checkToken($uid,$token);
        if($token_state == 1){
          $returnCode = $userModel->updateNicknameOfUser($uid,$nickname);
        }else if ($token_state == 0) {
          $returnCode = 2;//空的
        }else {
          $returnCode = 3;//重新登陆
        }
      }

      $info = [
        'returnCode'=>$returnCode
      ];
      return json_encode($info);
  }

  //更新性别
  public function updateUserSex(){

    $uid = input('uid');
    $token = input('token');
    $sex = input('sex');

    $returnCode = 0;//0 重复 1 成功 2 空的 3重新登陆

    if($uid == ""||$token == ""||$sex == ""){
      $returnCode = 2;//为空
    } else {
      $userModel = new UserModel();
      $token_state = $userModel->checkToken($uid,$token);
      if($token_state == 1){
        $returnCode = $userModel->updateSexOfUser($uid,$sex);
      }else if ($token_state == 0) {
        $returnCode = 2;//空的
      }else {
        $returnCode = 3;//重新登陆
      }
    }

    $info = [
      'returnCode'=>$returnCode
    ];
    return json_encode($info);
  }

  //更新生日
  public function updateUserBirth(){
    $uid = input('uid');
    $token = input('token');
    $birth = input('birth');

    $returnCode = 0;//0 重复 1 成功 2 空的 3重新登陆

    if($uid == ""||$token == ""||$birth == ""){
      $returnCode = 2;//为空
    } else {
      $userModel = new UserModel();
      $token_state = $userModel->checkToken($uid,$token);
      if($token_state == 1){
        $returnCode = $userModel->updateBirthOfUser($uid,$birth);
      }else if ($token_state == 0) {
        $returnCode = 2;//空的
      }else {
        $returnCode = 3;//重新登陆
      }
    }

    $info = [
      'returnCode'=>$returnCode
    ];
    return json_encode($info);
  }

  //添加签名
  public function addUserTag(){
    $uid = input('uid');
    $token = input('token');
    $tag = input('tag');

    $returnCode = 0;//0 重复 1 成功 2 空的 3重新登陆

    if($uid == ""||$token == ""||$tag == ""){
      $returnCode = 2;//为空
    } else {
      $userModel = new UserModel();
      $token_state = $userModel->checkToken($uid,$token);
      if($token_state == 1){
        $returnCode = $userModel->addTagOfUser(['uid'=>$uid,'publish_time'=>date("Y-m-d H:i:s"),'content'=>$tag,'face'=>1]);
      }else if ($token_state == 0) {
        $returnCode = 2;//空的
      }else {
        $returnCode = 3;//重新登陆
      }
    }

    $info = [
      'returnCode'=>$returnCode
    ];
    return json_encode($info);
  }

  public function uploadBlog(){

    if(request()->isPost()){

      $uid = input('uid');
      $token = input('token');
      $blog_json = input('blog_json');

      if($uid == ""||$token == ""||$blog_json == ""){
        $returnCode = 2;//为空
      }else {

        $userModel = new UserModel();
        $token_state = $userModel->checkToken($uid,$token);

        if($token_state == 1){
          $returnCode = $userModel->uploadBlogOfUser($blog_json);
        }else if ($token_state == 0) {
          $returnCode = 2;//空的
        }else {
          $returnCode = 3;//重新登陆
        }
      }

      $info = [
        'returnCode'=>$returnCode
      ];
      return json_encode($info);
    }

    return $this->fetch("uploadsblog");
  }

  //上传用户偏好
  public function uploadPrefer(){

    if(request()->isPost()){

      $uid = input('uid');
      $token = input('token');
      $prefer_json = input('prefer_json');

      if($uid == ""||$token == ""||$prefer_json == ""){

        $returnCode = 2;//为空
      }else {

        $userModel = new UserModel();
        $token_state = $userModel->checkToken($uid,$token);

        if($token_state == 1){
          $returnCode = $userModel->uploadPreferOfUser($prefer_json);
        }else if ($token_state == 0) {
          $returnCode = 2;//空的
        }else {
          $returnCode = 3;//重新登陆
        }
      }

      $info = [
        'returnCode'=>$returnCode
      ];
      return json_encode($info);
    }

    return $this->fetch("uploadprefer");
  }


  public function getBlog(){

    $uid = input('uid');
    $token = input('token');
    $page = input('page');
    $from_id = input('from_id');

    if($uid == ""||$token == ""){
      $returnCode = 2;//为空
    }else {

      $userModel = new UserModel();

      //$token_state = 1;
      $token_state = $userModel->checkToken($uid,$token);

      if($token_state == 1){

        $returnCode = $userModel->getBlogOfUser($page,$uid,$from_id);
        if($returnCode != null){
          return json_encode($returnCode,true);
        }

      }else if ($token_state == 0) {
        $returnCode = 2;//空的
      }else {
        $returnCode = 3;//重新登陆
      }
    }
  }

}


?>
