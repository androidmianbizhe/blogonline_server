<?php

namespace app\admin\validate;

use think\Validate;

class LinkValidate extends Validate {

	protected $rule = [
		'title' => 'require|max:10|unique:link',
		'url' => 'require',
	];

	protected $message = [
		'title.require' => '标题不能为空!',
		'title.max' => '标题不能超过30字!',
		'title.unique' => '标题不能重复!',
		'url.require' => '链接不能为空!',
	];

}

?>