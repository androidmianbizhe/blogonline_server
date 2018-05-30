<?php

namespace app\admin\validate;

use think\Validate;

class ArticleValidate extends Validate {

	protected $rule = [
		'title' => 'require|max:100|unique:article',
		'keywords' => 'require|max:30',
	];

	protected $msg = [
		'title.require' => '标题不能为空！',
		'title.max' => '标题长度不能超过100！',
		'title.unique' => '标题不能重复！',
		'keywords.require' => '关键字不能为空！',
		'keywords.max' => '关键字长度不能超过30',

	];



}







?>