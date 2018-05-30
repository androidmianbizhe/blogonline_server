<?php

namespace app\index\controller;

use think\Controller;
use think\Session;

public class Base extends Controller {

	public function _initialize(){

		if(Session('id')){
			//已登录
		}else {

		}
	}

	


}



?>
