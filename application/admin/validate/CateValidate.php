<?php

namespace app\admin\validate;

use think\Validate;

//验证器
//表名
class CateValidate extends Validate {

	//验证规则
	protected $rule = [
		'catename' => 'require|max:30|unique:cate',
		'keywords' => 'require|max:25',
	];

	//验证信息
	protected $msg = [
		'catename.require' => '栏目名称不能为空!',
		'catename.max' => '栏目名称长度不能超过30',
		'catename.unique' => '栏目名称不可重复',
		'keywords.require' => '关键词不能为空',
		'keywords.max' => '关键词不能超过25',
	];


}







?>