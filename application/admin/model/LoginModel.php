<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use think\Session;

class LoginModel extends Model {

	public function login($username,$password){

		$admin = Db::name('admin')->where('username','=',$username)->find();

		if($admin){

			if($admin['password'] == md5($password)){
				//写入本地session
				Session::set('id',$admin['id']);
				Session::set('username',$admin['username']);
				return 1;
			} else {
				return 2;
			}
		} else {
			//用户不存在
			return 3;
		}
	}


}






?>