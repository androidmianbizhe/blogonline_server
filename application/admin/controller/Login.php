<?php

namespace app\admin\controller;

use think\Controller;
use think\Loader;
use app\admin\model\LoginModel;

class Login extends Controller {

	public function index(){

		if(request()->isPost()){
			//实例化模型
			$loginModel = new LoginModel;
			
			//验证登录信息
			$loginState = $loginModel->login(input('username'),input('password'));

			if($loginState == 1){
				//登录成功
				return $this->success('登录成功，正在跳转..','Index/index');
			} elseif ($loginState == 2) {
				//密码错误
				return $this->error('账号或密码错误！');
			} else {
				//用户不存在
				return $this->error('用户名不存在!');
			}
		}
		return $this->fetch('login');
	}

	public function logout(){

		session(null);
		return $this->success('退出成功!','Login/index');
	}
}






?>