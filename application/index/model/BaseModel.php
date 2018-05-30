<?php

namespace app\index\model;

use think\DB;
use think\Model;

class BaseModel extends Model {


  public function checkToken($uid=-1,$client_token=""){

    if($uid == -1 || $client_token == ""){
      return 0;//空
    }
    
    $server_token = Db::name('user')->field('token')->where('uid','=',$uid)->find();

    if($server_token){

      //与客户端进行对比
      $client_token_dealed =  base64_encode(hash_hmac('sha256', $client_token, 'lihao', true));

      if($client_token_dealed == $server_token['token']){
        return 1;//正确
      }else {
        return 2;//错误
      }
    }

  }




}







 ?>
