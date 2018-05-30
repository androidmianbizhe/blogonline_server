<?php

namespace app\admin\validate;

use think\Validate;

class AdminValidate extends Validate {

	protected $rule = [
		'username' => 'require|min:5|max:15|unique:admin',
		'password' => 'require|min:5',
	];

	protected $message = [
		'username.require' => '账户不能为空！',
		'username.unique' => '账户已存在!',
		'username.min' => '账户长度不能小于5位！',
		'username.max' => '账户长度不能大于15位！',
		'password.require' => '密码不能为空!',
		'password.min' => '密码长度不能小于5位！',

	];


}







?>