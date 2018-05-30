<?php

namespace app\index\model;

use think\Db;
use app\index\model\BaseModel;

class FriendsModel extends BaseModel {

  function getFriends($uid=""){

    //uid,icon,nickname

    if($uid == ""){
      return;
    }

    $list = Db::name('frined')->alias('f')->join('user u','f.fid = u.uid')
    ->field('f.fid,u.nickname,u.u_icon')->where('f.uid','=',$uid)->select();

    return $list;
  }


}





 ?>
