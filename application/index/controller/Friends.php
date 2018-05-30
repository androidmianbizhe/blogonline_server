<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model\FriendsModel;

class Friends extends Controller {
//eyJ0eXBlIjoiSldUIiwiYWxnIjoiSFMyNTYifQ==.eyJ1aWQiOjEyMzQ1Niwibmlja25hbWUiOiJcdTY1ZjZcdTk1ZjRcdTRlNGJcdTU5MTYiLCJkZWFkbGluZSI6MjExNDI1MjA4MX0=
  function getFriendList()
  {

    if(request()->isPost()){

      //获取post过来的数据
      $client_token = input('token');
      $uid = input('uid');

      $returnCode = 0;
      $list = [];
      //检测token
      $friendModel = new FriendsModel();

      $token_state = $friendModel->checkToken($uid,$client_token);

      if($token_state == 0){
        //token出错
        //重新登录
        $returnCode = 0;

      } else if($token_state == 2){
        //uid为空
        $returnCode = 2;
      } else {

        $returnCode = 1;
        //可以访问资源
        $list = $friendModel->getFriends($uid);
      }

      $friendData = [
        'returnCode' => $returnCode,
        'friendlist' => $list
      ];

      return json_encode($friendData);
    }

    //return $this->fetch('friendlist');
  }

}




 ?>
