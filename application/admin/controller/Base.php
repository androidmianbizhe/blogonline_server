<?php

namespace app\admin\controller;

use think\Controller;
use think\Session;

class Base extends Controller {

	public function _initialize()
    {
        if(Session('id')){

        } else {

        	return $this->error('请先登录系统!',url('Login/index'));
        }
    }


}






?>